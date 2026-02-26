<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function searchUser(Request $request)
    {
        $keyword = $request->keywords;

        $users = User::query()
            ->when(! empty($keyword), function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereLike('Name', "%$keyword%")
                        ->orWhereLike('NIK', "%$keyword%")
                        ->orWhereLike('Email', "%$keyword%");
                });
            })
            ->limit(10)
            ->get(['Email', 'Name']);

        return response()->json($users);
    }

    public function searchUserByNik(Request $request)
    {
        $keyword = $request->keywords;

        $excludedGroupCodes = [
            'driver-lgi-booking',
            'admin-lgi-booking',
            'user-lgi-booking',
        ];

        $users = User::query()
            ->whereDoesntHave('UserGroup', function ($q) use ($excludedGroupCodes) {
                $q->whereIn('GroupCode', $excludedGroupCodes);
            })
            ->when(! empty($keyword), function ($query) use ($keyword) {
                $query->whereLike('Nik', "%$keyword%");
            })
            ->get();

        return response()->json($users);
    }
}
