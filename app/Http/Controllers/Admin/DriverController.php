<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DriverDestroyAction;
use App\Actions\Admin\DriverStoreAction;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = User::query()
            ->with('UserGroup')
            ->whereHas('UserGroup', function ($userGroup) {
                $userGroup->whereHas('Group', function ($group) {
                    $group->whereHas('App', function ($app) {
                        $app->where('AppCode', 'lgi-booking');
                    });
                })
                    ->where('GroupCode', RoleEnum::DRIVER);
            })
        ->get();

        return view('admin.driver.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.driver.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DriverStoreAction $action)
    {
        $action->handle($request->string('nik'));

        return to_route('admin.drivers.index')
            ->with('success', 'Meeting Room created successfully.');
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
    public function edit() {}

    /**
     * Update the specified resource in storage.
     */
    public function update() {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $driver, DriverDestroyAction $action)
    {
        $action->handle($driver);

        return to_route('admin.drivers.index')
            ->with('success', 'Meeting Room deleted successfully.');
    }
}
