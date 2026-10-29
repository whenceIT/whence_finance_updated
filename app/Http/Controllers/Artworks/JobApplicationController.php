<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index() { return view('artworks.job-applications.index'); }
    public function create() { return view('artworks.job-applications.create'); }
    public function pending() { return view('artworks.job-applications.index'); }
    public function approved() { return view('artworks.job-applications.index'); }
    public function employed() { return view('artworks.job-applications.index'); }
    public function qualifying() { return view('artworks.job-applications.index'); }
    public function reports() { return view('artworks.job-applications.reports'); }
}
