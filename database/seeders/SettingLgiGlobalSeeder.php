<?php

namespace Database\Seeders;

use App\Models\LgiGlobalApp;
use App\Models\LgiGlobalGroup;
use App\Models\LgiGlobalUserGroup;
use App\Models\User;
use App\Services\LgiPassword;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingLgiGlobalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::transaction(function () {
                // ? Setting App.
                $app = LgiGlobalApp::where('AppCode', 'lgi-booking')->first();
                if (! $app) {
                    LgiGlobalApp::create([
                        'AppCode' => 'lgi-booking',
                        'AppName' => 'LGI Booking',
                        'Status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                // ? Setting Group.
                $groupAdmin = LgiGlobalGroup::where('GroupCode', 'admin-lgi-booking')->first();
                if (! $groupAdmin) {
                    LgiGlobalGroup::create([
                        'GroupCode' => 'admin-lgi-booking',
                        'GroupName' => 'Admin LGI Booking',
                        'AppCode' => 'lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $groupDriver = LgiGlobalGroup::where('GroupCode', 'driver-lgi-booking')->first();
                if (! $groupDriver) {
                    LgiGlobalGroup::create([
                        'GroupCode' => 'driver-lgi-booking',
                        'GroupName' => 'Driver LGI Booking',
                        'AppCode' => 'lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $groupUser = LgiGlobalGroup::where('GroupCode', 'user-lgi-booking')->first();
                if (! $groupUser) {
                    LgiGlobalGroup::create([
                        'GroupCode' => 'user-lgi-booking',
                        'GroupName' => 'User LGI Booking',
                        'AppCode' => 'lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                // ? Insert User for Driver.
                // ! Ahmad Gustian Amir - 2023099790
                $userAhmad = User::where('UserId', '2023099790')->first();
                if (! $userAhmad) {
                    User::create([
                        'UserId' => '2023099790',
                        'Password' => LgiPassword::Encrypt('login123'),
                        'NIK' => '2023099790',
                        'Name' => 'Ahmad Gustian Amir',
                        'Email' => '',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userDuriat = User::where('UserId', '2012129922')->first();
                if (! $userDuriat) {
                    // ! Duriat - 2012129922
                    User::create([
                        'UserId' => '2012129922',
                        'Password' => LgiPassword::Encrypt('login123'),
                        'NIK' => '2012129922',
                        'Name' => 'Duriat',
                        'Email' => '',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userFebrianto = User::where('UserId', '2024059780')->first();
                if (! $userFebrianto) {
                    // ! Febrianto - 2024059780
                    User::create([
                        'UserId' => '2024059780',
                        'Password' => LgiPassword::Encrypt('login123'),
                        'NIK' => '2024059780',
                        'Name' => 'Febrianto',
                        'Email' => '',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userMulyana = User::where('UserId', '2012039921')->first();
                if (! $userMulyana) {
                    // ! Mulyana - 2012039921
                    User::create([
                        'UserId' => '2012039921',
                        'Password' => LgiPassword::Encrypt('login123'),
                        'NIK' => '2012039921',
                        'Name' => 'Mulyana',
                        'Email' => '',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userMRatmiyadi = User::where('UserId', '2013029906')->first();
                if (! $userMRatmiyadi) {
                    // ! Ratmiyadi - 2013029906
                    User::create([
                        'UserId' => '2013029906',
                        'Password' => LgiPassword::Encrypt('login123'),
                        'NIK' => '2013029906',
                        'Name' => 'Ratmiyadi',
                        'Email' => '',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                // ? Setting UserGroup.
                // ? Admin
                $userGroupAntonAdmin = LgiGlobalUserGroup::where('UserId', '2018113907')->where('GroupCode', 'admin-lgi-booking')->first();
                if (! $userGroupAntonAdmin) {
                    // ! Anton - Admin
                    LgiGlobalUserGroup::create([
                        'UserId' => '2018113907',
                        'GroupCode' => 'admin-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userGroupZenggaAdmin = LgiGlobalUserGroup::where('UserId', '2014123213')->where('GroupCode', 'admin-lgi-booking')->first();
                if (! $userGroupZenggaAdmin) {
                    // ! Zengga - Admin
                    LgiGlobalUserGroup::create([
                        'UserId' => '2014123213',
                        'GroupCode' => 'admin-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                // ? Driver
                $userGroupAhmadDriver = LgiGlobalUserGroup::where('UserId', '2023099790')->where('GroupCode', 'driver-lgi-booking')->first();
                if (! $userGroupAhmadDriver) {
                    // ! Ahmad Gustian Amir - Driver
                    LgiGlobalUserGroup::create([
                        'UserId' => '2023099790',
                        'GroupCode' => 'driver-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userGroupDuriatDriver = LgiGlobalUserGroup::where('UserId', '2012129922')->where('GroupCode', 'driver-lgi-booking')->first();
                if (! $userGroupDuriatDriver) {
                    // ! Duriat - Driver
                    LgiGlobalUserGroup::create([
                        'UserId' => '2012129922',
                        'GroupCode' => 'driver-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userGroupFebriantoDriver = LgiGlobalUserGroup::where('UserId', '2024059780')->where('GroupCode', 'driver-lgi-booking')->first();
                if (! $userGroupFebriantoDriver) {
                    // ! Febrianto - Driver
                    LgiGlobalUserGroup::create([
                        'UserId' => '2024059780',
                        'GroupCode' => 'driver-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userGroupMulyanaDriver = LgiGlobalUserGroup::where('UserId', '2012039921')->where('GroupCode', 'driver-lgi-booking')->first();
                if (! $userGroupMulyanaDriver) {
                    // ! Mulyana - Driver
                    LgiGlobalUserGroup::create([
                        'UserId' => '2012039921',
                        'GroupCode' => 'driver-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                $userGroupRatmiyadiDriver = LgiGlobalUserGroup::where('UserId', '2013029906')->where('GroupCode', 'driver-lgi-booking')->first();
                if (! $userGroupRatmiyadiDriver) {
                    // ! Ratmiyadi - Driver
                    LgiGlobalUserGroup::create([
                        'UserId' => '2013029906',
                        'GroupCode' => 'driver-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                // ? User
                $userGroupFarouqUser = LgiGlobalUserGroup::where('UserId', '2018113915')->where('GroupCode', 'user-lgi-booking')->first();
                if (! $userGroupFarouqUser) {
                    // ! Farouq - User
                    LgiGlobalUserGroup::create([
                        'UserId' => '2018113915',
                        'GroupCode' => 'user-lgi-booking',
                        'status' => 1,
                        'UserCreated' => 'system',
                        'DateCreated' => now(),
                        'UserModified' => null,
                        'DateModified' => null,
                    ]);
                }

                // ?
            });
        } catch (Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }
}
