<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $query = AuditLog::with('user');

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [$request->tanggal_mulai . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59']);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.audit-logs.index', compact('logs', 'users'));
    }

    public function show($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $log = AuditLog::with('user')->findOrFail($id);

        $log->old_values_pretty = $log->old_values ? json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT) : null;
        $log->new_values_pretty = $log->new_values ? json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT) : null;

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }
}

