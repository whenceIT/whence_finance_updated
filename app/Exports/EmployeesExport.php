<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->employees);
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID', 'Office Name', 'Province Name', 'Email', 'First Name', 'Last Name', 'Status',
            'Phone', 'Date of Birth', 'Date of Joining', 'Marital Status', 'Branch', 'Salary Mode',
            'Bank Name', 'Bank Account Number', 'Health Details', 'Health Insurance Provider',
            'Health Insurance Number', 'External Company', 'External Designation', 'Internal Designation',
            'Created At', 'Updated At', 'Mobile Number', 'Current Address', 'Relation to Emergency',
            'Confirmation Date', 'Qualification', 'School/University', 'Level of Education',
            'Year Completed', 'Major', 'TPIN', 'SSN', 'NHIMA', 'Salary Details', 'Deleted At',
            'User Status', 'Days in Institution', 'Months in Institution', 'Years in Institution',
            'Time Spent', 'Employment Category'
        ];
    }

    /**
    * @var mixed $employee
    * @return array
    */
    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->office_name,
            $employee->province_name,
            $employee->email,
            $employee->first_name,
            $employee->last_name,
            $employee->status,
            $employee->phone,
            $employee->date_of_birth,
            $employee->date_of_joining,
            $employee->marital_status,
            $employee->branch,
            $employee->salary_mode,
            $employee->bank_name,
            $employee->bank_account_number,
            $employee->health_details,
            $employee->health_insurance_provider,
            $employee->health_insurance_number,
            $employee->external_company,
            $employee->external_designation,
            $employee->internal_designation,
            $employee->created_at,
            $employee->updated_at,
            $employee->mobile_number,
            $employee->current_address,
            $employee->relation_to_emergency,
            $employee->confirmation_date,
            $employee->qualification,
            $employee->school_university,
            $employee->level_of_education,
            $employee->year_completed,
            $employee->major,
            $employee->tpin,
            $employee->ssn,
            $employee->nhima,
            $employee->salary_details,
            $employee->deleted_at,
            $employee->user_status,
            $employee->days_in_institution,
            $employee->months_in_institution,
            $employee->years_in_institution,
            $employee->time_spent,
            $employee->employment_category
        ];
    }
}