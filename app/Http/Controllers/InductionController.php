<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\User;
use App\Models\InductionChecklist;

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

            // Create induction checklist items
            $checklistItems = [
                'Review and sign pending company policies.',
                'Complete training on Cash Handling in the Loan Management System.',
                'Explore your personal dashboard and tools.',
                'Reach out to your supervisor for any guidance.'
            ];
            foreach ($checklistItems as $item) {
                InductionChecklist::create([
                    'user_id' => $u->id,
                    'item' => $item,
                    'completed' => false
                ]);
            }

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }

    public function toggleChecklistItem(Request $request)
    {
        $user = Sentinel::getUser();
        if ($user) {
            $itemId = $request->input('item_id');
            $completed = $request->input('completed');

            $item = InductionChecklist::where('user_id', $user->id)->where('id', $itemId)->first();
            if ($item) {
                $item->completed = $completed;
                $item->save();
                return response()->json(['success' => true]);
            }
        }
        return response()->json(['success' => false], 401);
    }
}
