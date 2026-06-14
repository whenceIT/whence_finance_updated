<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvincialTransaction;
use Illuminate\Support\Facades\Storage;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ProvincialLedgerApiController extends Controller
{
    public function index()
    {
        $transactions = ProvincialTransaction::with('province')->orderBy('created_at', 'desc')->get();
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
            'recorded_at' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $data = $validated;
        $user = Sentinel::getUser();
        $data['created_by'] = $user ? $user->id : null;
        $data['recorded_at'] = $data['recorded_at'] ?? now();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('provincial-transactions', 'public');
            $data['file_path'] = $path;
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
            'recorded_at' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($transaction->file_path) {
                Storage::disk('public')->delete($transaction->file_path);
            }
            $path = $request->file('file')->store('provincial-transactions', 'public');
            $validated['file_path'] = $path;
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
            Storage::disk('public')->delete($transaction->file_path);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully.'
        ]);
    }
}