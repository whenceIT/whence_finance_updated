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
use App\Models\VehicleCustody;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Office;

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
        'forced_sale_value' => $request->forced_sale_value
    ]);

    return redirect('/vehicles')
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
            'insured_value' => 'required|numeric'
        ]);

        VehicleInsurance::create([
            'vehicle_id' => $vehicle->id,
            'insurer_name' => $request->insurer_name,
            'policy_number' => $request->policy_number,
            'start_date' => $request->start_date,
            'expiry_date' => $request->expiry_date,
            'insured_value' => $request->insured_value
        ]);

       return redirect("/vehicles/{$vehicle->id}")
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
    ]);

    return redirect('/vehicles/'.$vehicle->id)
        ->with('success', 'Vehicle successfully received into custody.');


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

        return redirect("/vehicles/{$vehicle->id}")
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

            return redirect("/vehicles/{$vehicle->id}")
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
            'result' => 'required'
        ]);

        $reportUrl = null;

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
                ]);

                $reportUrl = $result['ObjectURL'];

            } catch (AwsException $e) {

                Log::error($e->getMessage());

                return back()->with(
                    'error',
                    'Failed to upload inspection report.'
                );
            }
        }

$inspection = VehicleInspection::create([
    'vehicle_id' => $vehicle->id,
    'inspection_date' => $request->inspection_date,
    'inspector' => $request->inspector,
    'inspection_type' => $request->inspection_type,
    'mileage' => $request->mileage,
    'condition_rating' => $request->condition_rating,
    'result' => $request->result,
    'notes' => $request->notes,
    'report_url' => $reportUrl,
]);

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

            VehicleInspectionPhoto::create([
                'vehicle_inspection_id' => $inspection->id,
                'photo_url' => $result['ObjectURL'],
            ]);

        } catch (AwsException $e) {

            Log::error($e->getMessage());

        }

    }

}



        return redirect("/vehicles/{$vehicle->id}")
            ->with(
                'success',
                'Inspection recorded successfully.'
            );
    }



        public function dashboard(Request $request)
    {

        // Default dates: beginning of year to today
        $start_date = $request->start_date ?? Carbon::now()->startOfYear()->format('Y-m-d');

        $end_date = $request->end_date ?? Carbon::now()->format('Y-m-d');


        try {


            $response = Http::timeout(60)->get(
                'https://lms2backend.whencefinancesystem.com/motor-vehicle-loans-info',
                [
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]
            );


            if (!$response->successful()) {

                return back()->with(
                    'error',
                    'Unable to load motor vehicle dashboard data'
                );

            }


            $data = $response->json();



            return view(
                'motor_vehicle.dashboard',
                compact(
                    'data',
                    'start_date',
                    'end_date'
                )
            );


        } catch (\Exception $e) {


            \Log::error(
                'Motor Vehicle Dashboard Error: '.$e->getMessage()
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
        ->paginate(20)
        ->appends($request->all());

    $offices = Office::orderBy('name')->get();

    return view(
        'motor_vehicle.motor_vehicle_loans',
        compact('recentLoans', 'offices')
    );
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



}