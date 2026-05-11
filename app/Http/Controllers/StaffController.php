<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function updatePosition(Request $request)
    {
        $data = $request->validate([
            'positionId' => 'required|integer|exists:job_positions,id',
            'officeId' => 'required|integer|exists:offices,id',
            'numOfVacancies' => 'nullable|integer|min:0',
            'numOfActive' => 'nullable|integer|min:0',
        ]);

        $position = Position::find($data['positionId']);
        $vacancy = Vacancy::updateOrCreate(
            [
                'position_id' => $position->id,
                'office_id' => $data['officeId'],
            ],
            [
                'num_of_vacancies' => $data['numOfVacancies'] ?? 0,
                'status' => 'Open',
            ]
        );

        $position->update([
            'num_of_vacancies' => $data['numOfVacancies'] ?? 0,
            'num_of_active' => $data['numOfActive'] ?? 0,
            'status' => 'Open',
            'is_vacant' => ($data['numOfVacancies'] ?? 0) > 0 ? 1 : 0,
            'date_added' => now(),
            'posted_date' => now(),
        ]);

        return redirect()->route('goa.vacancies-and-staffing')->with('success', 'Vacancy saved successfully.');
    }

    public function storeDepartment(Request $request)
    {
        $data = $request->validate([
            'departmentName' => 'required|string|max:255',
            'departmentHead' => 'nullable|string|max:255',
            'departmentCapacity' => 'nullable|integer|min:0',
            'departmentNotes' => 'nullable|string',
        ]);

        Department::create([
            'name' => $data['departmentName'],
            'head' => $data['departmentHead'],
            'capacity' => $data['departmentCapacity'] ?? 0,
            'notes' => $data['departmentNotes'],
        ]);

        return redirect()->route('goa.vacancies-and-staffing')->with('success', 'Department added successfully.');
    }

    public function storeRole(Request $request)
    {
        $data = $request->validate([
            'roleTitle' => 'required|string|max:255',
            'roleDepartment' => 'nullable|integer',
            'roleLevel' => 'required|in:Entry,Mid,Senior',
            'roleDescription' => 'nullable|string',
        ]);

        Position::create([
            'name' => $data['roleTitle'],
            'job_description' => $data['roleDescription'],
            'is_vacant' => 0, // new position, not vacant
            'num_of_vacancies' => 0,
            'num_of_active' => 0,
            'department_id' => $data['roleDepartment'],
            'posted_date' => null,
            'status' => 'Active',
            'date_added' => now(),
        ]);

        return redirect()->route('goa.vacancies-and-staffing')->with('success', 'Role added successfully.');
    }
}
