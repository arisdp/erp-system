<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class JournalController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $entries = JournalEntry::with(['createdBy', 'fiscalYear'])
                ->select(['id', 'journal_number', 'journal_date', 'description', 'status', 'created_by', 'fiscal_year_id']);

            return DataTables::of($entries)
                ->editColumn('journal_date', function($row){ return $row->journal_date->format('Y-m-d'); })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('journals.show', $row->id) . '" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    ';
                })
                ->make(true);
        }

        return view('general.journals.index');
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_postable', true)->orderBy('code')->get();
        $fiscalYears = FiscalYear::where('is_closed', false)->orderBy('year', 'desc')->get();
        
        return view('general.journals.create', compact('accounts', 'fiscalYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'journal_date' => 'required|date',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'description' => 'nullable|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        try {
            $data = $validated;
            $data['company_id'] = auth()->user()->company_id;
            $data['branch_id'] = auth()->user()->branch_id;
            
            $this->journalService->createJournal($data);

            return response()->json(['status' => 'success', 'message' => 'Journal entry created successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function show(JournalEntry $journal)
    {
        $journal->load(['lines.account', 'createdBy', 'fiscalYear']);
        return view('general.journals.show', compact('journal'));
    }
}
