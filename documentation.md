Motor Vehicle Loan Lifecycle System — Copilot Implementation Backlog

This is organized into feature groups that can be implemented independently and prompted one-by-one in GitHub Copilot. The order follows a practical implementation roadmap.

Phase 1 — Foundation (Core Data & Workflow)
Feature Group 1: Motor Vehicle Loan Master Record

Objective: Create a single source of truth for every Motor Vehicle Loan.

Scope

Create/extend the motor_vehicle_loans module.

Link each loan to:

Client.

Vehicle.

Loan Consultant.

Branch.

Branch Assessor.

District.

Province.

Capture loan ownership and responsibility.

Fields

Loan Consultant (Responsible Officer)

Originating Branch

Branch Assessor

District

Province

Loan Status

Vehicle Status

Custody Status

Current Storage Location

Current Custodian

Deliverables

Database migration.

Model relationships.

CRUD forms.

Details page showing ownership hierarchy.

Feature Group 2: Loan Lifecycle Workflow Engine

Objective: Manage every stage of a Motor Vehicle Loan.

Workflow Stages

Draft/Application

Assessment

Approval

Vehicle Intake

Disbursement

Active Loan

Arrears

Default

Recovery

Disposal Pending

Sold

Closed

Requirements

Workflow transition rules.

Status history.

Comments per transition.

Officer performing transition.

Timestamp.

Approval notes.

Deliverables

Workflow service.

Status timeline component.

Workflow permissions.

Feature Group 3: Complete Audit Trail

Objective: Record every action performed on a Motor Vehicle Loan or Vehicle.

Track

Created by.

Assessed by.

Approved by.

Valuated by.

Vehicle received by.

Vehicle moved by.

Verified by.

Recovery officer.

Disposal officer.

Closed by.

Log Information

Previous status.

New status.

User.

Branch.

Action reason.

Timestamp.

Deliverables

Audit log table.

Audit timeline UI.

Activity history tab.

Phase 2 — Customer Compliance & KYC
Feature Group 4: Client KYC & Compliance

Objective: Capture complete borrower verification information.

KYC Fields

NRC/Passport.

TPIN.

Address.

Employer.

Business Details.

Income.

Phone numbers.

Email.

Next of Kin.

Guarantor information.

Document Uploads

NRC Front.

NRC Back.

Selfie.

Utility Bill.

Employment Letter.

Payslip.

Deliverables

KYC section.

Document upload manager.

Verification status.

Feature Group 5: PEP & Sanctions Screening

Objective: Record compliance screening.

Fields

PEP Result.

Sanctions Result.

Screening Date.

Screening Officer.

Match Level.

Comments.

Supporting Evidence.

Workflow

Pending.

Cleared.

Flagged.

Requires Review.

Deliverables

Compliance card.

Approval blocker if not cleared.

Compliance report.

Phase 3 — Product Configuration
Feature Group 6: Configurable Motor Vehicle Loan Products

Objective: Remove hardcoded commercial rules.

Configurable Items

Loan Bands.

Minimum Loan.

Maximum Loan.

Interest Rate.

Interest Type.

Tenure.

Service Fee.

Processing Fee.

Insurance Fee.

Valuation Fee.

Inspection Fee.

Penalty Rate.

Recovery Charges.

Requirements

Effective Date.

Version History.

Active/Inactive.

Approval before activation.

Deliverables

Product Configuration module.

Parameter Version table.

Admin UI.

Feature Group 7: Approval Matrix Configuration

Objective: Configure approval hierarchy.

Configure

Branch Limits.

District Limits.

Province Limits.

Head Office Limits.

Approval Levels

Loan Officer.

Branch Manager.

District Manager.

Province Manager.

Risk Manager.

Executive Committee.

Deliverables

Approval Matrix table.

Workflow integration.

Approval dashboard.

Phase 4 — Vehicle Ownership & Documentation
Feature Group 8: Vehicle Ownership Verification

Objective: Support different ownership scenarios.

Ownership Types

Individual.

Letter of Sale.

Company/Corporate.

Individual Requirements

Registered Owner.

Seller.

Ownership Documents.

Letter of Sale Requirements

Letter upload.

Seller verification.

Witnesses.

Corporate Requirements

Company Registration.

Directors.

Resolution.

Authorized Representative.

Deliverables

Dynamic ownership forms.

Ownership checklist.

Feature Group 9: Vehicle Valuation Module

Objective: Record vehicle valuation history.

Capture

Valuation Company.

Valuator.

Market Value.

Forced Sale Value.

Valuation Cost.

Expiry Date.

Uploads

Valuation Report.

Photos.

Supporting Documents.

Deliverables

Multiple valuations.

Valuation history.

Expiry alerts.

Feature Group 10: Vehicle Inspection Module
Capture

Inspection Date.

Inspector.

Mileage.

Mechanical Condition.

Interior.

Exterior.

Tyres.

Battery.

Accessories.

Upload

Inspection Report.

Inspection Photos.

Deliverables

Inspection checklist.

Condition score.

Feature Group 11: Insurance Management
Capture

Insurance Company.

Policy Number.

Start Date.

End Date.

Premium.

Cover Type.

Alerts

Expiring insurance.

Expired insurance.

Deliverables

Insurance history.

Renewal reminders.

Phase 5 — Vehicle Intake & Custody Management
Feature Group 12: Vehicle Intake Module

Objective: Record vehicle reception into company custody.

Capture

Intake Date.

Receiving Officer.

Condition.

Keys Received.

Documents Received.

Accessories Received.

Fuel Level.

Upload

Intake Photos.

Signed Intake Form.

Deliverables

Intake checklist.

Intake report PDF.

Feature Group 13: Vehicle Custody Register

Objective: Know exactly where every vehicle is parked.

Capture

Storage Location.

GPS/Location Description.

House/Garage Owner.

Custodian Name.

NRC.

Phone.

Alternative Contact.

Storage Start Date.

Notes.

Deliverables

Custody Register.

Current Custody Card.

Custody history.

Feature Group 14: Vehicle Movement Register
Movement Record

Previous Location.

New Location.

Movement Date.

Authorized By.

Moved By.

Reason.

Condition.

Photos.

Deliverables

Movement history.

Transfer approval workflow.

Feature Group 15: Weekly Vehicle Roll Call
Weekly Verification

Verification Date.

Officer.

Location Confirmed.

Vehicle Present.

Condition.

Current Mileage.

Photos.

Status

Verified.

Missing.

Damaged.

Relocated.

Deliverables

Weekly verification screen.

Missing vehicle report.

Verification dashboard.

Phase 6 — Media & Document Management
Feature Group 16: Vehicle Photo Gallery
Categories

Intake Photos.

Weekly Verification.

Inspection.

Valuation.

Recovery.

Disposal.

Requirements

Unlimited history.

Date.

Officer.

Caption.

Deliverables

Gallery.

Timeline.

Feature Group 17: Document Repository
Store

NRC.

Valuation Reports.

Insurance.

Letters of Sale.

Registration.

Road Tax.

Fitness.

Recovery Notices.

Sale Documents.

Deliverables

Document manager.

Expiry tracker.

Download/view.

Phase 7 — Repayments, Incentives & Top Ups
Feature Group 18: Branch Referral Tracking
Capture

Referring Branch.

Receiving Branch.

Referral Officer.

Referral Date.

Notes.

Deliverables

Referral history.

Referral reports.

Feature Group 19: Loan Consultant Incentive Tracking
Rules

Incentive calculated.

Pending.

Earned.

Payable only after full settlement.

Paid.

Deliverables

Incentive ledger.

Settlement validation.

Payroll export.

Feature Group 20: Re-loans & Add-On Loans
Support

Top Up.

Re-loan.

Linked parent loan.

Validation

Loan performance.

Valuation.

Insurance.

Outstanding balance.

Deliverables

Loan chain history.

Eligibility checker.

Phase 8 — Default & Recovery
Feature Group 21: Arrears & Default Engine
Automatic States

Due.

Overdue.

Arrears.

Default.

Configurable Rules

Grace Period.

Days to Default.

Penalty.

Deliverables

Default calculator.

Arrears dashboard.

Feature Group 22: Recovery Workflow
Recovery Stages

Reminder.

Demand Notice.

Notice of Intention to Sell.

Repossession.

Recovery in Progress.

Recovery Complete.

Deliverables

Recovery case module.

Officer assignment.

Timeline.

Feature Group 23: Notice Management
Auto Generate

Reminder Letters.

Demand Letters.

Intention to Sell.

Final Notices.

Deliverables

PDF generation.

SMS trigger.

Email trigger.

Feature Group 24: Traffic Offence Management
Capture

Offence.

Date.

Amount.

Paid By.

Status.

Receipt.

Deliverables

Traffic offence ledger.

Outstanding offences report.

Phase 9 — Disposal & Sale Management
Feature Group 25: Vehicle Disposal Workflow
Stages

Disposal Approved.

Valuation.

Sale.

Payment.

Release.

Closed.

Deliverables

Disposal workflow.

Approval records.

Feature Group 26: Buyer Management
Capture Buyer

Full Name.

NRC.

Address.

Phone.

Email.

Company Details (if corporate).

Deliverables

Buyer profile.

Buyer history.

Feature Group 27: Vehicle Purchase Report Generator
Auto Generate PDF

Include:

Buyer details.

Vehicle details.

Purchase price.

As-Is declaration.

Signatures.

Witnesses.

Sale date.

Deliverables

Printable report.

Digital copy storage.

Feature Group 28: Sale Proceeds Allocation
Automatically Allocate

Sale Amount.

Outstanding Principal.

Interest.

Penalties.

Recovery Costs.

Storage Costs.

Legal Costs.

Surplus.

Deliverables

Allocation journal.

Disposal financial report.

Phase 10 — Notifications & Automation
Feature Group 29: SMS Notification Engine
Client SMS

Loan approved.

Disbursed.

Payment due.

Overdue.

Default.

Recovery notice.

Vehicle sold.

Loan closed.

Deliverables

SMS templates.

SMS logs.

Feature Group 30: Management Alerts
Notify Managers When

Vehicle moved.

Roll-call missed.

Insurance expired.

Valuation expired.

Default triggered.

Recovery overdue.

Disposal completed.

Deliverables

Notification center.

Email/SMS alerts.

Phase 11 — Reporting & Executive Dashboard
Feature Group 31: Motor Vehicle Portfolio Dashboard
KPIs

Portfolio Value.

Outstanding Balance.

Active Loans.

Arrears.

Defaults.

Recoveries.

Disposals.

Profitability.

Filters

Province.

District.

Branch.

Officer.

Date Range.

Feature Group 32: Custody Dashboard
Display

Vehicles in custody.

By Province.

By District.

By Branch.

Missing vehicles.

Verification compliance.

Storage occupancy.

Feature Group 33: Recovery Dashboard
Display

Recovery pipeline.

Notices issued.

Repossessions.

Vehicles awaiting sale.

Sale proceeds.

Recovery success rate.

Feature Group 34: Profitability Dashboard
Show

Interest earned.

Fees earned.

Recovery income.

Disposal income.

Recovery expenses.

Storage expenses.

Net profitability.

Phase 12 — Administration & Configuration
Feature Group 35: Motor Vehicle Settings Module
Configurable Items

Interest Rates.

Loan Bands.

Tenure.

Fees.

Penalties.

Recovery timelines.

Notice periods.

Approval limits.

Incentive percentages.

Storage verification frequency.

Deliverables

Settings UI.

Version history.

Effective date management.

Feature Group 36: Motor Vehicle Audit & History Viewer
Single Timeline Showing

Client → Staff → Branch → Assessor → District → Province → KYC → Valuation → Approval → Intake → Storage → Movement → Weekly Verification → Disbursement → Repayment → Default → Recovery → Disposal → Buyer → Closure

Deliverables

Complete lifecycle timeline.

Expandable event history.

Linked documents/photos.

Officer audit trail.

Recommended Copilot Implementation Order

Phase

	

Feature Groups




Sprint 1 (Foundation)

	1–3 (Master Record, Workflow, Audit Trail)


Sprint 2 (Compliance & Product Rules)

	4–7 (KYC, PEP, Product Configuration, Approval Matrix)


Sprint 3 (Vehicle Operations)

	8–17 (Ownership, Valuation, Inspection, Insurance, Intake, Custody, Movement, Roll Call, Documents)


Sprint 4 (Loan Operations)

	18–24 (Referrals, Incentives, Top-Ups, Defaults, Recovery, Notices, Traffic Offences)


Sprint 5 (Disposal & Reporting)

	25–36 (Disposal, Buyer Management, Purchase Reports, Sale Allocation, Notifications, Dashboards, Settings, Lifecycle Viewer)