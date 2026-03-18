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
    
    // Positions array
    private $positions = [
        1 => 'General Operations Manager (GOM)',
        2 => 'Provincial Manager',
        3 => 'District Regional Manager',
        4 => 'District Manager',
        5 => 'Branch Manager',
        6 => 'IT Manager',
        7 => 'Risk Manager',
        8 => 'Management Accountant',
        9 => 'Motor Vehicles Manager',
        10 => 'Payroll Loans Manager',
        11 => 'Policy & Training Manager',
        12 => 'Manager Administration',
        13 => 'R&D Coordinator',
        14 => 'Recoveries Coordinator',
        15 => 'IT Coordinator',
        16 => 'General Operations Administrator (GOA)',
        17 => 'Performance Operations Administrator (POA)',
        18 => 'Creative Artwork & Marketing Representative Manager',
        19 => 'Administration',
        20 => 'Super Seer',
        21 => 'Loan Consultant'
    ];
    
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
        
        // Check if user is admin (role id 1)
        $isAdmin = $user->roles->first() && $user->roles->first()->id == 1;
        
        if ($isAdmin) {
            // Admin sees all uploads
            $query = GeneralUpload::orderBy('created_at', 'desc');
        } else {
            // Regular user sees only their own uploads
            $query = GeneralUpload::where('uploaded_by', $user->id)->orderBy('created_at', 'desc');
        }
        
        // Filter by type if provided
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        // Filter by general topic if provided
        if ($request->has('topic')) {
            $query->where('general_topic_id', $request->topic);
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
        $generalTopics = \App\Models\GeneralTopic::all();
        $positions = $this->positions;
        return view('learning.general-uploads.create', compact('generalTopics', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Increase PHP upload limits to handle large files
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('max_input_time', 600); // 10 minutes
        ini_set('memory_limit', '256M');
        
        // Handle regular file upload (non-chunked)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $type = $request->input('type', 'other');
            $poster = $request->file('poster');
            $generalTopicId = $request->input('general_topic_id');
            $positionId = $request->input('position_id');
            
            $upload = $this->saveUpload($file, $type, $poster, $generalTopicId, $positionId);
            
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
        
        // Save poster if received in first chunk
        if ($index === 0 && $request->hasFile('poster')) {
            $poster = $request->file('poster');
            $poster->move($chunkDir, 'poster.' . $poster->getClientOriginalExtension());
        }
        
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
        // Increase PHP limits to handle large file merging
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('max_input_time', 600); // 10 minutes
        ini_set('memory_limit', '256M');
        
        // Get request data from JSON or form data
        $data = $request->json() ? $request->json()->all() : $request->all();
        
        $filename = $data['filename'];
        $fileId = $data['fileId'];
        $type = $data['type'] ?? 'other';
        $totalChunks = $data['totalChunks'];
        $poster = $request->file('poster');
        
        $chunkDir = storage_path('app/chunks/' . $fileId);
        
        if (!File::exists($chunkDir)) {
            return response()->json(['success' => false, 'message' => 'Chunks directory not found'], 400);
        }
        
        // Check if poster file exists in chunk directory
        $posterFiles = File::files($chunkDir);
        foreach ($posterFiles as $file) {
            if (strpos($file->getFilename(), 'poster.') === 0) {
                // Create a temporary file instance for the poster
                $poster = new \Illuminate\Http\UploadedFile(
                    $file->getPathname(),
                    'poster.' . $file->getExtension(),
                    mime_content_type($file->getPathname()),
                    filesize($file->getPathname()),
                    true
                );
                break;
            }
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
        
        // Get file size and mimetype before uploading
        $fileSize = filesize($tempFilePath);
        $mimeType = File::mimeType($tempFilePath);
        
        // Upload to S3
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
                'Body' => fopen($tempFilePath, 'r'),
                'ACL' => 'public-read',
                'ContentType' => $mimeType,
            ]);
            
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
            
            // Handle poster upload
            if ($poster) {
                $posterPath = $this->savePoster($poster, $typeFolder);
                $upload->poster = $posterPath;
            }
            
            // Handle new fields
            $upload->general_topic_id = $data['general_topic_id'] ?? null;
            $upload->position_id = $data['position_id'] ?? null;
            
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $upload = GeneralUpload::findOrFail($id);
        $generalTopics = \App\Models\GeneralTopic::all();
        $positions = $this->positions;
        return view('learning.general-uploads.edit', compact('upload', 'generalTopics', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $upload = GeneralUpload::findOrFail($id);
        

        // Update basic information
        $upload->name = $request->input('name', $upload->name);
        $upload->type = $request->input('type', $upload->type);
        
        // Handle file replacement
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $typeFolder = $this->getTypeFolder($upload->type);
            
            // Delete old file from S3
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
                    // Continue with new file upload even if old file deletion fails
                }
            }
            
            // Upload new file to S3
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
                
                $upload->path = $result['ObjectURL'];
                $upload->file_size = $file->getSize();
                $upload->mime_type = $file->getMimeType();
                
            } catch (\Aws\Exception\AwsException $e) {
                Log::error('S3 Upload Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload file to S3');
            }
        }
        
        // Handle poster upload
        if ($request->hasFile('poster')) {
            $poster = $request->file('poster');
            $typeFolder = $this->getTypeFolder($upload->type);
            $posterPath = $this->savePoster($poster, $typeFolder);
            $upload->poster = $posterPath;
        }
        
        // Handle new fields
        $upload->general_topic_id = $request->input('general_topic_id');
        $upload->position_id = $request->input('position_id');
        
        $upload->save();
        
        return redirect()->route('learning.general-uploads.index')->with('success', 'File updated successfully');
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
    private function saveUpload($file, $type, $poster = null, $generalTopicId = null, $positionId = null)
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
            
            // Handle poster upload
            if ($poster) {
                $posterPath = $this->savePoster($poster, $typeFolder);
                $upload->poster = $posterPath;
            }
            
            // Handle new fields
            $upload->general_topic_id = $generalTopicId;
            $upload->position_id = $positionId;
            
            $upload->save();
            
            return $upload;
            
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('S3 Upload Error: ' . $e->getMessage());
            throw new \Exception('Failed to upload file to S3: ' . $e->getMessage());
        }
    }
    
    /**
     * Save poster/thumbnail to S3
     */
    private function savePoster($poster, $typeFolder)
    {
        $originalName = pathinfo($poster->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
        $extension = $poster->getClientOriginalExtension();
        $posterKey = 'posters/' . $sanitizedName . '_' . uniqid() . '.' . $extension;
        
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $posterKey,
                'Body' => fopen($poster->getPathname(), 'r'),
                'ACL' => 'public-read',
                'ContentType' => $poster->getMimeType(),
            ]);
            
            return $result['ObjectURL'];
            
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('S3 Poster Upload Error: ' . $e->getMessage());
            return null;
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
