<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\MeetingRoomDestroyAction;
use App\Actions\Admin\MeetingRoomStoreAction;
use App\Actions\Admin\MeetingRoomUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MeetingRoomStoreRequest;
use App\Http\Requests\Admin\MeetingRoomUpdateRequest;
use App\Models\Location;
use App\Models\MeetingRoom;

class MeetingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meetingRooms = MeetingRoom::with('location')->get();

        return view('admin.meeting-room.index', compact('meetingRooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Location::pluck('name', 'id');

        return view('admin.meeting-room.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MeetingRoomStoreRequest $request, MeetingRoomStoreAction $action)
    {
        $action->handle($request->validated());

        return to_route('admin.meeting-rooms.index')
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
    public function edit(MeetingRoom $meetingRoom)
    {
        $locations = Location::pluck('name', 'id');

        return view('admin.meeting-room.edit', compact('locations', 'meetingRoom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MeetingRoomUpdateRequest $request, MeetingRoom $meetingRoom, MeetingRoomUpdateAction $action)
    {
        $action->handle($meetingRoom, $request);

        return to_route('admin.meeting-rooms.index')
            ->with('success', 'Meeting Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeetingRoom $meetingRoom, MeetingRoomDestroyAction $action)
    {
        $action->handle($meetingRoom);

        return to_route('admin.meeting-rooms.index')
            ->with('success', 'Meeting Room deleted successfully.');
    }
}
