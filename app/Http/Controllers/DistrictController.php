<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::with('province')->get();
        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $district = District::create($request->all());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'District created successfully',
                'data' => $district
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create district',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $district = District::with('province')->find($id);
        
        if (!$district) {
            return response()->json([
                'success' => false,
                'message' => 'District not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $district
        ]);
    }

    public function update(Request $request, $id)
    {
        $district = District::find($id);
        
        if (!$district) {
            return response()->json([
                'success' => false,
                'message' => 'District not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'province_id' => 'sometimes|required|exists:provinces,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $district->update($request->all());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'District updated successfully',
                'data' => $district
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update district',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $district = District::find($id);
        
        if (!$district) {
            return response()->json([
                'success' => false,
                'message' => 'District not found'
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            $district->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'District deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete district',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDistrictsWithStats()
    {
        $districts = District::with(['province', 'clients' => function($query) {
            $query->where('status', 'active');
        }, 'loans' => function($query) {
            $query->where('status', 'disbursed');
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }
}