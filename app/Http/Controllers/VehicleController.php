<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleInsurance;
use App\Models\VehicleDocument;
use App\Models\VehiclePhoto;
use App\Models\Client;
use App\Models\VehicleInspection;
use App\Models\VehicleInspectionPhoto;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\VehicleValuation;
use App\Models\VehicleCustody;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Office;
use App\Models\ComplianceScreening;
use App\Models\VehicleOwnershipRecord;
use App\Models\District;
use App\Models\Province;
use App\Models\User;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('client')
            ->latest()
            ->paginate(20);

        return view('motor_vehicle.vehicles', compact('vehicles'));
    }

    public function create()
    {
        $clients = Client::orderBy('display_name')
            ->get();

        return view('motor_vehicle.create', compact('clients'));
    }

public function edit($id)
{
    $vehicle = Vehicle::findOrFail($id);

    $clients = Client::orderBy('display_name')->get();

    return view(
        'motor_vehicle.edit',
        compact('vehicle', 'clients')
    );
}
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'make' => 'required',
            'model' => 'required',
            'registration_number' => 'required'
        ]);

        Vehicle::create([
            'vehicle_code' => 'VH'.time(),
            'client_id' => $request->client_id,
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'registration_number' => $request->registration_number,
            'market_value' => $request->market_value,
            'forced_sale_value' => $request->forced_sale_value
        ]);

        return redirect('/vehicles')
            ->with('success','Vehicle added successfully');
    }


    public function show($id)
    {
        $vehicle = Vehicle::with('client')
            ->findOrFail($id);

        return view('motor_vehicle.show', compact('vehicle'));
    }


public function update(Request $request, $id)
{
    $request->validate([
        'make' => 'required',
        'model' => 'required',
        'registration_number' => 'required'
    ]);

    $vehicle = Vehicle::findOrFail($id);

    $vehicle->update([
        'make' => $request->make,
        'model' => $request->model,
        'year' => $request->year,
        'registration_number' => $request->registration_number,
        'market_value' => $request->market_value,
        'color'=> $request->color,
        'engine_number' => $request->engine_number,
        'chassis_number'=> $request->chassis_number,
        'insurance_policy_number' => $request->insurance_policy_number,
        'mileage' => $request->mileage,
    ]);

    return redirect("/vehicles/{$id}")
        ->with('success', 'Vehicle updated successfully');
}




public function searchClients(Request $request)
{
    $search = $request->get('search');

    $clients = Client::with('office')
        ->where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('middle_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $search . '%');
        })
        ->limit(20)
        ->get();

    $results = [];

    foreach ($clients as $client) {

        $fullName = trim(
            $client->first_name . ' ' .
            $client->middle_name . ' ' .
            $client->last_name
        );

        $officeName = $client->office ? $client->office->name : 'No Office';

        $results[] = [
            'id'   => $client->id,
            'text' => $fullName . ' | ' . $officeName
        ];
    }

    return response()->json($results);
}


  public function createInsurance($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        return view(
            'motor_vehicle.create_insurance',
            compact('vehicle')
        );
    }

     public function createCustody($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        return view(
            'motor_vehicle.create_custody',
            compact('vehicle')
        );
    }

    public function storeInsurance(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $request->validate([
            'insurer_name' => 'required',
            'policy_number' => 'required',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date',
            'insured_value' => 'required|numeric',
            'premium' => 'nullable|numeric',
            'cover_type' => 'nullable|string',
        ]);

        VehicleInsurance::create([
            'vehicle_id' => $vehicle->id,
            'insurer_name' => $request->insurer_name,
            'policy_number' => $request->policy_number,
            'start_date' => $request->start_date,
            'expiry_date' => $request->expiry_date,
            'insured_value' => $request->insured_value,
            'premium' => $request->premium,
            'cover_type' => $request->cover_type,
        ]);

       return redirect()->route('vehicles.ownership-verification.show', $vehicle->id)
    ->with(
        'success',
        'Insurance information added successfully.'
    );
    }


    public function storeCustody(Request $request, $vehicleId)
    {

    $vehicle = Vehicle::findOrFail($vehicleId);

     $request->validate([
        'received_at' => 'required|date',
        'received_by' => 'required',
        'garage_name' => 'required',
    ]);

    VehicleCustody::create([
        'vehicle_id'             => $vehicle->id,
        'received_at'            => $request->received_at,
        'received_by'            => $request->received_by,
        'keys_received'          => $request->keys_received,
        'key_tag_numbers'        => $request->key_tag_numbers,
        'garage_name'            => $request->garage_name,
        'garage_location'        => $request->garage_location,
        'garage_gps'             => $request->garage_gps,
        'parking_bay'            => $request->parking_bay,
        'garage_contact_person'  => $request->garage_contact_person,
        'garage_contact_phone'   => $request->garage_contact_phone,
        'remarks'                => $request->remarks,
        'status'                 => 'in_custody',
        'custody_approved'       => 0,
    ]);

    return redirect()->route('vehicles.ownership-verification.show', $vehicle->id)
        ->with('success', 'Vehicle successfully received into custody.');


    }

    public function approveCustody(Request $request, $custodyId)
    {
        $custody = VehicleCustody::findOrFail($custodyId);
        
        $user = auth()->user();
        if (!$user || $custody->received_by != $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $custody->custody_approved = 1;
        $custody->save();

        return response()->json([
            'success' => true,
            'message' => 'Custody approved successfully'
        ]);
    }

    public function getPendingCustodyApprovals(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $service = new \App\Services\VehicleCustodyApprovalService();
        $pending = $service->getPendingApprovalsForUser($user->id);

        return response()->json([
            'success' => true,
            'data' => $pending
        ]);
    }

    public function createDocuments($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        return view(
            'motor_vehicle.create_documents',
            compact('vehicle')
        );
    }

  public function storeDocuments(Request $request, $vehicleId)
{
    $vehicle = Vehicle::findOrFail($vehicleId);

    $request->validate([
        'document_type' => 'required',
        'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
    ]);

    try {

        $file = $request->file('document_file');

        $fileName =
            'vehicle_documents/' .
            $vehicle->id . '/' .
            time() . '_' .
            preg_replace('/[^A-Za-z0-9\.\-_]/', '', $file->getClientOriginalName());

        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => 'nyc3',
            'endpoint' => 'https://nyc3.digitaloceanspaces.com',
            'credentials' => [
                'key'    => 'DO00RP9FA3QZTA3JV637',
                'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
            ],
        ]);

        $result = $s3Client->putObject([
            'Bucket' => 'wfssystem',
            'Key'    => $fileName,
            'Body'   => fopen($file->getPathname(), 'r'),
            'ACL'    => 'public-read',
            'ContentType' => $file->getMimeType(),
        ]);

        $url = $result['ObjectURL'];

        Log::info('Vehicle document uploaded', [
            'vehicle_id' => $vehicle->id,
            'url' => $url
        ]);

        VehicleDocument::create([
            'vehicle_id' => $vehicle->id,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'document_file' => $url,
            'uploaded_by' => auth()->id()
        ]);

        return redirect()->route('vehicles.ownership-verification.show', $vehicle->id)
            ->with(
                'success',
                'Document uploaded successfully.'
            );

    } catch (AwsException $e) {

        Log::error('DigitalOcean upload error: ' . $e->getMessage());

        return back()->with(
            'error',
            'Failed to upload document.'
        );

    } catch (\Exception $e) {

        Log::error('General upload error: ' . $e->getMessage());

        return back()->with(
            'error',
            'Failed to upload document.'
        );
    }
}


 public function createPhotos($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        return view(
            'motor_vehicle.create_photos',
            compact('vehicle')
        );
    }

    public function storePhotos(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $request->validate([
            'photo_type' => 'required',
            'photo' => 'required|image|max:10240'
        ]);

        try {

            $file = $request->file('photo');

            $fileName =
                'vehicle_photos/' .
                $vehicle->id . '/' .
                time() . '_' .
                preg_replace(
                    '/[^A-Za-z0-9\.\-_]/',
                    '',
                    $file->getClientOriginalName()
                );

            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => 'nyc3',
                'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                'credentials' => [
                    'key' => 'DO00RP9FA3QZTA3JV637',
                    'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                ],
            ]);

            $result = $s3Client->putObject([
                'Bucket' => 'wfssystem',
                'Key' => $fileName,
                'Body' => fopen($file->getPathname(), 'r'),
                'ACL' => 'public-read',
                'ContentType' => $file->getMimeType(),
            ]);

            $url = $result['ObjectURL'];

            VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'photo_type' => $request->photo_type,
                'caption' => $request->caption,
                'photo_url' => $url,
                'uploaded_by' => auth()->id()
            ]);

            return redirect()->route('vehicles.ownership-verification.show', $vehicle->id)
                ->with(
                    'success',
                    'Photo uploaded successfully.'
                );

        } catch (AwsException $e) {

            Log::error($e->getMessage());

            return back()->with(
                'error',
                'Failed to upload photo.'
            );
        }
    }

    public function destroyPhoto(Request $request, Vehicle $vehicle, VehiclePhoto $photo)
    {
        if ($photo->vehicle_id !== $vehicle->id) {
            return back()->with('error', 'Photo not found for this vehicle.');
        }

        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }

     public function createInspections($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        return view(
            'motor_vehicle.create_inspections',
            compact('vehicle')
        );
    }

    public function storeInspections(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $request->validate([
            'inspection_date' => 'required',
            'inspector' => 'required',
            'result' => 'required',
            'mileage' => 'nullable|integer',
            'condition_score' => 'nullable|integer',
        ]);

        $reportPath = null;

        if ($request->hasFile('report_file')) {

            try {

                $file = $request->file('report_file');

                $fileName =
                    'vehicle_inspections/' .
                    $vehicle->id . '/' .
                    time() . '_' .
                    $file->getClientOriginalName();

                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => 'nyc3',
                    'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                    'credentials' => [
                        'key'    => 'DO00RP9FA3QZTA3JV637',
                        'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                    ],
                ]);

                $result = $s3Client->putObject([
                    'Bucket' => 'wfssystem',
                    'Key' => $fileName,
                    'Body' => fopen($file->getPathname(), 'r'),
                    'ACL' => 'public-read',
                    'ContentType' => $file->getMimeType(),
                ]);

                $reportPath = $result['ObjectURL'];

            } catch (AwsException $e) {

                Log::error($e->getMessage());

                return back()->with(
                    'error',
                    'Failed to upload inspection report.'
                );
            }
        }

        $inspection = new VehicleInspection();

        $inspection->vehicle_id = $vehicle->id;
        $inspection->inspection_date = $request->inspection_date;
        $inspection->inspector = $request->inspector;
        $inspection->inspection_type = $request->inspection_type;
        $inspection->mileage = $request->mileage;
        $inspection->condition_rating = $request->condition_rating;
        $inspection->result = $request->result;
        $inspection->notes = $request->notes;
        $inspection->condition_notes = $request->notes;
        $inspection->mechanical_condition = $request->mechanical_condition;
        $inspection->interior_condition = $request->interior_condition;
        $inspection->exterior_condition = $request->exterior_condition;
        $inspection->tyres_condition = $request->tyres_condition;
        $inspection->battery_condition = $request->battery_condition;
        $inspection->accessories_condition = $request->accessories_condition;
        $inspection->condition_score = $request->condition_score;
        $inspection->report_file_path = $reportPath;

        $inspection->save();

        $photoUrls = [];

        if ($request->hasFile('photos')) {

            foreach ($request->file('photos') as $photo) {

                try {

                    $s3Client = new S3Client([
                            'version' => 'latest',
                            'region'  => 'nyc3',
                            'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                            'credentials' => [
                                'key'    => 'DO00RP9FA3QZTA3JV637',
                                'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                            ],
                        ]);

                    $fileName =
                        'vehicle_inspections/' .
                        $vehicle->id .
                        '/photos/' .
                        time() . '_' .
                        uniqid() . '_' .
                        $photo->getClientOriginalName();

                    $result = $s3Client->putObject([
                        'Bucket' => 'wfssystem',
                        'Key' => $fileName,
                        'Body' => fopen($photo->getPathname(), 'r'),
                        'ACL' => 'public-read',
                    ]);

                    $photoUrl = $result['ObjectURL'];
                    $photoUrls[] = $photoUrl;

                    VehicleInspectionPhoto::create([
                        'vehicle_inspection_id' => $inspection->id,
                        'photo_url' => $photoUrl,
                    ]);

                } catch (AwsException $e) {

                    Log::error($e->getMessage());

                }

            }

        }

        if (!empty($photoUrls)) {
            $inspection->inspection_photos = $photoUrls;
            $inspection->save();
        }

        return redirect()->route('vehicles.ownership-verification.show', $vehicle->id)
            ->with(
                'success',
                'Inspection recorded successfully.'
            );
    }

    public function storeValuation(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $request->validate([
            'valuation_company' => 'required|string',
            'valuator_name' => 'nullable|string',
            'valuation_date' => 'required|date',
            'market_value' => 'required|numeric',
            'forced_sale_value' => 'nullable|numeric',
            'valuation_cost' => 'nullable|numeric',
            'expiry_date' => 'nullable|date',
        ]);

        $reportPath = null;

        if ($request->hasFile('report_file')) {
            try {
                $file = $request->file('report_file');
                $fileName = 'vehicle_valuations/' . $vehicle->id . '/' . time() . '_' . $file->getClientOriginalName();
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => 'nyc3',
                    'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                    'credentials' => [
                        'key' => 'DO00RP9FA3QZTA3JV637',
                        'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                    ],
                ]);
                $result = $s3Client->putObject([
                    'Bucket' => 'wfssystem',
                    'Key' => $fileName,
                    'Body' => fopen($file->getPathname(), 'r'),
                    'ACL' => 'public-read',
                    'ContentType' => $file->getMimeType(),
                ]);
                $reportPath = $result['ObjectURL'];
            } catch (AwsException $e) {
                Log::error($e->getMessage());
                return back()->with('error', 'Failed to upload valuation report.');
            }
        }

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                try {
                    $s3Client = new S3Client([
                        'version' => 'latest',
                        'region' => 'nyc3',
                        'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                        'credentials' => [
                            'key' => 'DO00RP9FA3QZTA3JV637',
                            'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                        ],
                    ]);
                    $fileName = 'vehicle_valuations/' . $vehicle->id . '/photos/' . time() . '_' . uniqid() . '_' . $photo->getClientOriginalName();
                    $result = $s3Client->putObject([
                        'Bucket' => 'wfssystem',
                        'Key' => $fileName,
                        'Body' => fopen($photo->getPathname(), 'r'),
                        'ACL' => 'public-read',
                    ]);
                    $photos[] = $result['ObjectURL'];
                } catch (AwsException $e) {
                    Log::error($e->getMessage());
                }
            }
        }

        $supportingDocs = [];
        if ($request->hasFile('supporting_documents')) {
            foreach ($request->file('supporting_documents') as $doc) {
                try {
                    $s3Client = new S3Client([
                        'version' => 'latest',
                        'region' => 'nyc3',
                        'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                        'credentials' => [
                            'key' => 'DO00RP9FA3QZTA3JV637',
                            'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                        ],
                    ]);
                    $fileName = 'vehicle_valuations/' . $vehicle->id . '/docs/' . time() . '_' . uniqid() . '_' . $doc->getClientOriginalName();
                    $result = $s3Client->putObject([
                        'Bucket' => 'wfssystem',
                        'Key' => $fileName,
                        'Body' => fopen($doc->getPathname(), 'r'),
                        'ACL' => 'public-read',
                        'ContentType' => $doc->getMimeType(),
                    ]);
                    $supportingDocs[] = $result['ObjectURL'];
                } catch (AwsException $e) {
                    Log::error($e->getMessage());
                }
            }
        }

        VehicleValuation::create([
            'vehicle_id' => $vehicle->id,
            'valuation_company' => $request->valuation_company,
            'valuator_name' => $request->valuator_name,
            'valuation_date' => $request->valuation_date,
            'market_value' => $request->market_value,
            'forced_sale_value' => $request->forced_sale_value,
            'valuation_cost' => $request->valuation_cost,
            'expiry_date' => $request->expiry_date,
            'report_file_path' => $reportPath,
            'photos' => $photos,
            'supporting_documents' => $supportingDocs,
        ]);

        return redirect()->route('vehicles.ownership-verification.show', $vehicle->id)
            ->with('success', 'Valuation recorded successfully.');
    }

    public function createValuation($vehicleId)
    {
        $vehicle = Vehicle::with('valuations')->findOrFail($vehicleId);

        return view('motor_vehicle.create_valuations', compact('vehicle'));
    }

 public function dashboard(Request $request)
{
    // Default dates: beginning of year to today
    $start_date = $request->start_date ?? Carbon::now()->startOfYear()->format('Y-m-d');
    $end_date = $request->end_date ?? Carbon::now()->format('Y-m-d');

    $today = Carbon::today();
    $thirtyDays = Carbon::today()->copy()->addDays(30);

    $insuranceReminders = VehicleInsurance::with('vehicle.client')
        ->whereDate('expiry_date', '<=', $thirtyDays)
        ->orderBy('expiry_date', 'asc')
        ->get();

    try {

        /*
        |--------------------------------------------------------------------------
        | Motor Vehicle Loan Information
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(60)->get(
            'https://lms2backend.whencefinancesystem.com/motor-vehicle-loans-info',
            [
                'start_date' => $start_date,
                'end_date'   => $end_date
            ]
        );


        if (!$response->successful()) {

            return back()->with(
                'error',
                'Unable to load motor vehicle dashboard data'
            );

        }


        $data = $response->json();


        /*
        |--------------------------------------------------------------------------
        | Motor Vehicle Loan Consultant Information
        |--------------------------------------------------------------------------
        */

        $consultantResponse = Http::timeout(60)->get(
            'https://lms2backend.whencefinancesystem.com/mv-loan-consultant-info',
            [
                'start_date' => $start_date,
                'end_date'   => $end_date
            ]
        );


        if (!$consultantResponse->successful()) {

            \Log::error(
                'MV Consultant Endpoint Error: ' .
                $consultantResponse->body()
            );

            $consultantData = [
                'success' => false,
                'consultants' => [],
                'national' => []
            ];

        } else {

            $consultantData = $consultantResponse->json();

        }


        /*
        |--------------------------------------------------------------------------
        | Make sure consultants always exists
        |--------------------------------------------------------------------------
        */

        $consultants = $consultantData['consultants'] ?? [];


        return view(
            'motor_vehicle.dashboard',
            compact(
                'data',
                'consultantData',
                'consultants',
                'start_date',
                'end_date',
                'insuranceReminders'
            )
        );


    } catch (\Exception $e) {

        \Log::error(
            'Motor Vehicle Dashboard Error: ' .
            $e->getMessage()
        );

        return back()->with(
            'error',
            'Error loading motor vehicle dashboard'
        );

    }
}
   

   public function MotorVehicleLoan(Request $request)
{
    $query = Loan::where('loan_product_id', 0);

    // Search (Loan ID or Client Name)
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhereHas('client', function ($client) use ($search) {
                    $client->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
              });
        });
    }

    // Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Branch Filter
    if ($request->filled('office')) {
        $query->where('office_id', $request->office);
    }

    // Date Filter
    if ($request->filled('date')) {
        $query->whereDate('created_date', $request->date);
    }

    $recentLoans = $query
        ->orderBy('created_date', 'desc')
        ->with([
            'client',
            'loanConsultant',
            'vehicle',
            'vehicle.photos',
            'vehicle.custody.receiver',
            'vehicle.inspections',
            'vehicle.valuations',
            'vehicle.valuations.valuator',
        ])
        ->paginate(20)
        ->appends($request->all());

    $statsLoans = (clone $query)->get();
    $closedLoanIds = $statsLoans->where('status', 'closed')->pluck('id');

    $stats = [
        'total' => $statsLoans->count(),
        'total_amount' => $statsLoans->sum('principal'),
        'pending' => $statsLoans->where('status', 'pending')->count(),
        'pending_amount' => $statsLoans->where('status', 'pending')->sum('principal'),
        'approved' => $statsLoans->where('status', 'approved')->count(),
        'approved_amount' => $statsLoans->where('status', 'approved')->sum('principal'),
        'disbursed' => $statsLoans->where('status', 'disbursed')->count(),
        'disbursed_amount' => $statsLoans->where('status', 'disbursed')->sum('principal'),
        'closed' => $statsLoans->where('status', 'closed')->count(),
        'total_collected' => LoanTransaction::whereIn('loan_id', $closedLoanIds)->sum('credit'),
    ];

    $offices = Office::orderBy('name')->get();

    $statuses = [];
    $allLoans = Loan::where('loan_product_id', 0)->with([
        'client',
        'vehicle.ownershipRecords',
        'complianceScreenings',
    ])->get();
    foreach ($allLoans as $loan) {
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

            if ($loan->vehicle) {
                $ownershipCompleted = $loan->vehicle->ownershipRecords->isNotEmpty();
            }
        }

        $statuses[$loan->id] = [
            'kyc_completed' => $kycCompleted,
            'compliance_screening_completed' => $complianceCompleted,
            'ownership_completed' => $ownershipCompleted,
        ];
    }

    return view(
        'motor_vehicle.motor_vehicle_loans',
        compact('recentLoans', 'offices', 'statuses', 'stats')
    );
}


public function loanDetailSheet(Request $request, $loanId)
{
    $loan = Loan::with([
        'client',
        'office',
        'loanConsultant',
        'vehicle',
        'vehicle.photos',
        'vehicle.custody.receiver',
        'vehicle.custody',
        'vehicle.valuations',
        'vehicle.valuations.valuator',
        'vehicle.inspections',
        'vehicle.insurancePolicies',
        'vehicle.documents',
        'vehicle.ownershipRecords',
    ])->findOrFail($loanId);

    return response()->view('motor_vehicle.partials._vehicle_detail_sheet', compact('loan'));
}


   public function MotorVehicles(Request $request)
{
    $query = Vehicle::with('client.office');

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('vehicle_code', 'like', "%{$search}%")
              ->orWhere('registration_number', 'like', "%{$search}%")
              ->orWhere('make', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhereHas('client', function ($client) use ($search) {

                    $client->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");

              });

        });
    }

    // Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Branch Filter
 // Branch Filter
if ($request->filled('office')) {
    $query->whereHas('client', function ($q) use ($request) {
        $q->where('office_id', $request->office);
    });
}

    // Registration Date Filter
    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
    }

    $vehicles = $query
        ->latest()
        ->paginate(20)
        ->appends($request->all());

    $offices = Office::orderBy('name')->get();

    return view(
        'motor_vehicle.motor_vehicles',
        compact('vehicles', 'offices')
    );
}



    public function sellVehicle(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'sale_value' => 'required|numeric|min:0',
            'buyer_fullname' => 'required|string|max:255',
            'buyer_phone' => 'required|string|max:50',
            'buyer_nrc_number' => 'required|string|max:50',
            'buyer_sex' => 'required|in:Male,Female',
            'buyer_location' => 'required|string|max:255'
        ]);

        $vehicle->forced_sale_value = $request->sale_value;
        $vehicle->status = 'sold';
        $vehicle->sold_at = date('Y-m-d H:i:s');
        $vehicle->buyer_fullname = $request->buyer_fullname;
        $vehicle->buyer_phone = $request->buyer_phone;
        $vehicle->buyer_nrc_number = $request->buyer_nrc_number;
        $vehicle->buyer_sex = $request->buyer_sex;
        $vehicle->buyer_location = $request->buyer_location;

        $vehicle->save();

        Flash::success("Vehicle marked as sold.");

        return redirect()->back();
    }

public function sales(Request $request)
{
    $query = Vehicle::with('client')
        ->where('status', 'sold');

    // Sold date filter
    if ($request->filled('start_date')) {
        $query->whereDate('sold_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('sold_at', '<=', $request->end_date);
    }

    // Registration search
    if ($request->filled('registration')) {
        $query->where('registration_number', 'LIKE', '%' . $request->registration . '%');
    }

    // Vehicle search
    if ($request->filled('vehicle')) {
        $query->where(function ($q) use ($request) {
            $q->where('make', 'LIKE', '%' . $request->vehicle . '%')
              ->orWhere('model', 'LIKE', '%' . $request->vehicle . '%');
        });
    }

    // Branch
    if ($request->filled('branch')) {
        $query->whereHas('client', function ($q) use ($request) {
            $q->where('office_id', $request->branch);
        });
    }

    $vehicles = $query
        ->orderBy('sold_at', 'desc')
        ->paginate(20);

    $vehicles->appends($request->all());

    $summary = clone $query;

    $carsSold = $summary->count();

    $totalSales = $summary->sum('forced_sale_value');

    $averageSale = $carsSold ? $totalSales / $carsSold : 0;

    $highestSale = $summary->max('forced_sale_value');

    return view(
        'motor_vehicle.sales',
        compact(
            'vehicles',
            'carsSold',
            'totalSales',
            'averageSale',
            'highestSale'
        )
    );
}

  public function loans_pending_approval(Request $request)
    {

        $query = Loan::whereIn('status', ['pending', 'approved'])
            ->where('loan_product_id', 0)
            ->with([
                'client',
                'loanConsultant',
                'approved_by',
                'created_by',
                'loan_officer',
                'originatingBranch.district',
                'originatingBranch.province',
                'district',
                'province',
                'vehicle.inspections',
                'vehicle.valuations',
                'vehicle.custody.receiver',
                'vehicle.photos',
                'vehicle.ownershipRecords',
                'vehicle.custody',
                'complianceScreenings',
            ]);

        if ($request->filled('office')) {
            $query->where('office_id', $request->office);
        }

        if ($request->filled('district')) {
            $query->whereHas('originatingBranch', function ($q) use ($request) {
                $q->where('district_id', $request->district);
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('originatingBranch', function ($q) use ($request) {
                $q->where('province_id', $request->province);
            });
        }

        if ($request->filled('staff')) {
            $staffId = $request->staff;
            $query->where(function ($q) use ($staffId) {
                $q->where('loan_consultant_id', $staffId)
                  ->orWhere('branch_assessor_id', $staffId)
                  ->orWhere('approved_by_id', $staffId)
                  ->orWhere('created_by_id', $staffId)
                  ->orWhere('loan_officer_id', $staffId);
            });
        }

        $data = $query->get();

        $stats = [
            'total' => $data->count(),
            'total_amount' => $data->sum('principal'),
            'pending' => $data->where('status', 'pending')->count(),
            'pending_amount' => $data->where('status', 'pending')->sum('principal'),
            'approved' => $data->where('status', 'approved')->count(),
            'approved_amount' => $data->where('status', 'approved')->sum('principal'),
        ];

        $statuses = [];
 
        foreach ($data as $loan) {
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

                $complianceCompleted = $loan->relationLoaded('complianceScreenings')
                    && $loan->complianceScreenings->whereIn('status', ['cleared', 'flagged', 'requires_review'])->isNotEmpty();

                $vehicle = $loan->vehicle;
                if ($vehicle && $vehicle->relationLoaded('ownershipRecords')) {
                    $ownershipCompleted = $vehicle->ownershipRecords->isNotEmpty();
                }
            }

            $statuses[$loan->id] = [
                'kyc_completed' => $kycCompleted,
                'compliance_screening_completed' => $complianceCompleted,
                'ownership_completed' => $ownershipCompleted,
            ];
        }

        // dd($statuses);
        $offices = Office::orderBy('name')->get();
        $districts = \App\Models\District::orderBy('name')->get();
        $provinces = \App\Models\Province::orderBy('name')->get();
        $staff = \App\Models\User::orderBy('first_name')->get();

        return view('motor_vehicle.loans_pending_approval', compact('data', 'statuses', 'offices', 'districts', 'provinces', 'staff', 'stats'));
    }


     public function analytics_dashboard(Request $request)
{


$start =
$request->start_date ??
date('Y-01-01');


$end =
$request->end_date ??
date('Y-m-d');



$response = Http::get(
'https://lms2backend.whencefinancesystem.com/motor-vehicle-loans-analytics',
[
'start_date'=>$start,
'end_date'=>$end
]
);



$data=$response->json();



return view(
    'motor_vehicle.analytics_dashboard',
    compact(
        'data',
        'start',
        'end'
    )
);
    }

    public function disposalRegister(Request $request)
    {
        $mvlService = app(\App\Services\MVLService::class);
        $mvlService->week1reminder();
        $mvlService->month1reminder();

        $query = Loan::where('loan_product_id', 0)
            ->where(function ($q) {
                $q->where('status', 'disbursed')
                  ->orWhere('status','defaulted');
            })
            ->whereNotNull('first_repayment_date')
            ->where('first_repayment_date', '<', Carbon::now()->subMonth())
            ->with([
                'client',
                'loanConsultant',
                'approved_by',
                'created_by',
                'loan_officer',
                'originatingBranch.district',
                'originatingBranch.province',
                'vehicle.inspections',
                'vehicle.valuations',
                'vehicle.custody.receiver',
                'vehicle.photos',
                'vehicle.ownershipRecords',
                'complianceScreenings',
            ]);


        if ($request->filled('office')) {
            $query->where('office_id', $request->office);
        }

        if ($request->filled('district')) {
            $query->whereHas('originatingBranch', function ($q) use ($request) {
                $q->where('district_id', $request->district);
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('originatingBranch', function ($q) use ($request) {
                $q->where('province_id', $request->province);
            });
        }

        if ($request->filled('staff')) {
            $staffId = $request->staff;
            $query->where(function ($q) use ($staffId) {
                $q->where('loan_consultant_id', $staffId)
                  ->orWhere('branch_assessor_id', $staffId)
                  ->orWhere('approved_by_id', $staffId)
                  ->orWhere('created_by_id', $staffId)
                  ->orWhere('loan_officer_id', $staffId);
            });
        }

        $loans = $query->orderBy('first_repayment_date', 'asc')->get();

        $stats = [
            'total' => $loans->count(),
            'total_amount' => $loans->sum('principal'),
            'defaulted' => $loans->where('defaulted', 'yes')->count(),
            'defaulted_amount' => $loans->where('defaulted', 'yes')->sum('principal'),
            'disbursed_overdue' => $loans->where('defaulted', '!=', 'yes')->count(),
            'disbursed_overdue_amount' => $loans->where('defaulted', '!=', 'yes')->sum('principal'),
        ];

        $statuses = [];
        foreach ($loans as $loan) {
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

                $complianceCompleted = $loan->relationLoaded('complianceScreenings')
                    && $loan->complianceScreenings->whereIn('status', ['cleared', 'flagged', 'requires_review'])->isNotEmpty();

                $vehicle = $loan->vehicle;
                if ($vehicle && $vehicle->relationLoaded('ownershipRecords')) {
                    $ownershipCompleted = $vehicle->ownershipRecords->isNotEmpty();
                }
            }

            $statuses[$loan->id] = [
                'kyc_completed' => $kycCompleted,
                'compliance_screening_completed' => $complianceCompleted,
                'ownership_completed' => $ownershipCompleted,
            ];
        }

        $offices = Office::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $staff = User::orderBy('first_name')->get();

        return view('motor_vehicle.disposal_register', compact('loans', 'statuses', 'offices', 'districts', 'provinces', 'staff', 'stats'));
    }


}



