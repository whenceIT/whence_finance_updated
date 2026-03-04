<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaterial;
use App\Models\CourseCategory;
use App\Models\CourseTopic;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class TrainingMaterialController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No middleware - authentication handled via routes
    }

    /**
     * Display a listing of training materials.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $roleId = $role ? $role->id : null;
        $isAdmin = $role && in_array($role->id, ['1']);

        $query = $isAdmin 
            ? TrainingMaterial::query() // Admin sees all materials
            : TrainingMaterial::active(); // Others see only active

        // Apply filters
        if ($request->has('category') && $request->category != 'all') {
            $query->byCategoryId($request->category);
        }
        
        if ($request->has('department') && $request->department != 'all') {
            $query->byDepartment($request->department);
        }

        if ($request->has('type') && $request->type != 'all') {
            $query->byType($request->type);
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->byCategory($request->category);
        }

        // Apply role-based filtering (only for non-admin users)
        if ($roleId && !$isAdmin) {
            $query->forRole($roleId);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        $materials = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('learning.training-materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new training material.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to create training materials
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect('learning/training-materials');
        }

        // Get active categories for dropdown
        $categories = CourseCategory::active()->ordered()->get();

        return view('learning.training-materials.create', compact('categories'));
    }

    /**
     * Store a newly created training material in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (!Sentinel::check()) {
                return redirect('login');
            }

            $user = Sentinel::getUser();
            $role = $user->roles->first();

            // Check if user has permission to create training materials
            if (!$role || !in_array($role->id, ['1', '6', '4'])) {
                return redirect()->route('learning.training-materials.index')
                    ->with('toastr_type', 'error')
                    ->with('toastr_message', 'You do not have permission to create training materials.');
            }

            // Remove the dd() call - it was for debugging
            // Validate course info and topics
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_type' => 'required|in:document,audio,video',
                'department' => 'required|in:Operations,Recoveries,Administration,Finance,IT,HR,Legal,Compliance,General',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'exists:course_categories,id',
                'target_role' => 'required|in:all,1,4,6,3,5,10',
                'is_active' => 'nullable',
                'is_featured' => 'nullable',
                // Topic validation - now using file uploads
                'topic_name' => 'required|array|min:1',
                'topic_name.*' => 'required|string|max:255',
                'topic_duration' => 'nullable|array',
                'topic_duration.*' => 'nullable|integer|min:1',
                'video_topic_file' => 'nullable|array',
                'video_topic_file.*' => 'nullable|file|max:102400|mimetypes:video/*',
                'audio_topic_file' => 'nullable|array',
                'audio_topic_file.*' => 'nullable|file|max:102400|mimetypes:audio/*',
                'pdf_topic_file' => 'nullable|array',
                'pdf_topic_file.*' => 'nullable|file|max:102400|mimetypes:application/pdf',
                'ppt_topic_file' => 'nullable|array',
                'ppt_topic_file.*' => 'nullable|file|max:102400',

            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorMessages = implode('<br>', $validator->errors()->all());
                return redirect()->back()
                    ->with('toastr_type', 'error')
                    ->with('toastr_message', 'Validation failed:<br>' . $errorMessages)
                    ->withErrors($validator)
                    ->withInput();
            }

            // Skip main file upload - files are uploaded per topic
            // Create the training material
            $material = TrainingMaterial::create([
                'title' => $request->title,
                'description' => $request->description,
                'material_type' => $request->material_type,
                'file_path' => null, // No main file - files are attached to topics
                'file_name' => null,
                'file_size' => null,
                'mime_type' => null,
                'duration' => null,
                'department' => $request->department,
                'category' => null, // No longer used, using categories relationship instead
                'target_role' => $request->target_role,
                'created_by' => $user->id,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'published_at' => now(),
            ]);

            // Attach categories (many-to-many)
            $categoryIds = $request->category_ids ?? [];
            if (!empty($categoryIds)) {
                $material->categories()->attach($categoryIds);
            }

            // Handle topics - now storing file uploads to S3
            $topicNames = $request->topic_name ?? [];
            $topicDurations = $request->topic_duration ?? [];

            foreach ($topicNames as $index => $topicName) {
                if (!empty($topicName)) {
                    $topicData = [
                        'training_material_id' => $material->id,
                        'topic_name' => $topicName,
                        'topic_type' => $request->material_type,
                        'duration' => $topicDurations[$index] ?? null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ];

                    // Handle video file
                    $videoFile = $request->file('video_topic_file')[$index] ?? null;
                    if ($videoFile) {
                        $originalName = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
                        $fileName = $sanitizedName . '_' . uniqid() . '.' . $videoFile->getClientOriginalExtension();
                        
                        try {
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
                                'Bucket' => 'wfspolicies',
                                'Key' => 'training-materials/' . $fileName,
                                'Body' => fopen($videoFile->getPathname(), 'r'),
                                'ACL' => 'public-read',
                                'ContentType' => $videoFile->getClientMimeType(),
                            ]);
                            
                            $topicData['video_file_path'] = $result['ObjectURL'];
                            $topicData['file_name'] = $videoFile->getClientOriginalName();
                        } catch (\Aws\Exception\AwsException $e) {
                            Log::error('Training Material Upload Error: ' . $e->getMessage());
                            return redirect()->back()
                                ->with('toastr_type', 'error')
                                ->with('toastr_message', 'Failed to upload video file. Please try again.')
                                ->withInput();
                        }
                    }
                    
                    // Handle audio file
                    $audioFile = $request->file('audio_topic_file')[$index] ?? null;
                    if ($audioFile) {
                        $originalName = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
                        $fileName = $sanitizedName . '_' . uniqid() . '.' . $audioFile->getClientOriginalExtension();
                        
                        try {
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
                                'Bucket' => 'wfspolicies',
                                'Key' => 'training-materials/' . $fileName,
                                'Body' => fopen($audioFile->getPathname(), 'r'),
                                'ACL' => 'public-read',
                                'ContentType' => $audioFile->getClientMimeType(),
                            ]);
                            
                            $topicData['audio_file_path'] = $result['ObjectURL'];
                            if (!isset($topicData['file_name'])) {
                                $topicData['file_name'] = $audioFile->getClientOriginalName();
                            }
                        } catch (\Aws\Exception\AwsException $e) {
                            Log::error('Training Material Upload Error: ' . $e->getMessage());
                            return redirect()->back()
                                ->with('toastr_type', 'error')
                                ->with('toastr_message', 'Failed to upload audio file. Please try again.')
                                ->withInput();
                        }
                    }
                    
                    // Handle PDF file
                    $pdfFile = $request->file('pdf_topic_file')[$index] ?? null;
                    if ($pdfFile) {
                        $originalName = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
                        $fileName = $sanitizedName . '_' . uniqid() . '.' . $pdfFile->getClientOriginalExtension();
                        
                        try {
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
                                'Bucket' => 'wfspolicies',
                                'Key' => 'training-materials/' . $fileName,
                                'Body' => fopen($pdfFile->getPathname(), 'r'),
                                'ACL' => 'public-read',
                                'ContentType' => $pdfFile->getClientMimeType(),
                            ]);
                            
                            $topicData['pdf_file_path'] = $result['ObjectURL'];
                            if (!isset($topicData['file_name'])) {
                                $topicData['file_name'] = $pdfFile->getClientOriginalName();
                            }
                        } catch (\Aws\Exception\AwsException $e) {
                            Log::error('Training Material Upload Error: ' . $e->getMessage());
                            return redirect()->back()
                                ->with('toastr_type', 'error')
                                ->with('toastr_message', 'Failed to upload PDF file. Please try again.')
                                ->withInput();
                        }
                    }
                    
                    // Handle PPT file
                    $pptFile = $request->file('ppt_topic_file')[$index] ?? null;
                    if ($pptFile) {
                        $originalName = pathinfo($pptFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
                        $fileName = $sanitizedName . '_' . uniqid() . '.' . $pptFile->getClientOriginalExtension();
                        
                        try {
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
                                'Bucket' => 'wfspolicies',
                                'Key' => 'training-materials/' . $fileName,
                                'Body' => fopen($pptFile->getPathname(), 'r'),
                                'ACL' => 'public-read',
                                'ContentType' => $pptFile->getClientMimeType(),
                            ]);
                            
                            $topicData['ppt_file_path'] = $result['ObjectURL'];
                            if (!isset($topicData['file_name'])) {
                                $topicData['file_name'] = $pptFile->getClientOriginalName();
                            }
                        } catch (\Aws\Exception\AwsException $e) {
                            Log::error('Training Material Upload Error: ' . $e->getMessage());
                            return redirect()->back()
                                ->with('toastr_type', 'error')
                                ->with('toastr_message', 'Failed to upload PPT file. Please try again.')
                                ->withInput();
                        }
                    }

                    // Handle document file
                    $documentFile = $request->file('document_topic_file')[$index] ?? null;
                    if ($documentFile) {
                        $originalName = pathinfo($documentFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
                        $fileName = $sanitizedName . '_' . uniqid() . '.' . $documentFile->getClientOriginalExtension();
                        
                        try {
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
                                'Bucket' => 'wfspolicies',
                                'Key' => 'training-materials/' . $fileName,
                                'Body' => fopen($documentFile->getPathname(), 'r'),
                                'ACL' => 'public-read',
                                'ContentType' => $documentFile->getClientMimeType(),
                            ]);
                            
                            $topicData['document_file_path'] = $result['ObjectURL'];
                            if (!isset($topicData['file_name'])) {
                                $topicData['file_name'] = $documentFile->getClientOriginalName();
                            }
                        } catch (\Aws\Exception\AwsException $e) {
                            Log::error('Training Material Upload Error: ' . $e->getMessage());
                            return redirect()->back()
                                ->with('toastr_type', 'error')
                                ->with('toastr_message', 'Failed to upload document file. Please try again.')
                                ->withInput();
                        }
                    }

                    // Create the course topic with all file paths
                    CourseTopic::create($topicData);
                }
            }

            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'success')
                ->with('toastr_message', 'Training material created successfully with ' . count($topicNames) . ' topics.');
        } catch (\Throwable $th) {

            dd($th);
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Error creating training material: ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified training material.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $roleId = $role ? $role->id : null;

        $material = TrainingMaterial::findOrFail($id);

        // Check if user has permission to view this material
        if (!$material->is_active) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'This training material is not available.');
        }

        if ($roleId && $material->target_role != 'all' && $material->target_role != $roleId) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'You do not have permission to view this training material.');
        }

        // Increment view count
        $material->incrementViewCount();

        return view('learning.training-materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified training material.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to edit training materials
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to edit training materials.');
        }

        $material = TrainingMaterial::findOrFail($id);

        return view('learning.training-materials.edit', compact('material'));
    }

    /**
     * Update the specified training material in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to edit training materials
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to edit training materials.');
        }

        $material = TrainingMaterial::findOrFail($id);

        // Validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|in:Operations,Recoveries,Administration,Finance,IT,HR,Legal,Compliance,General',
            'category' => 'nullable|string|max:100',
            'target_role' => 'required|in:all,1,4,6,3,5,10',
            // is_active and is_featured are optional checkboxes, no validation needed
        ];

        if ($request->hasFile('file')) {
            $rules['file'] = 'required|file|max:102400';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = implode('<br>', $validator->errors()->all());
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Validation failed:<br>' . $errorMessages)
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file update if new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Delete old file
            if (Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            // Validate file type
            $allowedMimeTypes = [
                'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain'],
                'audio' => ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'],
                'video' => ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'],
            ];

            $mimeType = $file->getMimeType();
            $materialType = $request->material_type ?? $material->material_type;

            if (!in_array($mimeType, $allowedMimeTypes[$materialType] ?? [])) {
                return redirect()->back()
                    ->with('toastr_type', 'error')
                    ->with('toastr_message', 'Invalid file type for ' . $materialType . '. Please upload a valid file.')
                    ->withInput();
            }

            // Store new file
            $fileName = time() . '_' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('training-materials/' . $materialType, $fileName, 'public');

            // Get duration for audio/video files
            $duration = null;
            if (in_array($materialType, ['audio', 'video'])) {
                $duration = $materialType === 'audio' ? 300 : 600;
            }

            $material->update([
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
                'duration' => $duration,
                'material_type' => $materialType,
            ]);
        }

        // Update other fields
        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'department' => $request->department,
            'category' => $request->category,
            'target_role' => $request->target_role,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'is_featured' => $request->has('is_featured') ? $request->is_featured : false,
        ]);

        return redirect()->route('learning.training-materials.index')
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Training material updated successfully.');
    }

    /**
     * Remove the specified training material from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        $material = TrainingMaterial::findOrFail($id);

        // Check if user has permission to delete
        // Allow if: user is admin/manager (roles 1, 6, 4) OR user is the creator of this material
        $isAdmin = $role && in_array($role->id, ['1', '6', '4']);
        $isCreator = $material->created_by == $user->id;

        if (!$isAdmin && !$isCreator) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to delete this training material.');
        }

        // Delete file from storage if exists
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->route('learning.training-materials.index')
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Training material deleted successfully.');
    }

    /**
     * Download the specified training material.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $roleId = $role ? $role->id : null;

        $material = TrainingMaterial::findOrFail($id);

        // Check if user has permission to download this material
        if (!$material->is_active) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'This training material is not available.');
        }

        if ($roleId && $material->target_role != 'all' && $material->target_role != $roleId) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'You do not have permission to download this training material.');
        }

        // Increment download count
        $material->incrementDownloadCount();

        $filePath = storage_path('app/public/' . $material->file_path);

        if (!file_exists($filePath)) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'File not found.');
        }

        return response()->download($filePath, $material->file_name);
    }

    /**
     * Display topics and quizzes management page for a training material.
     * Only accessible by trainers.
     *
     * @param int $materialId
     * @return \Illuminate\Http\Response
     */
    public function topics($materialId)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        
        // Check if user is a trainer
        if (!$user || $user->istrainer != 1) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to manage topics and quizzes.');
        }

        $material = TrainingMaterial::with(['topics' => function($query) {
            $query->ordered();
        }, 'topics.quiz'])->findOrFail($materialId);

        return view('learning.training-materials.topics', compact('material'));
    }

    /**
     * Toggle the active status of a training material.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to manage training materials
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('learning.training-materials.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to manage training materials.');
        }

        $material = TrainingMaterial::findOrFail($id);
        $material->is_active = !$material->is_active;
        $material->save();

        return redirect()->back()
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Training material status updated successfully.');
    }
}
