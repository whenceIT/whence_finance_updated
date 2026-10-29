<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LostClientRecoveryController extends Controller
{
    public function index() { return view('artworks.lost-client-recoveries.index'); }
    public function create() { return view('artworks.lost-client-recoveries.create'); }
    public function repayment() { return view('artworks.lost-client-recoveries.repayment'); }
    public function profitShare() { return view('artworks.lost-client-recoveries.profit-share'); }
    public function payments() { return view('artworks.lost-client-recoveries.payments'); }
    public function reports() { return view('artworks.lost-client-recoveries.reports'); }
}
