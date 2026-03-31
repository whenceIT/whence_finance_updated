<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;

class DistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }
    public function index()
    {
        // if (!Sentinel::hasAccess('districts.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districts = District::with('province')->get();
        return view('district.data', compact('districts'));
    }

    public function create()
    {
        // if (!Sentinel::hasAccess('districts.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        return view('district.create');
    }

    public function edit($id)
    {
        // if (!Sentinel::hasAccess('districts.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $district = District::find($id);

        if (!$district) {
            Flash::warning('District not found');
            return redirect('districts');
        }

        return view('district.edit', compact('district'));
    }

    public function store(Request $request)
    {
        // if (!Sentinel::hasAccess('districts.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $validator = Validator::make($request->all(), [
            'province_id' => 'required|exists:province,id',
            'district_names' => 'required|array|min:1',
            'district_names.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $createdDistricts = 0;
            foreach ($request->district_names as $districtName) {
                if (!empty(trim($districtName))) {
                    District::create([
                        'name' => trim($districtName),
                        'province_id' => $request->province_id,
                    ]);
                    $createdDistricts++;
                }
            }

            DB::commit();

            Flash::success("Successfully created {$createdDistricts} district(s)");
            return redirect('districts');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::warning('Failed to create districts: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        // if (!Sentinel::hasAccess('districts.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $district = District::with('province')->find($id);

        if (!$district) {
            Flash::warning('District not found');
            return redirect('districts');
        }

        return view('district.show', compact('district'));
    }

    public function update(Request $request, $id)
    {
        // if (!Sentinel::hasAccess('districts.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $district = District::find($id);

        if (!$district) {
            Flash::warning('District not found');
            return redirect('districts');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'province_id' => 'sometimes|required|exists:province,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $district->update($request->all());

            DB::commit();

            Flash::success(trans('general.successfully_saved'));
            return redirect('districts');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::warning('Failed to update district: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        // if (!Sentinel::hasAccess('districts.delete')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $district = District::find($id);

        if (!$district) {
            Flash::warning('District not found');
            return redirect('districts');
        }

        try {
            DB::beginTransaction();

            $district->delete();

            DB::commit();

            Flash::success(trans('general.successfully_deleted'));
            return redirect('districts');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::warning('Failed to delete district: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function getDistrictsWithStats()
    {
        // if (!Sentinel::hasAccess('districts.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districts = District::with(['province', 'clients' => function($query) {
            $query->where('status', 'active');
        }, 'loans' => function($query) {
            $query->where('status', 'disbursed');
        }])->get();

        return view('district.stats', compact('districts'));
    }
}