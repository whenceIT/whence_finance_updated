<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanReferralController extends Controller
{
    public function index() { return view('artworks.loan-referrals.index'); }
    public function create() { return view('artworks.loan-referrals.create'); }
    public function pending() { return view('artworks.loan-referrals.index'); }
    public function due() { return view('artworks.loan-referrals.due'); }
    public function payments() { return view('artworks.loan-referrals.payments'); }
    public function reports() { return view('artworks.loan-referrals.reports'); }
}
