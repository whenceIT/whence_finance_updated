<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\User;

class InductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function markAsSeen()
    {
        $user = Sentinel::getUser();
        if ($user) {
            $u = User::find($user->id);
            $u->has_seen_induction = true;
            $u->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}
