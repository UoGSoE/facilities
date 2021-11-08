<?php

namespace App\Http\Controllers\Reports;

use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PendingReportController extends Controller
{
    public function show()
    {
        return view('reports.pending', [
            'people' => People::pending()->with('supervisor')->orderBy('start_at')->get(),
        ]);
    }
}
