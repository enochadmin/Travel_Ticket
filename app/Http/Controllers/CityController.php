<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::ordered()->paginate(20);

        return view('settings.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('settings.cities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'region' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        City::create([
            'name' => trim($validated['name']),
            'region' => $validated['region'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('settings.cities.index')->with('success', 'City added successfully.');
    }

    public function edit(City $city)
    {
        return view('settings.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'region' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $city->update([
            'name' => trim($validated['name']),
            'region' => $validated['region'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('settings.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('settings.cities.index')->with('success', 'City removed successfully.');
    }
}
