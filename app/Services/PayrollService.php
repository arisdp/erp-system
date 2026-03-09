<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PayrollDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function generateMonthlyPayroll($companyId, $month, $year)
    {
        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        foreach ($employees as $employee) {
            $this->calculateEmployeePayroll($employee, $month, $year);
        }
    }

    public function calculateEmployeePayroll(Employee $employee, $month, $year)
    {
        return DB::transaction(function () use ($employee, $month, $year) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // 1. Get Base Salary
            $basicSalary = $employee->basic_salary ?? 0;

            // 2. Calculate Work Days (Presence)
            $attendanceCount = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'Present')
                ->count();

            // 3. Calculate Overtime Hours
            $overtimeHours = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('overtime_hours');

            // 4. Calculate Unpaid Leave Days
            $unpaidLeaveDays = LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'Approved')
                ->whereHas('leaveType', function ($query) {
                    $query->where('is_paid', false);
                })
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })
                ->sum('total_days');

            // 5. Initialize Components
            $totalAllowance = 0;
            $totalDeduction = 0;
            $details = [];

            // Get standard components (Meal Allowance, Overtime)
            $mealComponent = PayrollComponent::where('company_id', $employee->company_id)
                ->where('name', 'LIKE', '%Makan%')
                ->first();
            
            if ($mealComponent) {
                $amount = $attendanceCount * $mealComponent->default_amount;
                $totalAllowance += $amount;
                $details[] = [
                    'component_id' => $mealComponent->id,
                    'amount' => $amount
                ];
            }

            $overtimeComponent = PayrollComponent::where('company_id', $employee->company_id)
                ->where('name', 'LIKE', '%Lembur%')
                ->first();

            if ($overtimeComponent) {
                $amount = $overtimeHours * $overtimeComponent->default_amount;
                $totalAllowance += $amount;
                $details[] = [
                    'component_id' => $overtimeComponent->id,
                    'amount' => $amount
                ];
            }

            // Unpaid Leave Deduction (Approximation: Basic Salary / 25 days)
            if ($unpaidLeaveDays > 0) {
                $deductionPerDay = $basicSalary / 25;
                $amount = $unpaidLeaveDays * $deductionPerDay;
                $totalDeduction += $amount;
                // We'll tag this as a generic deduction for now or create a component
                $details[] = [
                    'component_id' => null, // Manual deduction for unpaid leave
                    'amount' => $amount,
                    'note' => 'Unpaid Leave: ' . $unpaidLeaveDays . ' days'
                ];
            }

            // 6. Create or Update Payroll Record
            $payroll = Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'period_month' => $month,
                    'period_year' => $year,
                ],
                [
                    'company_id' => $employee->company_id,
                    'basic_salary' => $basicSalary,
                    'total_allowance' => $totalAllowance,
                    'total_deduction' => $totalDeduction,
                    'net_salary' => ($basicSalary + $totalAllowance) - $totalDeduction,
                    'status' => 'Draft'
                ]
            );

            // 7. Save Details
            $payroll->details()->delete();
            foreach ($details as $detail) {
                $payroll->details()->create([
                    'company_id' => $employee->company_id,
                    'component_id' => $detail['component_id'],
                    'amount' => $detail['amount']
                ]);
            }

            return $payroll;
        });
    }
}
