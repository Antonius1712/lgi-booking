<?php

namespace App\Actions\Admin;

use App\Enums\RoleEnum;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class DriverDestroyAction
{
    public function handle(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                $user->UserGroup()
                    ->where('GroupCode', RoleEnum::DRIVER)
                    ->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
