<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\User;
use App\Mail\SendSingleEmail;
use App\Models\Office;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Laracasts\Flash\Flash;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function create()
    {
        $user = Sentinel::getUser();

        // enforce max 3 open tickets per user
        $openCount = Ticket::where('opened_by', $user->id)->where('status', 'open')->count();
        // if ($openCount >= 3) {
        //     Flash::error('You already have 3 open tickets. Please resolve or close an existing ticket before creating a new one.');
        //     return redirect()->route('ticket.index');
        // }

        $users = User::all();
        $offices = \App\Models\Office::all();
        $roles = \DB::table('roles')->select('id', 'name')->get();
        $categories = Schema::hasTable('ticket_categories') ? \App\Models\TicketCategory::all() : collect();

        return view('ticket.create', compact('users', 'offices', 'roles', 'categories', 'openCount'));
    }

    public function create_dashboard_ticket(){
        $user = Sentinel::getUser();

        // enforce max 3 open tickets per user
        $openCount = Ticket::where('opened_by', $user->id)->where('status', 'open')->count();

        $users = User::all();
        $offices = \App\Models\Office::all();
        $roles = \DB::table('roles')->select('id', 'name')->get();
        $categories = Schema::hasTable('ticket_categories') ? \App\Models\TicketCategory::all() : collect();

        return view('ticket.create_dashboard_ticket',compact('users', 'offices', 'roles', 'categories', 'openCount'));

    }

    public function index()
    {
        $user = Sentinel::getUser();

        $assignedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy', 'issueCategory'])
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get();

        // tickets assigned to this user that are resolved or closed
        $assignedClosedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy', 'issueCategory'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->orderBy('datetime_close', 'desc')
            ->get()
            ->map(function ($ticket) {
                $ticket->opened_by_office_id = $ticket->openedBy->office_id ?? null;
                $ticket->assigned_to_name = $ticket->assignedTo ? ($ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name) : 'Unassigned';
                $ticket->issue_category_name = $ticket->issueCategory->name ?? 'Uncategorized';
                return $ticket;
            });

        $myTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy', 'issueCategory'])
            ->where('opened_by', $user->id)
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        // tickets opened by this user that are resolved
        $myResolvedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy', 'issueCategory'])
            ->where('opened_by', $user->id)
            ->where('status', 'resolved')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                $ticket->opened_by_office_id = $ticket->openedBy->office_id ?? null;
                $ticket->assigned_to_name = $ticket->assignedTo ? ($ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name) : 'Unassigned';
                $ticket->issue_category_name = $ticket->issueCategory->name ?? 'Uncategorized';
                return $ticket;
            });

        // tickets opened by this user that are already closed
        $myClosedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy', 'issueCategory'])
            ->where('opened_by', $user->id)
            ->where('status', 'closed')
            ->orderBy('datetime_close', 'desc')
            ->get()
            ->map(function ($ticket) {
                $ticket->opened_by_office_id = $ticket->openedBy->office_id ?? null;
                $ticket->assigned_to_name = $ticket->assignedTo ? ($ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name) : 'Unassigned';
                $ticket->issue_category_name = $ticket->issueCategory->name ?? 'Uncategorized';
                return $ticket;
            });

        $users = User::all();
        $offices = \App\Models\Office::all();
        $roles = \DB::table('roles')->select('id', 'name')->get();
        $categories = Schema::hasTable('ticket_categories') ? \App\Models\TicketCategory::all() : collect();

        // count open tickets created by this user
        $openCount = Ticket::where('opened_by', $user->id)->where('status', 'open')->count();

        // Get year filter from request
        $selectedYear = request()->get('year', null);
        
        // Build base query for ALL tickets (not paginated) - for charts
        $chartTicketsQuery = Ticket::with(['openedBy.office', 'assignedTo', 'closedBy', 'issueCategory']);
        
        // Apply year filter if selected
        if ($selectedYear) {
            $chartTicketsQuery->whereYear('created_at', $selectedYear);
        }
        
        // Get ALL tickets for chart calculations (not paginated)
        $chartTickets = $chartTicketsQuery->get();

        // Dashboard metrics - calculated from chartTickets (filtered by year)
        $totalTickets = $chartTickets->count();
        $openTicketsCount = $chartTickets->where('status', 'open')->count();
        $resolvedTicketsCount = $chartTickets->where('status', 'resolved')->count();
        $closedTicketsCount = $chartTickets->where('status', 'closed')->count();
        
        // SLA Compliance - based on RESOLVED and CLOSED tickets
        $resolvedForSla = $chartTickets->whereIn('status', ['resolved', 'closed']);
        $slaMetCount = $resolvedForSla->where('sla_met', true)->count();
        $resolvedForSlaCount = $resolvedForSla->count();
        $slaCompliancePercent = $resolvedForSlaCount > 0 ? round(($slaMetCount / $resolvedForSlaCount) * 100) . '%' : '—';

        $dashboardTotals = compact('totalTickets', 'openTicketsCount', 'resolvedTicketsCount', 'closedTicketsCount', 'slaCompliancePercent');

        // include all tickets for all users (paginated) - for display purposes
        $allTickets = Ticket::with(['openedBy.office', 'assignedTo', 'closedBy', 'issueCategory'])->orderBy('created_at', 'desc')->paginate(50);

        // SLA Compliance chart data - based on RESOLVED and CLOSED tickets
        $slaData = [
            'met' => $resolvedForSla->where('sla_met', true)->count(),
            'not_met' => $resolvedForSla->where('sla_met', false)->count(),
        ];

        // Tickets by Office - uses all tickets
        $officeData = $chartTickets->groupBy(function ($ticket) {
            return $ticket->openedBy && $ticket->openedBy->office ? $ticket->openedBy->office->name : 'Unknown';
        })->map(function ($group) {
            return $group->count();
        })->sortDesc();

        // Tickets by Issue Category - uses all tickets
        $categoryData = $chartTickets->groupBy(function ($ticket) {
            return $ticket->issueCategory->name ?? 'Uncategorized';
        })->map(function ($group) {
            return $group->count();
        })->sortDesc();

        // Tickets Opened Over Time - uses all tickets
        $openData = $chartTickets->groupBy(function ($ticket) {
            return \Carbon\Carbon::parse($ticket->datetime_open)->format('Y-m');
        })->map(function ($group) {
            return $group->count();
        })->sortKeys();

        // Average Days to Close by Office - uses all closed/resolved tickets
        $closeData = $chartTickets->whereIn('status', ['resolved', 'closed'])->whereNotNull('datetime_close')->groupBy(function ($ticket) {
            return $ticket->openedBy->office->name ?? 'Unknown';
        })->map(function ($group) {
            $totalDays = $group->sum(function ($ticket) {
                return \Carbon\Carbon::parse($ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->datetime_close));
            });
            return $group->count() > 0 ? round($totalDays / $group->count(), 1) : 0;
        })->sortDesc();

        // Tickets by Status - uses all tickets
        $statusData = $chartTickets->groupBy('status')->map(function ($group) {
            return $group->count();
        });

        // Get available years for filter dropdown
        $availableYears = Ticket::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // check if admin
        $isAdmin = false;
        try {
            $isAdmin = $user && $user->roles()->pluck('id')->contains(1);
        } catch (\Exception $e) {
        }

        return view('ticket.index', compact('totalTickets','assignedTickets', 'assignedClosedTickets', 'myTickets', 'myResolvedTickets', 'myClosedTickets', 'users', 'offices', 'roles', 'categories', 'openCount', 'dashboardTotals', 'allTickets', 'isAdmin', 'slaData', 'officeData', 'categoryData', 'openData', 'closeData', 'statusData', 'selectedYear', 'availableYears'));
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required',
                'priority' => 'required',
                'department' => 'nullable|string',
                'issue_category_id' => 'nullable|exists:ticket_categories,id',
                'sla_days' => 'nullable|integer|min:0',
                'due_date' => 'nullable|date',
            ]);

            // enforce max 3 open tickets per user
            $user = Sentinel::getUser();
            $office = Office::where('id',$user->office_id)->first();
            $openCount = Ticket::where('opened_by', $user->id)->where('status', 'open')->count();
            // if ($openCount >= 3) {
            //     Flash::error('You already have 3 open tickets. Please resolve or close an existing ticket before creating a new one.');
            //     return redirect()->back()->withInput();
            // }

            $ticket = new Ticket();
            $ticket->name = $request->name;
            $ticket->description = $request->description;
            // prefer priority from request, otherwise fallback to category default
            $priority = $request->priority;
            if (!$priority && $request->issue_category_id) {
                $cat = \App\Models\TicketCategory::find($request->issue_category_id);
                if ($cat && $cat->priority_default)
                    $priority = strtolower($cat->priority_default);
            }
            $ticket->priority = $priority ?? 'medium';
            $ticket->department = $request->department ?? 'Administration';
            $ticket->issue_category_id = $request->issue_category_id ?: null;
            $ticket->sla_days = $request->filled('sla_days') ? intval($request->sla_days) : null;
            $ticket->opened_by = $user->id;
            $ticket->datetime_open = now();
            $ticket->date_raised = now();
            $ticket->stage = 'Not started';
            $ticket->status = 'open';

            // also allow updating assigned_to from store form (if provided)
            if ($request->has('assigned_to')) {
                $ticket->assigned_to = $request->assigned_to;
                $ticket->assigned_by = Sentinel::getUser()->id;
            }

            // compute due_date if sla_days present and due_date not manually provided
            if ($ticket->sla_days && !$request->filled('due_date')) {
                $ticket->due_date = now()->addDays(intval($ticket->sla_days));
            } else if ($request->filled('due_date')) {
                $ticket->due_date = $request->due_date;
            }

            $ticket->save();

  $category_name = \App\Models\TicketCategory::find($request->issue_category_id);
         Http::post('https://notifications.whencefinancesystem.com/emit', [
                'event' => 'ticket.created',
                'data' => [
                    'name' => $request->name,
                    'user' => $user->first_name . ' ' . $user->last_name,
                    'office' => $office->name,
                    'type' => $category_name->name
                ]
            ]);

            // Send notification emails to admins
            // $notificationEmails = config('ticket.notification_emails', []);
            // foreach ($notificationEmails as $email) {
            //     try {
            //         Mail::to($email)->send(new SendSingleEmail('New Ticket Created | ' . $ticket->ticket_number, 'A new ticket has been created: ' . $ticket->ticket_number . ' by ' . $user->first_name . ' ' . $user->last_name . '. {link}'));
            //     } catch (\Exception $e) {
            //         \Log::error('Failed to send admin notification email: ' . $e->getMessage());
            //     }
            // }

            // Send confirmation email to user
            // try {
            //     Mail::to($user->email)->send(new SendSingleEmail('Ticket Submitted Successfully', 'Your ticket "' . $ticket->ticket_number . '" has been submitted successfully. {link}'));
            // } catch (\Exception $e) {
            //     \Log::error('Failed to send user confirmation email: ' . $e->getMessage());
            // }

            Flash::success(trans('general.successfully_saved'));
            return redirect('/ticket');
        } catch (\Throwable $th) {
            dd($th);
            Flash::success('Error occurred: ' . $th->getMessage());
            return redirect('/ticket');
        }
    }


    public function store_dashboard_ticket(Request $request)
{
   

        $request->validate([
            'summary_issue' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required',
            'department' => 'nullable|string',
            'issue_category_id' => 'nullable|exists:ticket_categories,id',
            'sla_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
        ]);

        $user = Sentinel::getUser();

        // enforce max open tickets (if needed later)
        $openCount = Ticket::where('opened_by', $user->id)
            ->where('status', 'open')
            ->count();

        $ticket = new Ticket();

        /*
        |--------------------------------------------------------------------------
        | TITLE: Summary Figure with Issue
        |--------------------------------------------------------------------------
        */
        $ticket->name = 'Dashboard Discrepancy - ' . $request->summary_issue;

        /*
        |--------------------------------------------------------------------------
        | DESCRIPTION: Transaction / Loan Entries
        |--------------------------------------------------------------------------
        */
        $ticket->description = $request->description;

        /*
        |--------------------------------------------------------------------------
        | PRIORITY (fallback to category default)
        |--------------------------------------------------------------------------
        */
        $priority = $request->priority;
        if (!$priority && $request->issue_category_id) {
            $cat = \App\Models\TicketCategory::find($request->issue_category_id);
            if ($cat && $cat->priority_default) {
                $priority = strtolower($cat->priority_default);
            }
        }

        $ticket->priority = $priority ?? 'medium';
        $ticket->department = $request->department ?? 'Administration';
        $ticket->issue_category_id = $request->issue_category_id ?: null;
        $ticket->sla_days = $request->filled('sla_days') ? intval($request->sla_days) : null;
        $ticket->opened_by = $user->id;
        $ticket->datetime_open = now();
        $ticket->date_raised = now();
        $ticket->stage = 'Not started';
        $ticket->status = 'open';

        /*
        |--------------------------------------------------------------------------
        | Assignment
        |--------------------------------------------------------------------------
        */
        if ($request->filled('assigned_to')) {
            $ticket->assigned_to = 1899; //$request->assigned_to;
            $ticket->assigned_by = $user->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Due Date Handling
        |--------------------------------------------------------------------------
        */
        if ($ticket->sla_days && !$request->filled('due_date')) {
            $ticket->due_date = now()->addDays((int) $ticket->sla_days);
        } elseif ($request->filled('due_date')) {
            $ticket->due_date = $request->due_date;
        }

        $ticket->save();


        $user->verified_numbers = 'pending';
        $user->save();

        Flash::success(trans('general.successfully_saved'));
        return redirect('/ticket');

 
}


    public function update(Request $request, $id)
    {

        $ticket = Ticket::findOrFail($id);

        if ($request->has('status')) {
            $ticket->status = $request->status;

            if ($request->status == 'closed') {
                $request->validate([
                    'rating' => 'required|integer|min:1|max:5',
                    'predefined_remarks' => 'nullable|string',
                    'custom_remarks' => 'nullable|string',
                ]);

                $ticket->datetime_close = now();
                $ticket->date_closed = now();
                $ticket->closed_by = Sentinel::getUser()->id;
                $ticket->rating = $request->rating;

                // Combine predefined and custom remarks
                $remarks = [];
                if ($request->filled('predefined_remarks')) {
                    $remarks[] = $request->predefined_remarks;
                }
                if ($request->filled('custom_remarks')) {
                    $remarks[] = $request->custom_remarks;
                }
                $ticket->remarks = implode(' ', $remarks);

                // compute SLA met
                if ($ticket->due_date && $ticket->date_closed) {
                    $ticket->sla_met = \Carbon\Carbon::parse($ticket->date_closed)->lessThanOrEqualTo(\Carbon\Carbon::parse($ticket->due_date));
                } else {
                    $ticket->sla_met = false;
                }
            } elseif ($request->status == 'resolved') {
                // record resolved time
                $ticket->datetime_close = now();
                $ticket->date_closed = now();
                $ticket->closed_by = Sentinel::getUser()->id;
                $ticket->resolution_comment = $request->resolution_comment;

                if ($ticket->due_date && $ticket->date_closed) {
                    $ticket->sla_met = \Carbon\Carbon::parse($ticket->date_closed)->lessThanOrEqualTo(\Carbon\Carbon::parse($ticket->due_date));
                }
            } elseif ($request->status == 'in_progress') {
                $ticket->status = 'in_progress';
            }
        }

        // also allow updating assigned_to from store form (if provided)
        if ($request->has('assigned_to')) {
            $ticket->assigned_to = $request->assigned_to;
            $ticket->assigned_by = Sentinel::getUser()->id;
            $ticket->stage = 'Started';
        }

        $ticket->save();

        // Send notification emails after operation
        $currentUser = Sentinel::getUser();
        $operation = '';

        if ($request->has('assigned_to')) {
            $operation = 'assignment';
            $assignee = User::find($ticket->assigned_to);
            $assigner = $currentUser;

            // Email to assignee
            if ($assignee && $assignee->email) {
                try {
                    // Mail::to($assignee->email)->send(new SendSingleEmail(
                    //     'Ticket Assigned to You',
                    //     'You have been assigned a ticket: "' . $ticket->name . '" (' . $ticket->ticket_number . ') by ' . $assigner->first_name . ' ' . $assigner->last_name . '. Please check your ticket dashboard. {link}'
                    // ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send assignment email to assignee: ' . $e->getMessage());
                }
            }
        }

        if ($request->has('status') && $request->status == 'resolved') {
            // Email to ticket opener
            $openedBy = $ticket->openedBy;
            if ($openedBy && $openedBy->email) {
                try {
                    // Mail::to($openedBy->email)->send(new SendSingleEmail(
                    //     'Ticket Resolved',
                    //     'Your ticket "' . $ticket->name . '" (' . $ticket->ticket_number . ') has been resolved. {link}'
                    // ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send resolution email to opener: ' . $e->getMessage());
                }
            }
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function officesByParent(Request $request)
    {
        $parentId = $request->query('parent_id');

        if (!$parentId) {
            return response()->json(['success' => false, 'message' => 'Missing parent_id', 'offices' => []]);
        }

        try {
            $offices = \App\Models\Office::where('parent_id', $parentId)->get();

            return response()->json(['success' => true, 'offices' => $offices]);
        } catch (\Exception $e) {
            \Log::error('officesByParent error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'offices' => []], 500);
        }
    }

    public function usersByOfficeRole(Request $request)
    {
        $officeId = $request->query('office_id');
        $roleId = $request->query('role_id');
        $type = $request->query('type', 'new'); // 'new' for new ticket, 'assign' for assign modal

        if (!$officeId || !$roleId) {
            return response()->json(['success' => false, 'message' => 'Missing office_id or role_id', 'users' => []]);
        }

        try {

            // Optional: restrict to predefined allowed user ids (set in config/ticket.php)
            $configKey = $type === 'new' ? 'allowed_user_ids' : 'allowed_assigning_ids';
            $allowedUserIds = config('ticket.' . $configKey, []);

            $query = \DB::table('users')
                ->join('role_users', 'users.id', '=', 'role_users.user_id')
                ->where('users.office_id', $officeId)
                ->where('role_users.role_id', $roleId)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email');

            if (!empty($allowedUserIds) && is_array($allowedUserIds)) {
                $query->whereIn('users.id', $allowedUserIds);
            }

            $users = $query->get();

            $users = $users->map(function ($u) {
                $u->display = trim(($u->first_name) . ' ' . ($u->last_name ?? ''));
                return $u;
            });

            return response()->json(['success' => true, 'users' => $users]);
        } catch (\Exception $e) {
            \Log::error('usersByOfficeRole error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'users' => []], 500);
        }
    }

    public function reassign(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'assigned_to' => 'required|exists:users,id',
            ]);

            $ticket = Ticket::findOrFail($request->ticket_id);
            $previousAssignee = $ticket->assigned_to;
            $newAssignee = User::findOrFail($request->assigned_to);
            
            // Update the ticket assignment
            $ticket->assigned_to = $request->assigned_to;
            $ticket->assigned_by = Sentinel::getUser()->id;
            $ticket->stage = 'Started';
            
            // If there are remarks, append them to the existing remarks
            if ($request->filled('remarks')) {
                $existingRemarks = $ticket->remarks ?? '';
                $newRemark = 'Reassigned from ' . ($previousAssignee ? User::find($previousAssignee)->first_name ?? 'Unknown' : 'Unassigned') . ' to ' . $newAssignee->first_name . ': ' . $request->remarks;
                $ticket->remarks = $existingRemarks . '\n' . $newRemark;
            }
            
            $ticket->save();

            // Email notifications are disabled due to SSL configuration issues
            // Uncomment below if you want to enable email notifications
            /*
            // Send notification email to new assignee
            $notificationEmails = config('ticket.notification_emails', []);
            foreach ($notificationEmails as $email) {
                try {
                    Mail::to($email)->send(new SendSingleEmail('Ticket Reassigned | ' . $ticket->ticket_number, 'Ticket has been reassigned: ' . $ticket->ticket_number . '. {link}'));
                } catch (\Exception $e) {
                    \Log::error('Failed to send reassignment notification email: ' . $e->getMessage());
                }
            }
            */

            Flash::success('Ticket has been reassigned successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Flash::error('Error reassigning ticket: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function reject(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'comments' => 'required|string',
            ]);

            $ticket = Ticket::findOrFail($request->ticket_id);

            $ticket->status = 'closed';
            $ticket->stage = 'Closed';
            $ticket->datetime_close = now();
            $ticket->date_closed = now();
            $ticket->closed_by = Sentinel::getUser()->id;
            $ticket->resolution_comment = $request->comments;

            $ticket->save();

            Flash::success('Ticket has been rejected and closed successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Flash::error('Error rejecting ticket: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
