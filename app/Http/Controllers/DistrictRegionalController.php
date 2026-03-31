<?php

namespace App\Http\Controllers;

use App\Models\DistrictRegional;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DistrictRegionalController extends Controller
{
    public function index()
    {
        $districtRegionals = DistrictRegional::with(['district', 'province'])->get();
        return response()->json([
            'success' => true,
            'data' => $districtRegionals
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
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
            
            $districtRegional = DistrictRegional::create($request->all());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'District Regional created successfully',
                'data' => $districtRegional
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create district regional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $districtRegional = DistrictRegional::with(['district', 'province'])->find($id);
        
        if (!$districtRegional) {
            return response()->json([
                'success' => false,
                'message' => 'District Regional not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $districtRegional
        ]);
    }

    public function update(Request $request, $id)
    {
        $districtRegional = DistrictRegional::find($id);
        
        if (!$districtRegional) {
            return response()->json([
                'success' => false,
                'message' => 'District Regional not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'district_id' => 'sometimes|required|exists:districts,id',
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
            
            $districtRegional->update($request->all());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'District Regional updated successfully',
                'data' => $districtRegional
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update district regional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $districtRegional = DistrictRegional::find($id);
        
        if (!$districtRegional) {
            return response()->json([
                'success' => false,
                'message' => 'District Regional not found'
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            $districtRegional->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'District Regional deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete district regional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDistrictRegionalsWithStats()
    {
        $districtRegionals = DistrictRegional::with(['district', 'province', 'clients' => function($query) {
            $query->where('status', 'active');
        }, 'loans' => function($query) {
            $query->where('status', 'disbursed');
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $districtRegionals
        ]);
    }
}