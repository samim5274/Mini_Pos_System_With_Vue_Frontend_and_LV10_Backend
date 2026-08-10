<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function users(){
        $users = User::where('is_active', 1)->get();
        return response()->json([
            'success' => true,
            'message' => "Get all active users.",
            'data' => $users
        ]);
    }
}
