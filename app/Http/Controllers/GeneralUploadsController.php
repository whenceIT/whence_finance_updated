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
            'retries' => 3,
            'timeout' => 300,
            'connect_timeout' => 60,
            'force_path_style' => true, // Required for DigitalOcean Spaces
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

        $query = GeneralUpload::orderBy('created_at', 'desc');
        
        // Filter by type if provided
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        // Filter by general topic if provided
        $topicName = null;
        $topicPoster = null;
        if ($request->has('topic')) {
            $query->where('general_topic_id', $request->topic);
            $topic = \App\Models\GeneralTopic::find($request->topic);
            if ($topic) {
                $topicName = $topic->name;
                $topicPoster = $topic->poster;
            }
        }
        
        $uploads = $query->get();
        return view('learning.general-uploads.index', compact('uploads', 'topicName', 'topicPoster'));
    }

    /**
     * Display a listing of the resource for Watch and Learning page.
     *
     * @return \Illuminate\Http\Response
     */
    public function watchAndLearning(Request $request)
    {
        $user = Sentinel::getUser();
        
        // Check if user is admin (role id 1)
        $isAdmin = $user->roles->first() && $user->roles->first()->id == 1;

        $query = GeneralUpload::orderBy('created_at', 'desc');
        
        // Filter by type if provided
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        // Filter by general topic if provided
        $topicName = null;
        $topicPoster = null;
        if ($request->has('topic')) {
            $query->where('general_topic_id', $request->topic);
            $topic = \App\Models\GeneralTopic::find($request->topic);
            if ($topic) {
                $topicName = $topic->name;
                $topicPoster = $topic->poster;
            }
        }
        
        $uploads = $query->get();
        
        // Check for specific upload to auto-play
        $autoPlayUpload = null;
        if ($request->has('upload')) {
            $autoPlayUpload = \App\Models\GeneralUpload::find($request->upload);
        }

        return view('learning.watch-and-learning', compact('uploads', 'topicName', 'topicPoster', 'autoPlayUpload'));
    }

    public function updateDailyLearningAdvisor(Request $request)
    {
        \App\Helpers\LearningHelper::updateDailyLearning();
        return response()->json(['success' => true]);
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
        
        try {
            // Validate request for both regular and chunked uploads
            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'required|file|max:200000', // 200MB max size
                    'type' => 'required|in:video,audio,book,paper,document,image,other',
                    'general_topic_id' => 'nullable|exists:general_topics,id',
                    'position_id' => 'nullable|array',
                    'position_id.*' => 'integer|between:1,21',
                    'poster' => 'nullable|file|image|max:10000' // 10MB max size for poster
                ]);
                
                // Handle regular file upload (non-chunked)
                $file = $request->file('file');
                $type = $request->input('type', 'other');
                $poster = $request->file('poster');
                $generalTopicId = $request->input('general_topic_id');
                $positionIds = $request->input('position_id', []);
                
                $upload = $this->saveUpload($file, $type, $poster, $generalTopicId, $positionIds);
            } else {
                $request->validate([
                    'file_path' => 'required|url',
                    'type' => 'required|in:video,audio,book,paper,document,image,other',
                    'general_topic_id' => 'nullable|exists:general_topics,id',
                    'position_id' => 'nullable|array',
                    'position_id.*' => 'integer|between:1,21',
                    'poster_path' => 'nullable|url'
                ]);
                
                // Handle chunked upload with file path
                $upload = new GeneralUpload();
                $upload->name = $request->input('filename', 'Unknown File');
                $upload->path = $request->input('file_path');
                $upload->type = $request->input('type', 'other');
                $upload->file_size = 0; // We don't have the file size from chunked upload
                $upload->mime_type = ''; // We don't have the mime type from chunked upload
                $upload->uploaded_by = Sentinel::getUser()->id ?? null;
                
                // Handle poster path
                if ($request->has('poster_path')) {
                    $upload->poster = $request->input('poster_path');
                }
                
                // Handle new fields
                $upload->general_topic_id = $request->input('general_topic_id');
                $upload->save();
                
                // Attach positions
                if (!empty($positionIds)) {
                    $upload->positions()->sync($positionIds);
                }
            }
            
            return redirect()->route('learning.general-uploads.index')
                ->with('toastr_type', 'success')
                ->with('toastr_message', 'File uploaded successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            Log::error('Validation Error: ' . json_encode($e->errors()));
            return redirect()->route('learning.general-uploads.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Validation failed: ' . implode(', ', collect($e->errors())->flatten()->toArray()));
        } catch (\Exception $e) {
            Log::error('Upload Error: ' . $e->getMessage());
            return redirect()->route('learning.general-uploads.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload chunk endpoint
     */
    public function uploadChunk(Request $request)
    {
        Log::info('Upload chunk endpoint called', $request->all());
        
        if (!$request->hasFile('chunk')) {
            Log::error('No chunk file provided');
            return response()->json(['success' => false, 'message' => 'No chunk file provided'], 400);
        }
        
        $chunk = $request->file('chunk');
        $index = $request->input('index');
        $totalChunks = $request->input('totalChunks');
        $filename = $request->input('filename');
        $fileId = $request->input('fileId');
        $type = $request->input('type', 'other');
        
        Log::info("Processing chunk $index of $totalChunks for file $filename ($fileId), type: $type");
        
        // Store chunk temporarily in local storage
        $chunksPath = storage_path('app/chunks');
        if (!file_exists($chunksPath)) {
            mkdir($chunksPath, 0755, true);
        }
        
        $chunkPath = $chunksPath . '/' . $fileId . '_part_' . $index;
        $chunk->move($chunksPath, $fileId . '_part_' . $index);
        Log::info("Chunk saved to: $chunkPath, size: " . filesize($chunkPath) . " bytes");
        
        return response()->json([
            'success' => true,
            'message' => 'Chunk uploaded',
            'index' => $index,
            'totalChunks' => $totalChunks
        ]);
    }

    /**
     * Merge uploaded chunks.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function mergeChunks(Request $request)
    {
        $fileName = $request->filename;
        $fileId = $request->fileId;
        $type = $request->type;
        $totalChunks = $request->totalChunks;
        
        $chunksPath = storage_path('app/chunks');
        $uploadsPath = storage_path('app/uploads');
        if (!file_exists($uploadsPath)) {
            mkdir($uploadsPath, 0755, true);
        }
        
        $finalPath = $uploadsPath . '/' . $fileName;
        $output = fopen($finalPath, 'ab');
        
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $chunksPath . '/' . $fileId . '_part_' . $i;
            
            if (!file_exists($chunkPath)) {
                return response()->json(['status' => 'error', 'message' => 'Chunk ' . $i . ' not found'], 400);
            }
            
            $chunk = fopen($chunkPath, 'rb');
            stream_copy_to_stream($chunk, $output);
            fclose($chunk);
            unlink($chunkPath);
        }
        
        fclose($output);
        
        // Upload to S3
        try {
            $originalName = pathinfo($fileName, PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
            $finalFileName = $sanitizedName . '_' . uniqid() . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            
            $s3Client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region' => 'nyc3',
                'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                'credentials' => [
                    'key' => 'DO00RP9FA3QZTA3JV637',
                    'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                ],
            ]);
            
            $result = $s3Client->putObject([
                'Bucket' => 'wfssystem',
                'Key' => $finalFileName,
                'Body' => fopen($finalPath, 'r'),
                'ACL' => 'public-read',
            ]);
            
            unlink($finalPath);
            
            // Handle poster upload
            $posterPath = null;
            if ($request->hasFile('poster')) {
                $poster = $request->file('poster');
                $posterOriginalName = pathinfo($poster->getClientOriginalName(), PATHINFO_FILENAME);
                $posterSanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $posterOriginalName);
                $posterFinalFileName = 'posters/' . $posterSanitizedName . '_' . uniqid() . '.' . $poster->getClientOriginalExtension();
                
                $posterResult = $s3Client->putObject([
                    'Bucket' => 'wfssystem',
                    'Key' => $posterFinalFileName,
                    'Body' => fopen($poster->getPathname(), 'r'),
                    'ACL' => 'public-read',
                ]);
                
                $posterPath = $posterResult['ObjectURL'];
            }
            
            return response()->json([
                'status' => 'file merged',
                'filePath' => $result['ObjectURL'],
                'fileName' => $fileName,
                'posterPath' => $posterPath
            ]);
            
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('S3 Upload Error: ' . $e->getMessage());
            unlink($finalPath);
            return response()->json(['status' => 'error', 'message' => 'Failed to upload to S3'], 500);
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
        
        // Eager load positions from the pivot table
        $uploadPositions = \DB::table('general_upload_position')
            ->join('job_positions', 'general_upload_position.position_id', '=', 'job_positions.id')
            ->where('general_upload_id', $id)
            ->select('job_positions.*')
            ->get();
        
        return view('learning.general-uploads.edit', compact('upload', 'generalTopics', 'positions', 'uploadPositions'));
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
        $upload->save();
        
        // Sync positions
        $positionIds = $request->input('position_id', []);
        $upload->positions()->sync($positionIds);
        
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
     * Toggle like for the specified upload.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function like($id)
    {
        $upload = GeneralUpload::findOrFail($id);
        $userId = Sentinel::getUser()->id;

        // Check if user already liked this upload
        $existingLike = \App\Models\GeneralUploadLike::where('user_id', $userId)
            ->where('general_upload_id', $id)
            ->first();

        if ($existingLike) {
            // Unlike: remove the like
            $existingLike->delete();
            $liked = false;
        } else {
            // Like: create new like
            \App\Models\GeneralUploadLike::create([
                'user_id' => $userId,
                'general_upload_id' => $id
            ]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $upload->fresh()->likes_count
        ]);
    }

    /**
     * Increment view count for the specified upload.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function incrementView($id)
    {
        $upload = GeneralUpload::findOrFail($id);

        // Record the viewer
        \App\Models\GeneralView::recordView('upload', $id);

        // Increment views_count
        $upload->increment('views_count');

        return response()->json([
            'success' => true,
            'views_count' => $upload->views_count
        ]);
    }

    /**
     * Assign positions to the specified upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignPositions(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|exists:general_uploads,id',
            'position_ids' => 'nullable|array',
            'position_ids.*' => 'integer|exists:job_positions,id'
        ]);

        $upload = GeneralUpload::findOrFail($request->upload_id);
        $upload->positions()->sync($request->position_ids ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Positions assigned successfully'
        ]);
    }
    
    /**
     * Save uploaded file directly to S3
     */
    private function saveUpload($file, $type, $poster = null, $generalTopicId = null, $positionIds = [])
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
            $upload->save();
            
            // Attach positions
            if (!empty($positionIds)) {
                $upload->positions()->sync($positionIds);
            }
            
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
