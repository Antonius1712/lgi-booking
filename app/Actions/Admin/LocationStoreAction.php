<?php

namespace App\Actions\Admin;

use App\Models\Location;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationStoreAction
{
    public function handle($request)
    {
        try {
            DB::transaction(function () use ($request) {
                Location::firstOrCreate([
                    'slug' => Str::slug($request['name']),
                    'name' => $request['name'],
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
