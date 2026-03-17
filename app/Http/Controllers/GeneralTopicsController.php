<?php

namespace App\Http\Controllers;

use App\Models\GeneralTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralTopicsController extends Controller
{
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
            // Delete old poster if exists
            if ($topic->poster) {
                Storage::disk('public')->delete($topic->poster);
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
        
        // Delete poster if exists
        if ($topic->poster) {
            Storage::disk('public')->delete($topic->poster);
        }
        
        $topic->delete();

        return redirect()->route('learning.settings.general-topics.index')
            ->with('success', 'General topic deleted successfully.');
    }
    
    /**
     * Save poster image to storage
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private function savePoster($file)
    {
        $path = $file->store('general_topic_posters', 'public');
        return $path;
    }
}
