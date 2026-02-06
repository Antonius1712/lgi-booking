<?php

namespace App\Actions\Admin;

use App\Enums\RoleEnum;
use App\Models\LgiGlobalUserGroup;
use Exception;
use Illuminate\Support\Facades\DB;

class DriverStoreAction
{
    public function handle(string $nik)
    {
        try {
            DB::transaction(function () use ($nik) {
                $validateUserGroup = LgiGlobalUserGroup::where('UserId', $nik)->where('GroupCode', RoleEnum::DRIVER)->exists();
                if (! $validateUserGroup) {
                    LgiGlobalUserGroup::create([
                        'UserId' => $nik,
                        'GroupCode' => RoleEnum::DRIVER,
                        'Status' => 1,
                        'UserCreated' => auth()->user()->NIK,
                        'DateCreated' => now(),
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
