<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\District;
use App\Models\Office;
use App\Models\Loan;
use Carbon\Carbon;

class DefaultersController extends Controller
{
    /**
     * Show the defaulters list page with provinces, districts, offices data.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $provinces = Province::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $offices = Office::orderBy('name')->get();

        return view('recoveries.defaulters', compact('provinces', 'districts', 'offices'));
    }

    /**
     * Get province stats: count of overdue loans per province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function provinceStats()
    {
        $today = Carbon::now()->toDateString();

        $provinces = Province::select('province.id', 'province.name')
            ->leftJoin('offices', 'offices.province_id', '=', 'province.id')
            ->leftJoin('loans', function ($join) use ($today) {
                $join->on('loans.office_id', '=', 'offices.id')
                    ->where('loans.first_repayment_date', '<', $today);
            })
            ->groupBy('province.id', 'province.name')
            ->orderBy('province.name')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'loan_count' => (int) $p->loan_count,
                ];
            });

        return response()->json($provinces);
    }

    /**
     * Get district stats within a province: count of overdue loans per district.
     *
     * @param int $provinceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function districtStats($provinceId)
    {
        $today = Carbon::now()->toDateString();

        $districts = District::select('districts.id', 'districts.name', 'districts.province_id')
            ->leftJoin('offices', 'offices.district_id', '=', 'districts.id')
            ->leftJoin('loans', function ($join) use ($today) {
                $join->on('loans.office_id', '=', 'offices.id')
                    ->where('loans.first_repayment_date', '<', $today);
            })
            ->where('districts.province_id', $provinceId)
            ->groupBy('districts.id', 'districts.name', 'districts.province_id')
            ->orderBy('districts.name')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->name,
                    'province_id' => $d->province_id,
                    'loan_count' => (int) $d->loan_count,
                ];
            });

        return response()->json($districts);
    }

    /**
     * Get office stats within a district: count of overdue loans per office.
     *
     * @param int $districtId
     * @return \Illuminate\Http\JsonResponse
     */
    public function officeStats($districtId)
    {
        $today = Carbon::now()->toDateString();

        $offices = Office::select('offices.id', 'offices.name', 'offices.district_id', 'offices.province_id')
            ->leftJoin('loans', function ($join) use ($today) {
                $join->on('loans.office_id', '=', 'offices.id')
                    ->where('loans.first_repayment_date', '<', $today);
            })
            ->where('offices.district_id', $districtId)
            ->groupBy('offices.id', 'offices.name', 'offices.district_id', 'offices.province_id')
            ->orderBy('offices.name')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'name' => $o->name,
                    'district_id' => $o->district_id,
                    'province_id' => $o->province_id,
                    'loan_count' => (int) $o->loan_count,
                ];
            });

        return response()->json($offices);
    }

    /**
     * Get overdue loan results for a specific office.
     *
     * @param int $officeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function officeLoans($officeId)
    {
        $today = Carbon::now()->toDateString();

        $loans = Loan::select(
            'loans.id',
            'loans.account_number',
            'loans.principal',
            'loans.status',
            'loans.first_repayment_date',
            'clients.first_name',
            'clients.last_name',
            'clients.nrc_number',
            'clients.phone_primary'
        )
            ->join('clients', 'clients.id', '=', 'loans.client_id')
            ->where('loans.office_id', $officeId)
            ->whereNotNull('loans.first_repayment_date')
            ->where('loans.first_repayment_date', '<', $today)
            ->orderBy('loans.first_repayment_date', 'asc')
            ->get();

        return response()->json($loans);
    }
}
