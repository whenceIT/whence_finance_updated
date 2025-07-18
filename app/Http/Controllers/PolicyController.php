<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class PolicyController extends Controller
{
    /**
     * Display all policies
     *
     * @return \Illuminate\View\View
     */
    public function viewPolicies()
    {
        $policies = Policy::latest()->get();
        
        return view('policies.view', compact('policies'));
    }

    /**
     * 
     *
     * @return \Illuminate\View\View
     */
    public function addPolicies()
    {
        return view('policies.add');
    }

    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePolicies(Request $request)
{
   
    $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'policy_file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        Flash::warning(trans('general.validation_error'));
        return redirect()->back()->withInput()->withErrors($validator);
    }

    if ($request->hasFile('policy_file')) {
        $file = $request->file('policy_file');
        
        // Create filename without using Str::slug
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
        $fileName = $sanitizedName . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        try {
            // Initialize S3 client
            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => 'nyc3',
                'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                'credentials' => [
                    'key'    => 'DO00RP9FA3QZTA3JV637',
                    'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                ],
            ]);

            // Upload file to DigitalOcean Spaces
            $result = $s3Client->putObject([
                'Bucket' => 'wfspolicies',
                'Key'    => 'policies/' . $fileName,
                'Body'   => fopen($file->getPathname(), 'r'),
                'ACL'    => 'public-read',
                'ContentType' => $file->getClientMimeType(),
            ]);

           
            $fileUrl = $result['ObjectURL'];
            
            // Create policy record
            Policy::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => 'policies/' . $fileName,
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
            ]);

            Flash::success(trans('general.successfully_saved'));
            return redirect()->route('policies.view_policies')
                ->with('success', 'Policy uploaded successfully.');

        } catch (AwsException $e) {
            Log::error('Policy Upload Error: ' . $e->getMessage());
            Flash::error(trans('general.upload_failed'));
            return back()->with('error', 'Failed to upload policy to DigitalOcean Spaces.');
        }
    } else {
        Flash::error(trans('general.no_file_uploaded'));
        return back()->with('error', 'No policy file was uploaded.');
    }
}


}
