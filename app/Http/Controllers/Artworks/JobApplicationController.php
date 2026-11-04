<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use App\Models\Artworks\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = JobApplication::orderBy('created_at', 'desc');
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nrc_number', 'like', "%{$search}%")
                  ->orWhere('position_applied_for', 'like', "%{$search}%");
            });
        }
        
        $applications = $query->paginate(20);
        return view('artworks.job-applications.index', compact('applications'));
    }

    public function create()
    {
        return view('artworks.job-applications.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nrc_number' => 'required|string',
            'full_name' => 'required|string|max:255',
            'contact_number' => 'required|string',
            'position_applied_for' => 'required|string',
            'recommending_department' => 'nullable|string',
        ]);

        // Check for duplicates
        $exists = JobApplication::where('nrc_number', $request->nrc_number)
            ->whereIn('status', ['Recommended', 'Under Review', 'Approved'])
            ->first();
        
        if ($exists) {
            return redirect()->back()->with('error', 'An active application already exists for this NRC number.')->withInput();
        }

        $application = JobApplication::create([
            'nrc_number' => $request->nrc_number,
            'full_name' => $request->full_name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'position_applied_for' => $request->position_applied_for,
            'recommending_department' => $request->recommending_department ?: 'Creative Artworks and Marketing',
            'recommended_by_user_id' => Auth::check() ? Auth::id() : null,
            'date_recommended' => Carbon::now(),
            'status' => 'Recommended',
            'notes' => $request->notes,
        ]);

        return redirect()->route('job-applications.index')->with('success', 'Job application registered successfully.');
    }

    public function show($id)
    {
        $application = JobApplication::findOrFail($id);
        return view('artworks.job-applications.show', compact('application'));
    }

    public function edit($id)
    {
        $application = JobApplication::findOrFail($id);
        return view('artworks.job-applications.edit', compact('application'));
    }

    public function update(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);
        
        $this->validate($request, [
            'nrc_number' => 'required|string',
            'full_name' => 'required|string|max:255',
            'contact_number' => 'required|string',
            'position_applied_for' => 'required|string',
        ]);

        $application->update($request->all());
        
        return redirect()->route('job-applications.show', $id)->with('success', 'Application updated successfully.');
    }

    public function pending()
    {
        $applications = JobApplication::where('status', 'Under Review')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.job-applications.index', compact('applications'));
    }

    public function approved()
    {
        $applications = JobApplication::where('status', 'Approved')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.job-applications.index', compact('applications'));
    }

    public function employed()
    {
        $applications = JobApplication::where('status', 'Employed')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.job-applications.index', compact('applications'));
    }

    public function qualifying()
    {
        // Applications employed for >= 3 months but not yet rewarded
        $applications = JobApplication::where('status', 'Employed')
            ->where('is_currently_employed', true)
            ->whereNotNull('employment_date')
            ->where('employment_date', '<=', Carbon::now()->subMonths(3))
            ->whereNull('qualifying_completed_at')
            ->orderBy('employment_date', 'asc')
            ->paginate(20);
        return view('artworks.job-applications.index', compact('applications'));
    }

    public function updateStatus(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);
        $status = $request->status;
        
        $application->status = $status;
        
        if ($status == 'Under Review' && $application->status != 'Under Review') {
            // Mark as under review
        } elseif ($status == 'Approved') {
            $application->approved_by = Auth::check() ? Auth::id() : null;
            $application->approved_at = Carbon::now();
            $application->admin_notified_at = Carbon::now();
        } elseif ($status == 'Rejected') {
            $application->rejected_by = Auth::check() ? Auth::id() : null;
            $application->rejected_at = Carbon::now();
            $application->rejection_reason = $request->rejection_reason;
        } elseif ($status == 'Employed') {
            $application->employment_date = $request->employment_date ?: Carbon::now();
            $application->is_currently_employed = true;
            // Check if qualifies for reward (3 months)
            if ($application->employment_date && Carbon::parse($application->employment_date)->diffInMonths(Carbon::now()) >= 3) {
                $application->qualifying_completed_at = Carbon::now();
                $application->reward_notified_at = Carbon::now();
            }
        }
        
        $application->save();
        
        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function markQualifying($id)
    {
        $application = JobApplication::findOrFail($id);
        $application->qualifying_completed_at = Carbon::now();
        $application->reward_notified_at = Carbon::now();
        $application->save();
        
        return redirect()->back()->with('success', 'Marked as qualifying for reward.');
    }

    public function reports()
    {
        $total = JobApplication::count();
        $recommended = JobApplication::where('status', 'Recommended')->count();
        $underReview = JobApplication::where('status', 'Under Review')->count();
        $approved = JobApplication::where('status', 'Approved')->count();
        $rejected = JobApplication::where('status', 'Rejected')->count();
        $employed = JobApplication::where('status', 'Employed')->count();
        $qualifying = JobApplication::whereNotNull('qualifying_completed_at')->whereNull('reward_notified_at')->count();
        
        return view('artworks.job-applications.reports', compact('total', 'recommended', 'underReview', 'approved', 'rejected', 'employed', 'qualifying'));
    }
}

