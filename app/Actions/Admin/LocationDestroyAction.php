<?php

namespace App\Actions\Admin;

use App\Models\Location;
use Exception;
use Illuminate\Support\Facades\DB;

class LocationDestroyAction
{
    public function handle(Location $location)
    {
        try {
            DB::transaction(function () use ($location) {
                $location->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
