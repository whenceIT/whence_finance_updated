<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index() { return view('artworks.daily-reports.index'); }
    public function incomeCreate() { return view('artworks.daily-reports.income-create'); }
    public function expenseCreate() { return view('artworks.daily-reports.expense-create'); }
    public function branchReport() { return view('artworks.daily-reports.branch-report'); }
    public function reports() { return view('artworks.daily-reports.reports'); }
}
