<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\People;
use Illuminate\Http\Request;

class ItAssetReportController extends Controller
{
    public function show()
    {
        return view('reports.itassets', [
            'people' => People::has('itAssets')->active()->orderBy('end_at')->with('itAssets')->get(),
        ]);
    }
}
