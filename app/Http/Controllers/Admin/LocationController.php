<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\LocationDestroyAction;
use App\Actions\Admin\LocationStoreAction;
use App\Actions\Admin\LocationUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationStoreRequest;
use App\Http\Requests\Admin\LocationUpdateRequest;
use App\Models\Location;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::all();

        return view('admin.location.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.location.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationStoreRequest $request, LocationStoreAction $action)
    {
        $action->handle($request->validated());

        return to_route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('admin.location.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationUpdateRequest $request, Location $location, LocationUpdateAction $action)
    {
        $action->handle($location, $request->validated());

        return to_route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location, LocationDestroyAction $action)
    {
        $action->handle($location);

        return to_route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
