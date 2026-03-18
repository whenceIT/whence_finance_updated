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
        $topics = GeneralTopic::orderBy('created_at', 'desc')->get();
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();
        
        if ($request->hasFile('poster')) {
            $data['poster'] = $this->savePoster($request->file('poster'));
        }

        GeneralTopic::create($data);

        return redirect()->route('learning.settings.general-topics.index')
            ->with('success', 'General topic created successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048'
        ]);

        $topic = GeneralTopic::findOrFail($id);
        
        $data = $request->all();
        
        if ($request->hasFile('poster')) {
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
        }

        $topic->update($data);

        return redirect()->route('learning.settings.general-topics.index')
            ->with('success', 'General topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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
            ->with('success', 'General topic deleted successfully.');
    }
    
    /**
     * Save poster image to S3 storage
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private function savePoster($file)
    {
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
            return null;
        }
    }
}
