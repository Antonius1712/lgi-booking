<?php

namespace App\Actions\Admin;

use App\Models\MeetingRoom;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MeetingRoomStoreAction
{
    public function handle($request)
    {
        try {
            DB::transaction(function () use ($request) {
                MeetingRoom::create([
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
