<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeopleReportController extends Controller
{
    public function show()
    {
        return view('reports.people');
    }
}
