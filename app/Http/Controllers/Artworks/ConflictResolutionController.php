<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConflictResolutionController extends Controller
{
    public function index() { return view('artworks.conflict-resolution.index'); }
    public function create() { return view('artworks.conflict-resolution.create'); }
    public function charges() { return view('artworks.conflict-resolution.charges'); }
    public function payments() { return view('artworks.conflict-resolution.payments'); }
    public function outstanding() { return view('artworks.conflict-resolution.outstanding'); }
    public function reports() { return view('artworks.conflict-resolution.reports'); }
}
