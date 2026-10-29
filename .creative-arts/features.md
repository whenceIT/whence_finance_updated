# System Development Requirements — Creative Artworks and Marketing Department

## 1. Job Application Recommendation Platform

A module to register, track, and manage job applicants recommended by the Creative Artworks and Marketing Department.

Required features:

- Applicant registration using NRC number, full name, contact details, position applied for, and other relevant information.
- Record the recommending department and date of recommendation.
- Prevent duplicate applicant recommendations where applicable.
- Track application status: Recommended, Under Review, Approved, Rejected, Employed, and Not Employed.
- Notify the Administration Department when an applicant is approved.
- Record employment date and verify whether the applicant remains employed.
- Track the three-month qualifying period for the department's token of appreciation.
- Notify responsible users when the three-month qualifying period is completed.
- Maintain a history of recommendations, employment outcomes, and rewards.
- Generate reports on recommended applicants, approvals, employment rates, and qualifying rewards.

## 2. Daily Reports Module

A module for recording daily income and expenditure and generating branch financial reports.

Required features:

- Record daily income by branch, date, and income category.
- Record daily expenses by branch, date, and expense category.
- Capture transaction descriptions, amounts, and supporting references.
- View, edit, and verify daily entries based on assigned permissions.
- Generate daily, weekly, monthly, and custom-period reports.
- Export reports to Excel.
- Generate a branch report showing:
  - Branch name.
  - Total income.
  - Total expenses.
  - Payments received.
  - Outstanding balances.
  - Net balance.
- Filter reports by branch, date range, and transaction category.
- Maintain an audit trail of financial entries and changes.

## 3. Customer Care Reports Module

A module for recording and monitoring customer complaints, cases handled, and their resolutions.

Required features:

- Register customer care cases with a unique case reference number.
- Record the branch name and client details, including NRC where required.
- Categorise complaints by type.
- Record the complaint description, date reported, and responsible staff member.
- Capture actions taken and resolution details.
- Track case status: Open, In Progress, Resolved, or Escalated.
- Record the date of resolution and any follow-up actions.
- Generate reports on cases attended to, unresolved cases, complaint categories, and resolution rates.
- Filter reports by branch, complaint type, assigned staff, status, and date.
- Export customer care reports to Excel.

### 3.1 Institutional Activities Calendar

- Create and schedule institutional activities, meetings, campaigns, and events.
- Record activity titles, descriptions, dates, times, and venues.
- Assign responsible departments or staff members.
- View activities in monthly, weekly, and daily calendar formats.
- Send reminders and notifications before scheduled activities.
- Edit, reschedule, or cancel activities based on user permissions.

## 4. Conflict Resolution Charges Module

A module for recording customer conflicts, applying resolution charges, and tracking payments from branches.

Required features:

- Register conflict resolution cases against the relevant branch.
- Apply a standard conflict resolution charge of K20.00 per case.
- Record the client, branch, complaint details, date, and assigned officer.
- Track the case status and resolution details.
- Generate charges payable by each branch.
- Notify branches when conflict resolution charges are pending.
- Record payments received against outstanding charges.
- Track paid, partially paid, and outstanding amounts.
- Reflect confirmed payments in the relevant branch income statements or financial reports, according to the approved accounting treatment.
- Generate reports showing charges raised, payments received, and outstanding balances.
- Maintain payment history and an audit trail.

### 4.1 Conflict Management Categories

The system must include the following complaint types:

- Payment amount disputes.
- Service complaints.
- Delayed disbursement issues.
- Communication breakdown between clients and branches or Loan Officers.

## 5. Branch Loan Recommendation Charge Module

A module for recording clients referred to branches and tracking the department's agreed profit share after loan repayment.

Required features:

- Register referred clients using their NRC number and relevant personal details.
- Record the referring department, referral date, destination branch, and referring officer.
- Track the client's loan application and approval status.
- Link the referral to the relevant loan record where system integration is available.
- Monitor loan repayment status.
- Calculate the department's 5% profit share according to the approved calculation basis.
- Generate amounts due after the client has repaid the loan, based on the agreed eligibility rules.
- Notify branches of pending payments.
- Record payments made to the department.
- Track paid, unpaid, and outstanding referral charges.
- Generate reports on referred clients, loans repaid, profit shares earned, and outstanding payments.
- Export referral and payment reports to Excel.

## 6. Lost Client Recoveries Program

A module for registering previously lost or inactive clients who have been recovered and tracking the department's profit share.

Required features:

- Register recovered clients using NRC number and relevant client details.
- Record the branch, recovery date, responsible staff member, and recovery notes.
- Identify and link the client's existing or subsequent loan record.
- Track the client's repayment status.
- Calculate the department's 10% profit share after the client meets the agreed repayment eligibility conditions.
- Record the amount earned and the date it becomes payable.
- Track payments made and outstanding amounts.
- Generate reports on recovered clients, repayment progress, profit shares, and payment status.
- Export recovery and profit-share reports to Excel.
- Maintain a history of recovered clients and related transactions.

## 7. Common System Features

The following features should be available across the relevant modules.

| Feature             | Requirement                                                                                           |
| ------------------- | ----------------------------------------------------------------------------------------------------- |
| User access control | Restrict access according to roles and permissions.                                                   |
| Dashboard           | Display key figures, pending cases, outstanding charges, and payment summaries.                       |
| Notifications       | Notify responsible departments and branches of approvals, pending payments, and completed milestones. |
| Search and filters  | Search by NRC, branch, date, status, case reference, and other relevant fields.                       |
| Excel exports       | Export operational, financial, and performance reports.                                               |
| Audit trail         | Track who created, updated, approved, or processed a record.                                          |
| Financial tracking  | Maintain amounts charged, amounts paid, outstanding balances, and payment dates.                      |
| Record management   | Allow authorised users to update records while preserving transaction history.                        |
| Reporting           | Generate summaries by department, branch, period, and transaction status.                             |

## 8. Business Rules Requiring Confirmation

Before development, management should confirm the following rules to avoid incorrect calculations or payment disputes.

1\. Job recommendation reward

Is the token of appreciation awarded once after the applicant has completed three months of employment, or does it cover a recurring three-month period?

2\. Loan recommendation — 5%

Is the 5% calculated on the total loan amount repaid, the interest earned, or the actual profit generated by the branch?

3\. Lost client recovery — 10%

Is the 10% calculated on the recovered client's loan amount, interest earned, or actual profit generated?

4\. Conflict resolution charge — K20.00

Is the K20.00 charged per registered case, per client, or only after a case has been resolved? Who is responsible for paying it?

5\. Branch financial statements

Should payments collected for these programmes be recorded as branch income, departmental income, or separate recoverable charges? The accounting treatment must be approved before posting transactions automatically.

## 9. Recommended Development Phases

1. Phase 1 — Core setup: branch integration, shared client records, audit trail, and notifications.
2. Phase 2 — Operational modules: Job recommendations, customer care cases, and institutional activities calendar.
3. Phase 3 — Financial modules: Daily income and expenses, conflict resolution charges, loan referral profit shares, and lost client recoveries.
4. Phase 4 — Reporting and validation: Excel exports, dashboards, payment reconciliation, calculation testing, and user acceptance testing.

Expected outcome: A centralised platform that enables the Creative Artworks and Marketing Department to manage recommendations, customer care, institutional activities, financial reporting, conflict resolution charges, and client referral or recovery earnings with transparent tracking and accountability.