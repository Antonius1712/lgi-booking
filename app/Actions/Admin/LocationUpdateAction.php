<?php

namespace App\Actions\Admin;

use App\Models\Location;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationUpdateAction
{
    public function handle(Location $location, $request)
    {
        try {
            DB::transaction(function () use ($location, $request) {
                $location->update([
                    'slug' => Str::slug($request['name']),
                    'name' => $request['name'],
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
