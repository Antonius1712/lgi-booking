<?php

namespace App\Actions\MeetingRoom;

use App\Http\Requests\StoreMeetingRoomRequest;
use App\Models\MeetingRoom;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreAction
{
    public function handle(StoreMeetingRoomRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $location_id = $request->location;
                $name = $request->name;

                MeetingRoom::create([
                    'location_id' => $location_id,
                    'slug' => str($name)->slug('-'),
                    'name' => $name,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
