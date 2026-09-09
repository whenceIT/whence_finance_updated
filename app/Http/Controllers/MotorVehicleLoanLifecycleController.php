<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\User;
use App\Models\Office;
use App\Models\District;
use App\Models\Province;
use App\Models\MotorVehicleLoanWorkflowStage;
use App\Models\MotorVehicleLoanStatusHistory;
use App\Models\MotorVehicleAuditLog;
use App\Models\ComplianceScreening;
use App\Models\LoanProduct;
use App\Models\ApprovalMatrix;
use App\Models\VehicleOwnershipRecord;
use App\Models\VehicleMovement;
use App\Models\VehicleRollCall;
use App\Models\VehicleValuation;
use App\Models\VehicleInspection;
use App\Models\VehicleInsurance;
use App\Models\VehicleCustody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class MotorVehicleLoanLifecycleController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['vehicle', 'client', 'loanConsultant', 'originatingBranch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('branch_id')) {
            $query->where('originating_branch_id', $request->branch_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            })->orWhereHas('vehicle', function ($q) use ($search) {
                $q->where('registration_number', 'LIKE', "%{$search}%")
                  ->orWhere('make', 'LIKE', "%{$search}%");
            });
        }

        $loans = $query->paginate(20);
        $branches = Office::all();
        $statuses = ['draft', 'assessment', 'approval', 'vehicle_intake', 'disbursement', 'active_loan', 'arrears', 'default', 'recovery', 'disposal_pending', 'sold', 'closed'];

        return view('motor_vehicle.loan_lifecycle.index', compact('loans', 'branches', 'statuses'));
    }

    public function show($id)
    {
        $loan = Loan::with([
            'vehicle', 'client', 'loanConsultant', 'originatingBranch', 'branchAssessor',
            'district', 'province', 'currentCustodian', 'workflowStages', 'statusHistory',
            'auditLogs', 'complianceScreenings', 'vehicle.ownershipRecords',
            'vehicle.valuations', 'vehicle.inspections', 'vehicle.insurancePolicies',
            'vehicle.custody', 'vehicle.movements', 'vehicle.rollCalls', 'vehicle.photos', 'vehicle.documents'
        ])->findOrFail($id);

        $workflowStages = $loan->workflowStages()->orderBy('transition_date', 'desc')->get();
        $statusHistory = $loan->statusHistory()->orderBy('transition_date', 'desc')->get();
        $auditLogs = $loan->auditLogs()->orderBy('actioned_at', 'desc')->get();
        $compliance = $loan->complianceScreenings()->latest()->first();
        $valuations = $loan->vehicle ? $loan->vehicle->valuations : collect();
        $inspections = $loan->vehicle ? $loan->vehicle->inspections : collect();
        $insurancePolicies = $loan->vehicle ? $loan->vehicle->insurancePolicies : collect();
        $custody = $loan->vehicle ? $loan->vehicle->custody : null;
        $movements = $loan->vehicle ? $loan->vehicle->movements : collect();
        $rollCalls = $loan->vehicle ? $loan->vehicle->rollCalls : collect();
        $photos = $loan->vehicle ? $loan->vehicle->photos : collect();
        $documents = $loan->vehicle ? $loan->vehicle->documents : collect();

        return view('motor_vehicle.loan_lifecycle.show', compact('loan', 'workflowStages', 'statusHistory', 'auditLogs', 'compliance', 'valuations', 'inspections', 'insurancePolicies', 'custody', 'movements', 'rollCalls', 'photos', 'documents'));
    }

    public function editMasterRecord($id)
    {
        $loan = Loan::with(['vehicle', 'client', 'loanConsultant', 'originatingBranch', 'branchAssessor', 'district', 'province'])->findOrFail($id);
        $users = User::all();
        $branches = Office::all();
        $districts = District::all();
        $provinces = Province::all();

        return view('motor_vehicle.loan_lifecycle.edit_master_record', compact('loan', 'users', 'branches', 'districts', 'provinces'));
    }

    public function updateMasterRecord(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $validated = $request->validate([
            'loan_consultant_id' => 'nullable|exists:users,id',
            'originating_branch_id' => 'nullable|exists:offices,id',
            'branch_assessor_id' => 'nullable|exists:users,id',
            'district_id' => 'nullable|exists:districts,id',
            'province_id' => 'nullable|exists:provinces,id',
            'vehicle_status' => 'nullable|string',
            'custody_status' => 'nullable|string',
            'current_storage_location' => 'nullable|string',
            'current_custodian_id' => 'nullable|exists:users,id',
            'current_custodian_phone' => 'nullable|string',
            'current_custodian_nrc' => 'nullable|string',
            'current_custodian_alternative_contact' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $loan->update($validated);

        $this->logAudit($loan->id, $loan->vehicle_id, 'master_record_updated', null, 'Master record updated');

        Flash::success('Master record updated successfully');
        return redirect()->route('motor-vehicle-loans.show', $id);
    }

    public function transitionStage(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate([
            'stage' => 'required|string',
            'comments' => 'nullable|string',
            'approval_notes' => 'nullable|string',
        ]);

        $previousStage = $loan->status;
        $user = Auth::user();
        $branch = $user ? Office::find($user->office_id) : null;

        $loan->update(['status' => $request->stage]);

        MotorVehicleLoanWorkflowStage::create([
            'motor_vehicle_loan_id' => $loan->id,
            'stage' => $request->stage,
            'previous_stage' => $previousStage,
            'comments' => $request->comments,
            'officer_id' => $user ? $user->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'district_id' => $branch ? $branch->district_id : null,
            'province_id' => $branch ? $branch->province_id : null,
            'approval_notes' => $request->approval_notes,
            'transition_date' => now(),
        ]);

        MotorVehicleLoanStatusHistory::create([
            'motor_vehicle_loan_id' => $loan->id,
            'previous_status' => $previousStage,
            'new_status' => $request->stage,
            'user_id' => $user ? $user->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'action_reason' => $request->comments,
            'transition_date' => now(),
        ]);

        $this->logAudit($loan->id, $loan->vehicle_id, 'stage_transition', $previousStage, $request->stage);

        Flash::success('Workflow stage transitioned successfully');
        return back();
    }

    public function auditTrail($id)
    {
        $loan = Loan::findOrFail($id);
        $auditLogs = $loan->auditLogs()->orderBy('actioned_at', 'desc')->paginate(50);

        return view('motor_vehicle.loan_lifecycle.audit_trail', compact('loan', 'auditLogs'));
    }

    public function editKyc($clientId, $loanId = null)
    {
        $client = $clientId;
        return view('motor_vehicle.loan_lifecycle.edit_kyc', compact('client', 'loanId'));
    }

    public function updateKyc(Request $request, $clientId, $loanId = null)
    {
        $client = $clientId;

        $validated = $request->validate([
            'nrc_number' => 'nullable|string',
            'tpin' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city' => 'nullable|string',
            'employer' => 'nullable|string',
            'employer_address' => 'nullable|string',
            'business_name' => 'nullable|string',
            'business_type' => 'nullable|string',
            'annual_income' => 'nullable|numeric',
            'phone_primary' => 'nullable|string',
            'phone_secondary' => 'nullable|string',
            'email_primary' => 'nullable|email',
            'next_of_kin_name' => 'nullable|string',
            'next_of_kin_relationship' => 'nullable|string',
            'next_of_kin_phone' => 'nullable|string',
            'next_of_kin_address' => 'nullable|string',
            'guarantor_name' => 'nullable|string',
            'guarantor_nrc' => 'nullable|string',
            'guarantor_phone' => 'nullable|string',
            'guarantor_address' => 'nullable|string',
            'guarantor_employer' => 'nullable|string',
            'guarantor_relationship' => 'nullable|string',
        ]);

        $client->update($validated);

        Flash::success('KYC information updated successfully');
        if ($loanId) {
            return redirect()->route('motor-vehicle-loans.compliance-screening', $loanId);
        }
        return back();
    }

    public function complianceScreening($loanId)
    {
        
        $loan = Loan::with('client')->findOrFail($loanId);
        $screenings = ComplianceScreening::where('motor_vehicle_loan_id', $loanId)->latest()->get();
        $latest = $screenings->first();

        return view('motor_vehicle.loan_lifecycle.compliance_screening', compact('loan', 'screenings', 'latest'));
    }

    public function storeComplianceScreening(Request $request, $loanId)
    {
       
        $loan = Loan::findOrFail($loanId);
        $user = Sentinel::getUser();

        $validated = $request->validate([
            'pep_result' => 'nullable|string',
            'sanctions_result' => 'nullable|string',
            'screening_date' => 'nullable|date',
            'match_level' => 'nullable|string',
            'comments' => 'nullable|string',
            'supporting_evidence' => 'nullable|string',
            'status' => 'required|in:pending,cleared,flagged,requires_review',
        ]);

        $validated['client_id'] = $loan->client_id;
        $validated['motor_vehicle_loan_id'] = $loan->id;
        $validated['screening_officer_id'] = $user->id;

        ComplianceScreening::create($validated);

        Flash::success('Compliance screening recorded successfully');

        $vehicle = Vehicle::where('loan_id', $loan->id)->first();
    
        return redirect()->route('vehicles.ownership-verification.show', $vehicle->id);

    }

    public function productConfigurations()
    {
        $configs = LoanProduct::where('is_motor_vehicle', true)->latest()->paginate(20);

        return view('motor_vehicle.admin.product_configurations', compact('configs'));
    }

    public function createProductConfiguration()
    {
        return view('motor_vehicle.admin.create_product_configuration');
    }

    public function storeProductConfiguration(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'minimum_principal' => 'nullable|numeric',
            'maximum_principal' => 'nullable|numeric',
            'minimum_loan_term' => 'nullable|integer',
            'maximum_loan_term' => 'nullable|integer',
            'minimum_interest_rate' => 'nullable|numeric',
            'maximum_interest_rate' => 'nullable|numeric',
            'interest_rate_type' => 'nullable|string',
            'service_fee' => 'nullable|numeric',
            'processing_fee' => 'nullable|numeric',
            'insurance_fee' => 'nullable|numeric',
            'valuation_fee' => 'nullable|numeric',
            'inspection_fee' => 'nullable|numeric',
            'penalty_rate' => 'nullable|numeric',
            'recovery_charges' => 'nullable|numeric',
            'effective_date' => 'nullable|date',
        ]);

        $validated['is_motor_vehicle'] = true;
        $validated['is_active'] = false;

        LoanProduct::create($validated);

        Flash::success('Motor Vehicle Loan Product configuration created successfully');
        return redirect()->route('motor-vehicle.product-configurations');
    }

    public function approvalMatrices()
    {
        $matrices = ApprovalMatrix::with(['requiredApprover', 'branch', 'district', 'province'])->latest()->paginate(20);

        return view('motor_vehicle.admin.approval_matrices', compact('matrices'));
    }

    public function storeApprovalMatrix(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_amount' => 'nullable|numeric',
            'max_amount' => 'nullable|numeric',
            'level' => 'required|string',
            'required_approver_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:offices,id',
            'district_id' => 'nullable|exists:districts,id',
            'province_id' => 'nullable|exists:provinces,id',
        ]);

        ApprovalMatrix::create($validated);

        Flash::success('Approval matrix created successfully');
        return back();
    }

    public function ownershipVerification($vehicleId = null)
    {
        
        if ($vehicleId) {
            $vehicle = Vehicle::with([
                'insurancePolicies',
                'inspections',
                'documents',
                'photos',
                'custody.receiver',
                'valuations',
            ])->findOrFail($vehicleId);
            $records = VehicleOwnershipRecord::where('vehicle_id', $vehicleId)->get();
            return view('motor_vehicle.ownership.verification', compact('vehicle', 'records'));
        }

        $records = VehicleOwnershipRecord::with('vehicle')->latest()->paginate(20);
        return view('motor_vehicle.ownership.index', compact('records'));
    }

    public function storeOwnershipVerification(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $validated = $request->validate([
            'ownership_type' => 'required|in:individual,letter_of_sale,company',
            'registered_owner' => 'nullable|string',
            'seller_name' => 'nullable|string',
            'seller_nrc' => 'nullable|string',
            'seller_phone' => 'nullable|string',
            'seller_email' => 'nullable|email',
            'seller_address' => 'nullable|string',
            'witness_1_name' => 'nullable|string',
            'witness_1_nrc' => 'nullable|string',
            'witness_2_name' => 'nullable|string',
            'witness_2_nrc' => 'nullable|string',
            'company_name' => 'nullable|string',
            'company_registration' => 'nullable|string',
            'company_directors' => 'nullable|array',
            'company_resolution' => 'nullable|string',
            'authorized_representative' => 'nullable|string',
            'authorized_representative_nrc' => 'nullable|string',
            'letter_of_sale_file' => 'nullable|string',
            'ownership_documents' => 'nullable|string',
            'ownership_verification_status' => 'nullable|string',
        ]);

        $vehicle->update($validated);
        VehicleOwnershipRecord::create(array_merge($validated, [
            'vehicle_id' => $vehicleId,
            'registered_owner_name' => $validated['registered_owner'] ?? null,
            'company_registration_number' => $validated['company_registration'] ?? null,
            'authorized_representative_name' => $validated['authorized_representative'] ?? null,
            'ownership_documents_path' => $validated['ownership_documents'] ?? null,
        ]));

        Flash::success('Ownership verification recorded successfully');
        return back();
    }

    public function movements($vehicleId = null)
    {
        if ($vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            $movements = VehicleMovement::where('vehicle_id', $vehicleId)->orderBy('movement_date', 'desc')->paginate(20);
            $users = User::all();
            return view('motor_vehicle.movements.index', compact('vehicle', 'movements', 'users'));
        }

        $movements = VehicleMovement::with('vehicle')->latest()->paginate(20);
        return view('motor_vehicle.movements.index_all', compact('movements'));
    }

    public function rollCalls($vehicleId = null)
    {
        if ($vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            $rollCalls = VehicleRollCall::where('vehicle_id', $vehicleId)->orderBy('verification_date', 'desc')->paginate(20);
            $users = User::all();
            return view('motor_vehicle.roll_calls.index', compact('vehicle', 'rollCalls', 'users'));
        }

        $rollCalls = VehicleRollCall::with('vehicle')->latest()->paginate(20);
        return view('motor_vehicle.roll_calls.index_all', compact('rollCalls'));
    }

    public function storeMovement(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $validated = $request->validate([
            'previous_location' => 'required|string',
            'new_location' => 'required|string',
            'movement_date' => 'required|date',
            'authorized_by' => 'nullable|exists:users,id',
            'moved_by' => 'nullable|exists:users,id',
            'reason' => 'nullable|string',
            'condition' => 'nullable|string',
            'photos' => 'nullable|array',
        ]);

        $validated['vehicle_id'] = $vehicleId;
        VehicleMovement::create($validated);

        $this->logAuditForVehicle($vehicleId, 'movement_recorded', null, 'Vehicle movement recorded: ' . $validated['new_location']);

        Flash::success('Vehicle movement recorded successfully');
        return back();
    }

    public function storeRollCall(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $validated = $request->validate([
            'verification_date' => 'required|date',
            'officer_id' => 'nullable|exists:users,id',
            'location_confirmed' => 'nullable|boolean',
            'vehicle_present' => 'nullable|boolean',
            'condition' => 'nullable|string',
            'current_mileage' => 'nullable|integer',
            'photos' => 'nullable|array',
            'status' => 'required|in:verified,missing,damaged,relocated',
            'remarks' => 'nullable|string',
        ]);

        $validated['vehicle_id'] = $vehicleId;
        VehicleRollCall::create($validated);

        $this->logAuditForVehicle($vehicleId, 'roll_call_recorded', null, 'Roll call recorded: ' . $validated['status']);

        Flash::success('Roll call recorded successfully');
        return back();
    }

    public function custodyRegister()
    {
        $custodies = VehicleCustody::with([
            'vehicle',
            'vehicle.photos',
            'vehicle.ownershipRecords',
            'vehicle.loan',
            'vehicle.loan.client',
            'vehicle.loan.loanConsultant',
            'vehicle.loan.complianceScreenings',
            'vehicle.inspections',
            'vehicle.valuations',
            'receiver',
        ])->latest()->paginate(20);

        $statuses = [];
        $totalApproved = VehicleCustody::where('custody_approved', true)->count();
        $totalPending = VehicleCustody::where('custody_approved', false)->count();
        foreach ($custodies as $custody) {
            $loan = optional($custody->vehicle)->loan;
            if ($loan) {
                $kycCompleted = false;
                $complianceCompleted = false;
                $ownershipCompleted = false;

                if ($loan->loan_product_id == 0 && $loan->client) {
                    $client = $loan->client;
                    $kycFields = ['nrc_number', 'phone_primary', 'email_primary', 'city', 'address_line1'];
                    $kycCompleted = true;
                    foreach ($kycFields as $field) {
                        if (!isset($client->$field) || $client->$field === null || trim($client->$field) === '') {
                            $kycCompleted = false;
                            break;
                        }
                    }

                    $complianceCompleted = $loan->complianceScreenings
                        ->whereIn('status', ['cleared', 'flagged', 'requires_review'])
                        ->isNotEmpty();

                    if ($custody->vehicle) {
                        $ownershipCompleted = $custody->vehicle->ownershipRecords->isNotEmpty();
                    }
                }

                $statuses[$custody->id] = [
                    'kyc_completed'              => $kycCompleted,
                    'compliance_screening_completed' => $complianceCompleted,
                    'ownership_completed'        => $ownershipCompleted,
                ];
            } else {
                $statuses[$custody->id] = ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null];
            }
        }

        return view('motor_vehicle.custody.register', compact('custodies', 'statuses', 'totalApproved', 'totalPending'));
    }

    public function storeIntake(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $validated = $request->validate([
            'received_at' => 'required|date',
            'received_by' => 'required|exists:users,id',
            'garage_name' => 'required|string',
            'garage_location' => 'nullable|string',
            'garage_gps' => 'nullable|string',
            'parking_bay' => 'nullable|string',
            'garage_contact_person' => 'nullable|string',
            'garage_contact_phone' => 'nullable|string',
            'house_owner_name' => 'nullable|string',
            'house_owner_nrc' => 'nullable|string',
            'house_owner_phone' => 'nullable|string',
            'alternative_contact_name' => 'nullable|string',
            'alternative_contact_phone' => 'nullable|string',
            'storage_start_date' => 'nullable|date',
            'storage_end_date' => 'nullable|date',
            'gps_location' => 'nullable|string',
            'location_description' => 'nullable|string',
            'keys_received' => 'required|boolean',
            'documents_received' => 'required|boolean',
            'accessories_received' => 'required|boolean',
            'fuel_level' => 'nullable|integer|min:0|max:100',
            'intake_photos' => 'nullable|array',
            'signed_intake_form_path' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['vehicle_id'] = $vehicleId;
        $validated['status'] = 'in_custody';
        $validated['custody_approved'] = 0;
        $validated['intake_date'] = now();

        VehicleCustody::create($validated);

        $this->logAuditForVehicle($vehicleId, 'intake_recorded', null, 'Vehicle intake recorded');

        Flash::success('Vehicle intake recorded successfully');
        return back();
    }

    private function logAudit($loanId, $vehicleId, $action, $oldValue, $newValue)
    {
        $user = Auth::user();
        $branch = $user ? Office::find($user->office_id) : null;

        MotorVehicleAuditLog::create([
            'motor_vehicle_loan_id' => $loanId,
            'vehicle_id' => $vehicleId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'user_id' => $user ? $user->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'district_id' => $branch ? $branch->district_id : null,
            'province_id' => $branch ? $branch->province_id : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'actioned_at' => now(),
        ]);
    }

    private function logAuditForVehicle($vehicleId, $action, $oldValue, $newValue)
    {
        $loan = Loan::where('vehicle_id', $vehicleId)->first();
        $this->logAudit($loan ? $loan->id : null, $vehicleId, $action, $oldValue, $newValue);
    }

    public function checkKycAndComplianceStatus($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        $kycCompleted = false;
        $complianceCompleted = false;
        $ownershipCompleted = false;

        if ($loan->loan_product_id == 0) {
            $client = $loan->client;

            if ($client) {
                $kycFields = ['nrc_number', 'phone_primary', 'email_primary', 'city', 'address_line1'];
                $kycCompleted = true;
                foreach ($kycFields as $field) {
                    if (!isset($client->$field) || $client->$field === null || trim($client->$field) === '') {
                        $kycCompleted = false;
                        break;
                    }
                }
            }

            $compliance = ComplianceScreening::where('motor_vehicle_loan_id', $loanId)
                ->whereIn('status', ['cleared', 'flagged', 'requires_review'])
                ->exists();
            $complianceCompleted = $compliance;

            $vehicle = Vehicle::where('loan_id', $loan->id)->first();
            if ($vehicle) {
                $ownership = VehicleOwnershipRecord::where('vehicle_id', $vehicle->id)->exists();
                $ownershipCompleted = $ownership;
            }
        }

        return response()->json([
            'kyc_completed' => $kycCompleted,
            'compliance_screening_completed' => $complianceCompleted,
            'ownership_completed' => $ownershipCompleted,
        ]);
    }
}
