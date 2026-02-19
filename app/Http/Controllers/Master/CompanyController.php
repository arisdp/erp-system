<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $companies = Company::select('id', 'code', 'name', 'email');

            return DataTables::of($companies)
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-warning btn-edit"
                            data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-sm btn-danger btn-delete"
                            data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.companies.index');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:companies',
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $company = Company::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully',
            'data' => $company
        ]);
    }


    public function edit(Company $company)
    {
        return response()->json($company);
    }


    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'code' => 'required|unique:companies,code,' . $company->id,
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $company->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Company updated successfully'
        ]);
    }


    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Company deleted successfully'
        ]);
    }
}
