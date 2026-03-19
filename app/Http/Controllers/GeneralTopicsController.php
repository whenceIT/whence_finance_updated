<?php

namespace App\Http\Controllers;

use App\Models\GeneralTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GeneralTopicsController extends Controller
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
    public function index()
    {
        // Increase PHP upload limits to handle large files
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('max_input_time', 600); // 10 minutes
        ini_set('memory_limit', '256M');
        $topics = GeneralTopic::with('uploads')->orderBy('created_at', 'desc')->get();
        return view('learning.settings.general-topics.index', compact('topics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('learning.settings.general-topics.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $data = $validatedData;
            
            if ($request->hasFile('poster')) {
                try {
                    $data['poster'] = $this->savePoster($request->file('poster'));
                } catch (\Exception $e) {
                    Log::error('Poster Upload Error: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->with('toastr_type', 'error')
                        ->with('toastr_message', 'Failed to upload poster image. Please try again.')
                        ->with('toastr_title', 'File Upload Error');
                }
            }

            GeneralTopic::create($data);

            return redirect()->route('learning.settings.general-topics.index')
                ->with('toastr_type', 'success')
                ->with('toastr_message', 'General topic created successfully.')
                ->with('toastr_title', 'Success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($th);
            Log::error('Validation Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Validation failed. Please check your input.')
                ->with('toastr_title', 'Validation Error');
        } catch (\Exception $e) {
            Log::error('General Topic Creation Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Failed to create general topic. Please try again.')
                ->with('toastr_title', 'Error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $topic = GeneralTopic::findOrFail($id);
        return view('learning.settings.general-topics.edit', compact('topic'));
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
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $topic = GeneralTopic::findOrFail($id);
            
            $data = $validatedData;
            
            if ($request->hasFile('poster')) {
                try {
                    // Delete old poster from S3 if exists
                    if ($topic->poster) {
                        try {
                            $parsedUrl = parse_url($topic->poster);
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
                            // Continue with new poster upload even if old poster deletion fails
                        }
                    }
                    $data['poster'] = $this->savePoster($request->file('poster'));
                } catch (\Exception $e) {
                    Log::error('Poster Upload Error: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->with('toastr_type', 'error')
                        ->with('toastr_message', 'Failed to upload poster image. Please try again.')
                        ->with('toastr_title', 'File Upload Error');
                }
            }

            $topic->update($data);

            return redirect()->route('learning.settings.general-topics.index')
                ->with('toastr_type', 'success')
                ->with('toastr_message', 'General topic updated successfully.')
                ->with('toastr_title', 'Success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Validation failed. Please check your input.')
                ->with('toastr_title', 'Validation Error');
        } catch (\Exception $e) {
            Log::error('General Topic Update Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Failed to update general topic. Please try again.')
                ->with('toastr_title', 'Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $topic = GeneralTopic::findOrFail($id);
            
            // Delete poster from S3 if exists
            if ($topic->poster) {
                try {
                    $parsedUrl = parse_url($topic->poster);
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
            
            $topic->delete();

            return redirect()->route('learning.settings.general-topics.index')
                ->with('toastr_type', 'success')
                ->with('toastr_message', 'General topic deleted successfully.')
                ->with('toastr_title', 'Success');
        } catch (\Exception $e) {
            Log::error('General Topic Deletion Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Failed to delete general topic. Please try again.')
                ->with('toastr_title', 'Error');
        }
    }
    
    /**
     * Save poster image to S3 storage
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     * @throws \Exception
     */
    private function savePoster($file)
    {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, GIF, and SVG images are allowed.');
        }
        
        // Validate file size
        if ($file->getSize() > 2048 * 1024) { // 2MB
            throw new \Exception('File size exceeds maximum limit of 2MB.');
        }
        
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
        $extension = $file->getClientOriginalExtension();
        $posterKey = 'general_topic_posters/' . $sanitizedName . '_' . uniqid() . '.' . $extension;
        
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $posterKey,
                'Body' => fopen($file->getPathname(), 'r'),
                'ACL' => 'public-read',
                'ContentType' => $file->getMimeType(),
            ]);
            
            return $result['ObjectURL'];
            
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('S3 Poster Upload Error: ' . $e->getMessage());
            throw new \Exception('Failed to upload poster image to storage. Please try again.');
        } catch (\Exception $e) {
            Log::error('Poster Upload Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
