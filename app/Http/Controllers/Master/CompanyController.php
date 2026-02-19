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

            $data = Company::query();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                    <button onclick="editCompany(' . $row->id . ')" 
                        class="btn btn-sm btn-warning">Edit</button>
                ';
                })
                ->make(true);
        }

        return view('master.companies.index');
    }

    public function create()
    {
        return view('master.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:companies',
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $company = Company::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $company
        ]);
    }


    public function edit(Company $company)
    {
        return view('master.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'code' => 'required|unique:companies,code,' . $company->id,
            'name' => 'required',
        ]);

        $company->update($request->all());

        return redirect()->route('companies.index')
            ->with('success', 'Company updated');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted');
    }
}
