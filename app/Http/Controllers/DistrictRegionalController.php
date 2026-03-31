<?php

namespace App\Http\Controllers;

use App\Models\DistrictRegional;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;

class DistrictRegionalController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }
    public function index()
    {
        // if (!Sentinel::hasAccess('district-regionals.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districtRegionals = DistrictRegional::with(['district', 'province'])->get();
        return view('district_regional.data', compact('districtRegionals'));
    }

    public function create()
    {
        // if (!Sentinel::hasAccess('district-regionals.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        return view('district_regional.create');
    }

    public function edit($id)
    {
        // if (!Sentinel::hasAccess('district-regionals.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districtRegional = DistrictRegional::find($id);

        if (!$districtRegional) {
            Flash::warning('District Regional not found');
            return redirect('district-regionals');
        }

        return view('district_regional.edit', compact('districtRegional'));
    }

    public function store(Request $request)
    {
        // if (!Sentinel::hasAccess('district-regionals.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $validator = Validator::make($request->all(), [
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:districts,id',
            'district_regional_names' => 'required|array|min:1',
            'district_regional_names.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $createdDistrictRegionals = 0;
            foreach ($request->district_regional_names as $districtRegionalName) {
                if (!empty(trim($districtRegionalName))) {
                    DistrictRegional::create([
                        'name' => trim($districtRegionalName),
                        'district_id' => $request->district_id,
                        'province_id' => $request->province_id,
                    ]);
                    $createdDistrictRegionals++;
                }
            }

            DB::commit();

            Flash::success("Successfully created {$createdDistrictRegionals} district regional(s)");
            return redirect('district-regionals');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::warning('Failed to create district regionals: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        // if (!Sentinel::hasAccess('district-regionals.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districtRegional = DistrictRegional::with(['district', 'province'])->find($id);

        if (!$districtRegional) {
            Flash::warning('District Regional not found');
            return redirect('district-regionals');
        }

        return view('district_regional.show', compact('districtRegional'));
    }

    public function update(Request $request, $id)
    {
        // if (!Sentinel::hasAccess('district-regionals.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districtRegional = DistrictRegional::find($id);

        if (!$districtRegional) {
            Flash::warning('District Regional not found');
            return redirect('district-regionals');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'district_id' => 'sometimes|required|exists:districts,id',
            'province_id' => 'sometimes|required|exists:province,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $districtRegional->update($request->all());

            DB::commit();

            Flash::success(trans('general.successfully_saved'));
            return redirect('district-regionals');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::warning('Failed to update district regional: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        // if (!Sentinel::hasAccess('district-regionals.delete')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districtRegional = DistrictRegional::find($id);

        if (!$districtRegional) {
            Flash::warning('District Regional not found');
            return redirect('district-regionals');
        }

        try {
            DB::beginTransaction();

            $districtRegional->delete();

            DB::commit();

            Flash::success(trans('general.successfully_deleted'));
            return redirect('district-regionals');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::warning('Failed to delete district regional: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function getDistrictRegionalsWithStats()
    {
        // if (!Sentinel::hasAccess('district-regionals.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $districtRegionals = DistrictRegional::with(['district', 'province', 'clients' => function($query) {
            $query->where('status', 'active');
        }, 'loans' => function($query) {
            $query->where('status', 'disbursed');
        }])->get();

        return view('district_regional.stats', compact('districtRegionals'));
    }
}