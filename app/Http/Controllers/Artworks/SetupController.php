<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function categories() { return view('artworks.setup.categories'); }
    public function expenseCategories() { return view('artworks.setup.expense-categories'); }
    public function incomeCategories() { return view('artworks.setup.income-categories'); }
    public function positions() { return view('artworks.setup.positions'); }
}
