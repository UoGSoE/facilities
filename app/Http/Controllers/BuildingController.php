<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class BuildingController extends Controller
{
    public function show(Building $building)
    {
        $building->load('rooms.desks.owner', 'rooms.lockers.owner');
        return view('building.show', [
            'building' => $building,
        ]);
    }

    public function create()
    {
        return view('building.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('buildings'),
            ],
        ]);

        Building::create([
            'name' => $data['name'],
        ]);

        return redirect()->route('home')->with(['success', 'Building Created']);
    }

    public function edit(Building $building)
    {
        return view('building.edit', [
            'building' => $building,
        ]);
    }

    public function update(Request $request, Building $building)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('buildings')->ignore($building->id),
            ],
        ]);

        $building->update([
            'name' => $data['name'],
        ]);

        return redirect()->route('building.show', $building)->with(['success', 'Building Updated']);
    }
}
