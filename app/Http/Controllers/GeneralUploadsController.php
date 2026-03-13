<?php

namespace App\Http\Controllers;

use App\Models\GeneralUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Sentinel;

class GeneralUploadsController extends Controller
{
    /**
     * S3 Configuration
     */
    private $s3Client;
    private $bucket = 'wfssystem';
    
    public function __construct()
    {
        $this->s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'nyc3',
            'endpoint' => 'https://nyc3.digitaloceanspaces.com',
            'credentials' => [
                'key' => 'DO00RP9FA3QZTA3JV637',
                'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Sentinel::getUser();
        
        $query = GeneralUpload::where('uploaded_by', $user->id)->orderBy('created_at', 'desc');
        
        // Filter by type if provided
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        $uploads = $query->get();
        return view('learning.general-uploads.index', compact('uploads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('learning.general-uploads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Handle regular file upload (non-chunked)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $type = $request->input('type', 'other');
            
            $upload = $this->saveUpload($file, $type);
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'upload' => $upload
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ], 400);
    }

    /**
     * Upload chunk endpoint
     */
    public function uploadChunk(Request $request)
    {
        if (!$request->hasFile('chunk')) {
            return response()->json(['success' => false, 'message' => 'No chunk file provided'], 400);
        }
        
        $chunk = $request->file('chunk');
        $index = $request->input('index');
        $totalChunks = $request->input('totalChunks');
        $filename = $request->input('filename');
        $fileId = $request->input('fileId');
        $type = $request->input('type', 'other');
        
        // Store chunk temporarily in local storage
        $chunkDir = storage_path('app/chunks/' . $fileId);
        if (!File::exists($chunkDir)) {
            File::makeDirectory($chunkDir, 0755, true);
        }
        
        $chunk->move($chunkDir, 'chunk_' . $index);
        
        return response()->json([
            'success' => true,
            'message' => 'Chunk uploaded',
            'index' => $index,
            'totalChunks' => $totalChunks
        ]);
    }

    /**
     * Merge chunks endpoint - uploads to S3 after merging
     */
    public function mergeChunks(Request $request)
    {
        $filename = $request->input('filename');
        $fileId = $request->input('fileId');
        $type = $request->input('type', 'other');
        $totalChunks = $request->input('totalChunks');
        
        $chunkDir = storage_path('app/chunks/' . $fileId);
        
        if (!File::exists($chunkDir)) {
            return response()->json(['success' => false, 'message' => 'Chunks directory not found'], 400);
        }
        
        // Create temp file for merged content
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }
        
        $tempFilePath = $tempDir . '/' . $fileId . '_' . $filename;
        
        // Merge chunks into temp file
        $outputFile = fopen($tempFilePath, 'wb');
        
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = $chunkDir . '/chunk_' . $i;
            $chunkContent = File::get($chunkFile);
            fwrite($outputFile, $chunkContent);
            @unlink($chunkFile);
        }
        
        fclose($outputFile);
        
        // Remove chunks directory
        @rmdir($chunkDir);
        
        // Determine actual file type if not provided
        if ($type === 'other') {
            $type = $this->detectFileType($tempFilePath, $filename);
        }
        
        // Generate S3 key
        $typeFolder = $this->getTypeFolder($type);
        $originalName = pathinfo($filename, PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $s3Key = $typeFolder . '/' . $sanitizedName . '_' . uniqid() . '.' . $extension;
        
        // Upload to S3
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
                'Body' => fopen($tempFilePath, 'r'),
                'ACL' => 'public-read',
                'ContentType' => $mimeType,
            ]);
            
            // Get file size before deleting temp file
            $fileSize = filesize($tempFilePath);
            $mimeType = File::mimeType($tempFilePath);
            
            // Delete temp file
            unlink($tempFilePath);
            
            // Save to database
            $upload = new GeneralUpload();
            $upload->name = $filename;
            $upload->path = $result['ObjectURL'];
            $upload->type = $type;
            $upload->file_size = $fileSize;
            $upload->mime_type = $mimeType;
            $upload->uploaded_by = Sentinel::getUser()->id ?? null;
            $upload->save();
            
            return response()->json([
                'success' => true,
                'filePath' => $upload->path,
                'upload' => $upload
            ]);
            
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('S3 Upload Error: ' . $e->getMessage());
            // Clean up temp file
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            return response()->json(['success' => false, 'message' => 'Failed to upload to S3: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $upload = GeneralUpload::findOrFail($id);
        return view('learning.general-uploads.show', compact('upload'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $upload = GeneralUpload::findOrFail($id);
        
        // Delete file from S3
        if ($upload->path) {
            try {
                $parsedUrl = parse_url($upload->path);
                if ($parsedUrl && isset($parsedUrl['path'])) {
                    $s3Key = ltrim($parsedUrl['path'], '/');
                    if (!empty($s3Key)) {
                        $this->s3Client->deleteObject([
                            'Bucket' => $this->bucket,
                            'Key' => $s3Key,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('S3 Delete Error: ' . $e->getMessage());
                // Continue with database deletion even if S3 deletion fails
            }
        }
        
        $upload->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully'
        ]);
    }
    
    /**
     * Save uploaded file directly to S3
     */
    private function saveUpload($file, $type)
    {
        // Determine actual type if not provided
        if ($type === 'other') {
            $type = $this->detectFileType($file->getPathname(), $file->getClientOriginalName());
        }
        
        // Generate S3 key
        $typeFolder = $this->getTypeFolder($type);
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
        $extension = $file->getClientOriginalExtension();
        $s3Key = $typeFolder . '/' . $sanitizedName . '_' . uniqid() . '.' . $extension;
        
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
                'Body' => fopen($file->getPathname(), 'r'),
                'ACL' => 'public-read',
                'ContentType' => $file->getMimeType(),
            ]);
            
            $upload = new GeneralUpload();
            $upload->name = $file->getClientOriginalName();
            $upload->path = $result['ObjectURL'];
            $upload->type = $type;
            $upload->file_size = $file->getSize();
            $upload->mime_type = $file->getMimeType();
            $upload->uploaded_by = Sentinel::getUser()->id ?? null;
            $upload->save();
            
            return $upload;
            
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('S3 Upload Error: ' . $e->getMessage());
            throw new \Exception('Failed to upload file to S3: ' . $e->getMessage());
        }
    }
    
    /**
     * Get folder name based on type
     */
    private function getTypeFolder($type)
    {
        $folders = [
            'video' => 'videos',
            'audio' => 'audios',
            'book' => 'books',
            'paper' => 'papers',
            'document' => 'documents',
            'image' => 'images',
            'other' => 'others'
        ];
        
        return $folders[$type] ?? 'others';
    }
    
    /**
     * Detect file type based on extension and mime type
     */
    private function detectFileType($filePath, $filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeType = File::mimeType($filePath);
        
        // Video types
        if (in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm', 'flv', 'wmv']) || 
            strpos($mimeType, 'video/') === 0) {
            return 'video';
        }
        
        // Audio types
        if (in_array($extension, ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma']) || 
            strpos($mimeType, 'audio/') === 0) {
            return 'audio';
        }
        
        // Book types (epub, mobi, etc.)
        if (in_array($extension, ['epub', 'mobi', 'azw', 'azw3'])) {
            return 'book';
        }
        
        // Paper types (academic)
        if (in_array($extension, ['pdf']) && strpos($filename, 'paper') !== false) {
            return 'paper';
        }
        
        // Document types
        if (in_array($extension, ['doc', 'docx', 'txt', 'rtf', 'odt'])) {
            return 'document';
        }
        
        // Image types
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']) || 
            strpos($mimeType, 'image/') === 0) {
            return 'image';
        }
        
        return 'other';
    }
}
