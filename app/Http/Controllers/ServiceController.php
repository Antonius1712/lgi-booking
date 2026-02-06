<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
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
