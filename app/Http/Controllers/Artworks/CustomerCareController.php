<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerCareController extends Controller
{
    public function index() { return view('artworks.customer-care.index'); }
    public function create() { return view('artworks.customer-care.create'); }
    public function open() { return view('artworks.customer-care.index'); }
    public function inProgress() { return view('artworks.customer-care.index'); }
    public function resolved() { return view('artworks.customer-care.index'); }
    public function escalated() { return view('artworks.customer-care.index'); }
    public function calendar() { return view('artworks.customer-care.calendar'); }
    public function reports() { return view('artworks.customer-care.reports'); }
}
