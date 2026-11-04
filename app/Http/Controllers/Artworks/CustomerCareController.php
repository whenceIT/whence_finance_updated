<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use App\Models\Artworks\CustomerCareCase;
use App\Models\Artworks\CustomerCareActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerCareController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerCareCase::orderBy('created_at', 'desc');
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('case_reference', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('nrc_number', 'like', "%{$search}%");
            });
        }
        
        $cases = $query->paginate(20);
        return view('artworks.customer-care.index', compact('cases'));
    }

    public function create()
    {
        return view('artworks.customer-care.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'client_name' => 'required|string|max:255',
            'complaint_type' => 'required|string',
            'complaint_description' => 'required|string',
            'branch_id' => 'nullable|integer',
            'nrc_number' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'date_reported' => 'required|date',
        ]);

        $caseReference = 'CC-' . date('Y') . '-' . str_pad(CustomerCareCase::count() + 1, 6, '0', STR_PAD_LEFT);

        $case = CustomerCareCase::create([
            'case_reference' => $caseReference,
            'branch_id' => $request->branch_id,
            'client_name' => $request->client_name,
            'nrc_number' => $request->nrc_number,
            'contact_number' => $request->contact_number,
            'complaint_type' => $request->complaint_type,
            'complaint_description' => $request->complaint_description,
            'reported_by_user_id' => Auth::check() ? Auth::id() : null,
            'date_reported' => $request->date_reported,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'status' => 'Open',
            'notes' => $request->notes,
        ]);

        return redirect()->route('customer-care.index')->with('success', 'Customer care case registered successfully. Case Reference: ' . $caseReference);
    }

    public function show($id)
    {
        $case = CustomerCareCase::findOrFail($id);
        return view('artworks.customer-care.show', compact('case'));
    }

    public function open()
    {
        $cases = CustomerCareCase::where('status', 'Open')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.customer-care.index', compact('cases'));
    }

    public function inProgress()
    {
        $cases = CustomerCareCase::where('status', 'In Progress')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.customer-care.index', compact('cases'));
    }

    public function resolved()
    {
        $cases = CustomerCareCase::where('status', 'Resolved')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.customer-care.index', compact('cases'));
    }

    public function escalated()
    {
        $cases = CustomerCareCase::where('status', 'Escalated')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.customer-care.index', compact('cases'));
    }

    public function updateCase(Request $request, $id)
    {
        $case = CustomerCareCase::findOrFail($id);
        
        if ($request->has('status')) {
            $case->status = $request->status;
            if ($request->status == 'Resolved') {
                $case->resolution_details = $request->resolution_details;
                $case->resolution_date = $request->resolution_date ?: Carbon::now();
            } elseif ($request->status == 'Escalated') {
                $case->escalated_to = $request->escalated_to;
                $case->escalated_at = Carbon::now();
            } elseif ($request->status == 'In Progress') {
                $case->actions_taken = $request->actions_taken;
            }
        }
        
        $case->actions_taken = $request->actions_taken ?: $case->actions_taken;
        $case->follow_up_actions = $request->follow_up_actions ?: $case->follow_up_actions;
        $case->assigned_to_user_id = $request->assigned_to_user_id ?: $case->assigned_to_user_id;
        $case->save();
        
        return redirect()->back()->with('success', 'Case updated successfully.');
    }

    public function calendar(Request $request)
    {
        $activities = CustomerCareActivity::orderBy('start_date', 'asc')->get();
        return view('artworks.customer-care.calendar', compact('activities'));
    }

    public function storeActivity(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'activity_type' => 'nullable|string',
            'venue' => 'nullable|string',
        ]);

        $activity = CustomerCareActivity::create([
            'title' => $request->title,
            'description' => $request->description,
            'activity_type' => $request->activity_type,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'venue' => $request->venue,
            'responsible_department' => $request->responsible_department,
            'responsible_user_id' => $request->responsible_user_id,
            'status' => 'Scheduled',
            'notes' => $request->notes,
        ]);

        return redirect()->route('customer-care.calendar')->with('success', 'Activity scheduled successfully.');
    }

    public function reports(Request $request)
    {
        $total = CustomerCareCase::count();
        $open = CustomerCareCase::where('status', 'Open')->count();
        $inProgress = CustomerCareCase::where('status', 'In Progress')->count();
        $resolved = CustomerCareCase::where('status', 'Resolved')->count();
        $escalated = CustomerCareCase::where('status', 'Escalated')->count();
        
        $complaintTypes = CustomerCareCase::select('complaint_type', DB::raw('count(*) as count'))
            ->groupBy('complaint_type')
            ->get();
        
        return view('artworks.customer-care.reports', compact('total', 'open', 'inProgress', 'resolved', 'escalated', 'complaintTypes'));
    }
}

