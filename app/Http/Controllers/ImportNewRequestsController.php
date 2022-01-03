<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ohffs\SimpleSpout\ExcelSheet;
use App\Jobs\ProcessNewRequestsBatch;

class ImportNewRequestsController extends Controller
{
    public function create()
    {
        return view('imports.new_requests');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sheet' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        ProcessNewRequestsBatch::dispatch((new ExcelSheet)->import($request->file('sheet')), $request->user()->id);

        return redirect(route('home'))->with('success', 'Import started - you will get an email once it is complete.');
    }
}
