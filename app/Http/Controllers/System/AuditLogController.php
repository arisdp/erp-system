<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = AuditLog::with('user')->select(['audit_logs.*']);
            return DataTables::of($logs)
                ->addColumn('user_name', function($row){ return $row->user->name ?? 'System'; })
                ->editColumn('created_at', function($row){ return $row->created_at->format('Y-m-d H:i:s'); })
                ->editColumn('old_values', function($row){ return '<pre><small>'.json_encode($row->old_values, JSON_PRETTY_PRINT).'</small></pre>'; })
                ->editColumn('new_values', function($row){ return '<pre><small>'.json_encode($row->new_values, JSON_PRETTY_PRINT).'</small></pre>'; })
                ->rawColumns(['old_values', 'new_values'])
                ->make(true);
        }
        return view('system.audit_logs.index');
    }
}
