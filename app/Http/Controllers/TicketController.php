<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\User;
use App\Mail\SendSingleEmail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        if ($openCount >= 3) {
            Flash::error('You already have 3 open tickets. Please resolve or close an existing ticket before creating a new one.');
            return redirect()->route('tickets.index');
        }

        $users = User::all();
        $offices = \App\Models\Office::all();
        $roles = \DB::table('roles')->select('id','name')->get();
        $categories = \App\Models\TicketCategory::all();

        return view('ticket.create', compact('users', 'offices', 'roles', 'categories', 'openCount'));
    }

    public function index()
    {
        $user = Sentinel::getUser();

        $assignedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy'])
            ->where('assigned_to', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // tickets assigned to this user that are closed (resolved by this user)
        $assignedClosedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy'])
            ->where('assigned_to', $user->id)
            ->where('status', 'closed')
            ->orderBy('datetime_close', 'desc')
            ->get();

        $myTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy'])
            ->where('opened_by', $user->id)
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        // tickets opened by this user that are already closed
        $myClosedTickets = Ticket::with(['openedBy', 'assignedTo', 'closedBy'])
            ->where('opened_by', $user->id)
            ->where('status', 'closed')
            ->orderBy('datetime_close', 'desc')
            ->get();

        $users = User::all();
        $offices = \App\Models\Office::all();
        $roles = \DB::table('roles')->select('id','name')->get();
        $categories = \App\Models\TicketCategory::all();

        // count open tickets created by this user
        $openCount = Ticket::where('opened_by', $user->id)->where('status', 'open')->count();

        // Dashboard metrics (site-wide)
        $totalTickets = Ticket::count();
        $openTicketsCount = Ticket::where('status', 'open')->count();
        $closedTicketsCount = Ticket::where('status', 'closed')->count();
        $slaMetCount = Ticket::where('status', 'closed')->where('sla_met', true)->count();
        $slaCompliancePercent = $closedTicketsCount ? round(($slaMetCount / $closedTicketsCount) * 100) . '%' : '—';

        $dashboardTotals = compact('totalTickets', 'openTicketsCount', 'closedTicketsCount', 'slaCompliancePercent');

        // include all tickets for all users
        $allTickets = Ticket::with(['openedBy','assignedTo','closedBy','issueCategory'])->orderBy('created_at','desc')->get();

        // check if admin
        $isAdmin = false;
        try{
            $isAdmin = $user && $user->roles()->pluck('id')->contains(1);
        } catch(\Exception $e){ }

        return view('ticket.index', compact('assignedTickets', 'assignedClosedTickets', 'myTickets', 'myClosedTickets', 'users', 'offices', 'roles', 'categories', 'openCount', 'dashboardTotals', 'allTickets', 'isAdmin'));
    }

    public function store(Request $request)
    {
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
        $openCount = Ticket::where('opened_by', $user->id)->where('status', 'open')->count();
        if ($openCount >= 3) {
            Flash::error('You already have 3 open tickets. Please resolve or close an existing ticket before creating a new one.');
            return redirect()->back()->withInput();
        }

        $ticket = new Ticket();
        $ticket->name = $request->name;
        $ticket->description = $request->description;
        // prefer priority from request, otherwise fallback to category default
        $priority = $request->priority;
        if(!$priority && $request->issue_category_id){
            $cat = \App\Models\TicketCategory::find($request->issue_category_id);
            if($cat && $cat->priority_default) $priority = strtolower($cat->priority_default);
        }
        $ticket->priority = $priority ?? 'medium';
        $ticket->department = $request->department ?? 'Administration';
        $ticket->assigned_to = $request->assigned_to;
        $ticket->issue_category_id = $request->issue_category_id ?: null;
        $ticket->sla_days = $request->sla_days ?: ($cat->sla_days ?? null);
        $ticket->opened_by = $user->id;
        $ticket->datetime_open = now();
        $ticket->date_raised = now();
        $ticket->status = 'open';

        // compute due_date if sla_days present and due_date not manually provided
        if($ticket->sla_days && !$request->due_date){
            $ticket->due_date = now()->addDays(intval($ticket->sla_days));
        } else if($request->due_date){
            $ticket->due_date = $request->due_date;
        }

        $ticket->save();

        // // Send notification emails to admins
        // $notificationEmails = config('ticket.notification_emails', []);
        // foreach ($notificationEmails as $email) {
        //     try {
        //         Mail::to($email)->send(new SendSingleEmail('New Ticket Created', 'A new ticket has been created: ' . $ticket->name . ' by ' . $user->first_name . ' ' . $user->last_name));
        //     } catch (\Exception $e) {
        //         \Log::error('Failed to send admin notification email: ' . $e->getMessage());
        //     }
        // }

        // // Send confirmation email to user
        // try {
        //     Mail::to($user->email)->send(new SendSingleEmail('Ticket Submitted Successfully', 'Your ticket "' . $ticket->name . '" has been submitted successfully.'));
        // } catch (\Exception $e) {
        //     \Log::error('Failed to send user confirmation email: ' . $e->getMessage());
        // }

        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($request->has('status')) {
            $ticket->status = $request->status;

            if ($request->status == 'closed') {
                $request->validate([
                    'rating' => 'required|integer|min:1|max:5',
                    'remarks' => 'nullable|string',
                ]);

                $ticket->datetime_close = now();
                $ticket->date_closed = now();
                $ticket->closed_by = Sentinel::getUser()->id;
                $ticket->rating = $request->rating;
                $ticket->remarks = $request->remarks;

                // compute SLA met
                if($ticket->due_date && $ticket->date_closed){
                    $ticket->sla_met = \Carbon\Carbon::parse($ticket->date_closed)->lessThanOrEqualTo(\Carbon\Carbon::parse($ticket->due_date));
                } else {
                    $ticket->sla_met = false;
                }
            } elseif ($request->status == 'resolved') {
                // record resolved time
                $ticket->datetime_close = now();
                $ticket->date_closed = now();
                $ticket->closed_by = Sentinel::getUser()->id;

                if($ticket->due_date && $ticket->date_closed){
                    $ticket->sla_met = \Carbon\Carbon::parse($ticket->date_closed)->lessThanOrEqualTo(\Carbon\Carbon::parse($ticket->due_date));
                }
            } elseif ($request->status == 'in_progress') {
                $ticket->status = 'in_progress';
            }
        }

        // also allow updating assigned_to from store form (if provided)
        if ($request->has('assigned_to')) {
            // Only admins may assign tickets via this endpoint
            $currentUser = Sentinel::getUser();
            $isAdmin = false;
            try{ $isAdmin = $currentUser && $currentUser->roles()->pluck('id')->contains(1); } catch(\Exception $e){ }
            if(!$isAdmin){
                Flash::error('Only administrators can assign tickets.');
                return redirect()->back();
            }

            $assignedTo = $request->assigned_to ?: null;
            if($assignedTo){
                $u = User::find($assignedTo);
                if(!$u){
                    Flash::error('Selected user not found.');
                    return redirect()->back();
                }
                // basic validation: if office/role provided, ensure the user matches
                if($request->has('assign_office') && $request->assign_office){
                    if($u->office_id != $request->assign_office){
                        Flash::error('Selected user does not belong to the chosen office.');
                        return redirect()->back();
                    }
                }
                if($request->has('assign_role') && $request->assign_role){
                    $hasRole = \DB::table('role_users')->where('user_id',$u->id)->where('role_id',$request->assign_role)->exists();
                    if(!$hasRole){
                        Flash::error('Selected user does not have the chosen role.');
                        return redirect()->back();
                    }
                }
            }

            $ticket->assigned_to = $assignedTo;
        }

        $ticket->save();

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
            $configKey = $type === 'assign' ? 'allowed_assigning_ids' : 'allowed_user_ids';
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
                $u->display = trim(($u->first_name ?? $u->name) . ' ' . ($u->last_name ?? ''));
                return $u;
            });

            return response()->json(['success' => true, 'users' => $users]);
        } catch (\Exception $e) {
            \Log::error('usersByOfficeRole error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'users' => []], 500);
        }
    }
}
