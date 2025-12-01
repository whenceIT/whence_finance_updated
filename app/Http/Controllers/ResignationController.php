<?php

namespace App\Http\Controllers;

use App\Models\ResignationLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;

class ResignationController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    // Submit resignation form
    public function create()
    {
        $user = Sentinel::getUser();
        $existing = ResignationLetter::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'manager_approved'])
            ->first();

        return view('resignation.create', compact('existing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'resignation_date' => 'required|date|after:today',
            'reason' => 'required|string|max:1000',
            'letter' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $user = Sentinel::getUser();

        // Check if user already has a pending resignation
        $existing = ResignationLetter::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'manager_approved'])
            ->first();

        $letterPath = null;
        if ($request->hasFile('letter')) {
            $letterPath = $request->file('letter')->store('resignation_letters', 'public');
        }

        if ($existing) {
            // Update existing
            $existing->update([
                'resignation_date' => $request->resignation_date,
                'reason' => $request->reason,
                'letter_path' => $letterPath,
                'status' => 'pending', // Reset to pending if it was manager_approved
                'manager_id' => null,
                'manager_approved_at' => null,
                'manager_comment' => null,
                'admin_id' => null,
                'admin_approved_at' => null,
                'admin_comment' => null,
            ]);
            Flash::success('Resignation letter updated successfully.');
        } else {
            // Create new
            ResignationLetter::create([
                'user_id' => $user->id,
                'resignation_date' => $request->resignation_date,
                'reason' => $request->reason,
                'letter_path' => $letterPath,
                'status' => 'pending',
            ]);
            Flash::success('Resignation letter submitted successfully.');
        }

        return redirect()->route('resignation.my');
    }

    // User's own resignation
    public function myResignations()
    {
        $user = Sentinel::getUser();
        $resignations = ResignationLetter::where('user_id', $user->id)->with(['manager', 'admin'])->get();
        $existing = ResignationLetter::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'manager_approved'])
            ->first();
        return view('resignation.my', compact('resignations', 'existing'));
    }

    // Manager pending approvals (role 4)
    public function managerPending()
    {
        $user = Sentinel::getUser();
        if ($user->role->role_id != 4) {
            abort(403);
        }

        $pending = ResignationLetter::where('status', 'pending')
            // ->whereHas('user', function($q) use ($user) {
            //     $q->where('office_id', $user->office_id);
            // })
            ->with('user')
            ->get();

        return view('resignation.manager_pending', compact('pending'));
    }

    // Admin pending approvals (role 1)
    public function adminPending()
    {
        $user = Sentinel::getUser();
        if ($user->role->role_id != 1) {
            abort(403);
        }

        $pending = ResignationLetter::where('status', 'manager_approved')
            ->with(['user', 'manager'])
            ->get();

        return view('resignation.admin_pending', compact('pending'));
    }

    // Manager approve/decline
    public function managerApprove(Request $request, $id)
    {
        $user = Sentinel::getUser();
        if ($user->role->role_id != 4) {
            abort(403);
        }

        $resignation = ResignationLetter::findOrFail($id);

        // Prevent self-approval
        if ($resignation->user_id == $user->id) {
            abort(403, 'You cannot approve or decline your own resignation.');
        }

        if ($request->action == 'approve') {
            $resignation->update([
                'status' => 'manager_approved',
                'manager_id' => $user->id,
                'manager_approved_at' => now(),
                'manager_comment' => $request->comment,
            ]);
            Flash::success('Resignation approved by manager.');
        } elseif ($request->action == 'decline') {
            $resignation->update([
                'status' => 'declined',
                'manager_id' => $user->id,
                'manager_approved_at' => now(),
                'manager_comment' => $request->comment,
            ]);
            Flash::success('Resignation declined by manager.');
        }

        return redirect()->back();
    }

    // Admin approve/decline
    public function adminApprove(Request $request, $id)
    {
        $user = Sentinel::getUser();
        if ($user->role->role_id != 1) {
            abort(403);
        }

        $resignation = ResignationLetter::findOrFail($id);

        // Prevent self-approval
        if ($resignation->user_id == $user->id) {
            abort(403, 'You cannot approve or decline your own resignation.');
        }

        if ($request->action == 'approve') {
            $resignation->update([
                'status' => 'admin_approved',
                'admin_id' => $user->id,
                'admin_approved_at' => now(),
                'admin_comment' => $request->comment,
            ]);

            // Deactivate the user account
            $resignation->user->update(['blocked' => 1]);

            Flash::success('Resignation approved by admin. User account has been deactivated.');
        } elseif ($request->action == 'decline') {
            $resignation->update([
                'status' => 'declined',
                'admin_id' => $user->id,
                'admin_approved_at' => now(),
                'admin_comment' => $request->comment,
            ]);
            Flash::success('Resignation declined by admin.');
        }

        return redirect()->back();
    }

    // Approved resignations
    public function approved()
    {
        $user = Sentinel::getUser();
        $approved = [];

        if ($user->role->role_id == 1) {
            $approved = ResignationLetter::where('status', 'admin_approved')
                ->with(['user', 'manager', 'admin'])
                ->get();
        } elseif ($user->role->role_id == 4) {
            $approved = ResignationLetter::where('status', 'admin_approved')
                ->whereHas('user', function($q) use ($user) {
                    $q->where('office_id', $user->office_id);
                })
                ->with(['user', 'manager', 'admin'])
                ->get();
        }

        return view('resignation.approved', compact('approved'));
    }

    // Declined resignations
    public function declined()
    {
        $user = Sentinel::getUser();
        $declined = [];

        if ($user->role->role_id == 1) {
            $declined = ResignationLetter::where('status', 'declined')
                ->with(['user', 'manager', 'admin'])
                ->get();
        } elseif ($user->role->role_id == 4) {
            $declined = ResignationLetter::where('status', 'declined')
                ->whereHas('user', function($q) use ($user) {
                    $q->where('office_id', $user->office_id);
                })
                ->with(['user', 'manager', 'admin'])
                ->get();
        }

        return view('resignation.declined', compact('declined'));
    }

    // Cancel pending resignation
    public function cancel($id)
    {
        $user = Sentinel::getUser();
        $resignation = ResignationLetter::where('id', $id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'manager_approved'])
            ->firstOrFail();

        $resignation->delete(); // Soft delete or hard delete? Since "not delete", but to disable, perhaps soft delete if model has it.

        Flash::success('Resignation letter cancelled successfully.');
        return redirect()->route('resignation.my');
    }

    // Show details
    public function show($id)
    {
        $resignation = ResignationLetter::with(['user', 'manager', 'admin'])->findOrFail($id);
        $user = Sentinel::getUser();

        // Check permissions
        if ($user->id != $resignation->user_id &&
            $user->role->role_id != 1 &&
            !($user->role->role_id == 4 && $resignation->user->office_id == $user->office_id)) {
            abort(403);
        }

        if (request()->ajax()) {
            $html = view('resignation.partials.show_content', compact('resignation'))->render();
            return response()->json(['html' => $html]);
        }

        return view('resignation.show', compact('resignation'));
    }
}
