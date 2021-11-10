<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function show(People $person)
    {
        $person->load(['supervisor', 'desks.room.building', 'lockers.room.building']);
        return view('people.show', [
            'person' => $person,
        ]);
    }
}
