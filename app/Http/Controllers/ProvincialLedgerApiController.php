<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvincialTransaction;
use Illuminate\Support\Facades\Storage;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Aws\S3\S3Client;

class ProvincialLedgerApiController extends Controller
{
    public function index()
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;

        $query = ProvincialTransaction::query();
        if ($province_id) {
            $query->where('province_id', $province_id);
        }
        
        $monthsAgo = now()->subMonths(3)->startOfMonth();
        $query->where('created_at', '>=', $monthsAgo);
        
        $transactions = $query->with('province')->orderBy('created_at', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'province_id' => 'required|exists:province,id',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $data = $validated;
        $user = Sentinel::getUser();
        $data['created_by'] = $user ? $user->id : null;
        $data['recorded_at'] = now();

        if ($data['type'] === 'expense') {
            $totalIncome = ProvincialTransaction::where('type', 'income')->sum('amount');
            $totalExpenses = ProvincialTransaction::where('type', 'expense')->sum('amount');
            $netBalance = $totalIncome - $totalExpenses;

            if ($data['amount'] > $netBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense amount exceeds current net balance.',
                    'net_balance' => $netBalance
                ], 422);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9\./_-]/', '', $file->getClientOriginalName());
            
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
                'Bucket' => 'wfspolicies',
                'Key' => 'proof-docs/' . $fileName,
                'Body' => fopen($file->getPathname(), 'r'),
                'ACL' => 'public-read',
                'ContentType' => $file->getClientMimeType(),
            ]);

            $data['file_path'] = $result['ObjectURL'];
        }

        $transaction = ProvincialTransaction::create($data);

        return response()->json([
            'success' => true,
            'message' => ucfirst($transaction->type) . ' recorded successfully.',
            'data' => $transaction
        ], 201);
    }

    public function show($id)
    {
        $transaction = ProvincialTransaction::with('province')->find($id);
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $transaction]);
    }

    public function update(Request $request, $id)
    {
        $transaction = ProvincialTransaction::find($id);
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:income,expense',
            'province_id' => 'sometimes|exists:province,id',
            'transaction_date' => 'sometimes|date',
            'reference_number' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9\./_-]/', '', $file->getClientOriginalName());
            
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
                'Bucket' => 'wfspolicies',
                'Key' => 'policies/' . $fileName,
                'Body' => fopen($file->getPathname(), 'r'),
                'ACL' => 'public-read',
                'ContentType' => $file->getClientMimeType(),
            ]);

            $validated['file_path'] = $result['ObjectURL'];
        }

        $transaction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully.',
            'data' => $transaction
        ]);
    }

    public function destroy($id)
    {
        $transaction = ProvincialTransaction::find($id);
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        if ($transaction->file_path) {
            $oldPath = $transaction->file_path;
            $pathInfo = parse_url($oldPath, PHP_URL_PATH);
            $key = basename($pathInfo);
            
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => 'nyc3',
                'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                'credentials' => [
                    'key' => 'DO00RP9FA3QZTA3JV637',
                    'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                ],
            ]);
            
            try {
                $s3Client->deleteObject([
                    'Bucket' => 'wfspolicies',
                    'Key' => 'policies/' . $key,
                ]);
            } catch (\Exception $e) {
                // Ignore deletion errors
            }
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully.'
        ]);
    }
}