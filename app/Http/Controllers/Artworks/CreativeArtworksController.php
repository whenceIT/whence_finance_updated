<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreativeArtworksController extends Controller
{
    public function dashboard()
    {
        return view('artworks.dashboard');
    }

    public function reports()
    {
        return view('artworks.reports');
    }
}
