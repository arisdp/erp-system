<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:job_positions,id',
            'employee_code' => 'required|string|unique:employees,employee_code,' . ($this->employee ? $this->employee->id : 'NULL') . ',id,company_id,' . auth()->user()->company_id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'join_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
