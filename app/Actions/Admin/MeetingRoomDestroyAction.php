<?php

namespace App\Actions\Admin;

use App\Models\MeetingRoom;
use Exception;
use Illuminate\Support\Facades\DB;

class MeetingRoomDestroyAction
{
    public function handle(MeetingRoom $meetingRoom)
    {
        try {
            DB::transaction(function () use ($meetingRoom) {
                $meetingRoom->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
