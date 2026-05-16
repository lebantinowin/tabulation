<?php

namespace App\Http\Controllers;

use App\Models\AdminReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReportController extends Controller
{
    public function index()
    {
        $contributions = AdminReport::with('admin')
            ->where('type', 'contribution')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('admin.reports.index', compact('contributions'));
    }

    public function create()
    {
        return view('admin.reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'  => 'required|in:contribution,bug',
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        AdminReport::create([
            'admin_id' => Auth::id(),
            'type'     => $request->type,
            'title'    => $request->title,
            'body'     => $request->body,
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Report submitted successfully.');
    }
}
