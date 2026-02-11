Main Menu Links
1. Dashboard
Link: /dashboard
2. Tickets
Link: /ticket
3. My Cycle
Link: /user/cycle
4. Ledger
General Ledger - route('ledger.summary')
Branch Ledgers - route('ledger.transactions')
5. Branch Deposits
Link: /user/branch_deposits
6. Approvals
Loans Pending - /loan/managers_pending_approval
Top Ups Pending Approval - /loan/top_up_approvals
Transaction Approvals - /loan/transaction_approvals
Reloan Approvals - /loan/reloan_approvals
Waiver Approvals - route('loan.waiver_approvals')
Charge Approvals - route('loan.charge_approvals')
Clients Pending Approval - /client/managers_pending_approval
Advances Pending Approvals - route('advances.pending_approvals')
Advance-TopUps Approvals - route('advances.topups_pending_approval')
Pending Leave Approvals - route('leave.pending_leave_approvals')
7. Payroll Loan Applications
Pending approval - /loan/payroll_loan/pending_list
Approved - /loan/payroll_loan/approved_list
Declined - /loan/payroll_loan/declined_list
8. Collections
Collections - /loan/new_collections
Expected collections - /loan/expected_collections
My Collections - /loan/my_collections
My Expected Collections - /loan/my_expected_collections
9. Recoveries
Branch uncollected - /loan/branch_uncollected
10. Leaderboard
Link: /user/leaderboard
11. Appraisal
Forms - /user/appraisal_forms
My Appraisal - /user/my_appraisal_forms
12. My Loan Applications
Link: /loan/my_applications/data
13. Branches
View Branches - /office/data
Add Branch - /office/create
14. Clients
View Clients - /client/data
My Clients - /client/my_clients
Branch Clients - /client/branch_clients
Clients Pending Approval - /client/pending_approval
Clients Closed - /client/closed
Clients Inactive - /client/clients_inactive
Clients Blacklisted - /client/clients_blacklisted
Clients Declined - /client/declined
Add Client - /client/create
View Groups - /group/data
Groups Pending Approval - /group/pending_approval
Groups Declined - /group/groups_declined
Groups Closed - /group/groups_closed
Add Group - /group/create
15. Loans
Active Loans - /loan/data
My Active Loans - /loan/my_loans
Branch Active Loans - /loan/branch_loans
Pending Approval - /loan/pending_approval
Awaiting Disbursement - /loan/awaiting_disbursement
Loans Declined - /loan/loans_declined
Loans Written Off - /loan/loans_written_off
Loans Closed - /loan/loans_closed
Loans Rescheduled - /loan/loans_rescheduled
Loan Applications - /loan/application/data
Add Loan - /loan/create
Manage Loan Products - /loan/product/data
Loan Calculator - /loan/calculator/create
16. Company Policies
View Policies - route('policies.view_policies')
User Responses - route('policies.user_responses')
Add Policies - route('policies.add_policies')
17. Accounting
Chart of Accounts - /accounting/gl_account/data
Journals - /accounting/journal/data
Add Journal Entry - /accounting/journal/create
Reconciliation - /accounting/reconciliation/data
Close Periods - /accounting/period/data
18. Reports
Client Reports - /report/client_report
Loan Reports - /report/loan_report
Financial Reports - /report/financial_report
Organisation Reports - /report/company_report
Savings Reports - /report/savings_report
Report Scheduler - /report/report_scheduler/data
19. Advances
Apply for Advance - route('advances.apply')
My Advances - route('advances.my_advances')
Active Advances - route('advances.active_advances')
Pending Approvals - route('advances.pending_approvals')
TopUps Pending Approval - route('advances.topups_pending_approval')
Declined Advances - route('advances.declined_advances')
Closed Advances - route('advances.closed_advances')
20. Annual Leave
My Leave Days - route('leave.my_leave_days')
Active Leave - route('leave.active_leave')
Pending Leave Approvals - route('leave.pending_leave_approvals')
Declined Leave - route('leave.declined_leave')
21. Communication
View Campaigns - /communication/data
Create Campaign - /communication/create
22. Assets
View Assets - /asset/data
Add Asset - /asset/create
Manage Asset Types - /asset/type/data
23. Expenses
View Expenses - /expense/data
Add Expense - /expense/create
Manage Expense Types - /expense/type/data
Manage Budget - /expense/budget/data
Budget Report - /expense/budget/report
24. Other Income
View Other Income - /other_income/data
Add Other Income - /other_income/create
Manage Other Income Types - /other_income/type/data
25. Payroll
Add payroll - /payroll/create_wage_bill
Payroll List - /payroll/payroll_list
Payroll Approvals - /payroll/payroll_pending_approval
Manage Payroll Templates - /payroll/template
26. Performance Information
Link: /user/performance_information
27. Loan Consultant Information
Link: /payroll/lc_information
28. Users
View Users - /user/data
View Inactive Users - route('user.inactive')
View Client Users - /user/client_users/data
Manage Roles - /user/role/data
Add User - /user/create
29. My Payslips
Link: /payroll/mypayslips
30. Audit Trail
Link: /audit_trail/data
31. Settings
General - /setting/general
Organisation - /setting/organisation
System fail safes - /setting/fail_safe
Run in Server SSH
---Poilies Page Update
php artisan migrate --path=database/migrations/2025_11_19_144018_create_user_policy_responses_table.php

// if (in_array($user->role->role_id, [3,4,6])) {
//     $offices = Office::where('id', $user->office_id)->get();
// } 

// elseif ($user->role->role_id == 6) {
//     $offices = Office::where('province_id', $user->province_id)->get();
// } else {
//     $offices = collect();
// }
