<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function all_users()
    {
        $allUsers = User::get();
        return helper::responseData(UserResource::collection($allUsers));
    }


    public function search(Request $request)
    {
        $searchQuery = $request->input('searchBox');

        $users = User::where('name', 'like', '%' . $searchQuery . '%')->get();

        return count($users)
            ? helper::responseData(UserResource::collection($users), "Results for '$searchQuery'")
            : helper::responseError("No users found with '$searchQuery'");
    }
}
