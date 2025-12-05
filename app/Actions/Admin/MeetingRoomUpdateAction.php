<?php

namespace App\Actions\Admin;

use App\Models\MeetingRoom;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MeetingRoomUpdateAction
{
    public function handle(MeetingRoom $meetingRoom, $request)
    {
        try {
            DB::transaction(function () use ($meetingRoom, $request) {
                $meetingRoom->update([
                    'location_id' => $request['location'],
                    'slug' => Str::slug($request['name']),
                    'name' => $request['name'],
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
