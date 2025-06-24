<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminService
{
    public function makeAdmin($credentials)
    {
        $user = User::find($credentials['user_id']);
        $user->admin_lvl = $credentials['admin_lvl'];
        $user->save();

        Log::alert("{${auth()->user()->name}} ({${\auth()->id()}}) make new admin {$user->name}} ({$user->id}) lvl - {$user->admin_lvl}  ");

        return response()->json([
            'user_id' => $credentials['user_id'],
            'admin_lvl'=> $credentials['admin_lvl'],
        ]);
    }
    public function banned($credentials)
    {
        $user = User::find($credentials['user_id']);
        if(Auth::user()->admin_lvl <= $user->admin_lvl){
            return response()->json([
                'message' => 'You cannot ban a user with a higher admin level than yours.'
            ], 403);
        }
        $user->isBanned = true;
        $user->save();

        Log::alert("{${auth()->user()->name}} ({${\auth()->id()}}) banned user {$user->name}} ({$user->id})  ");

        return response()->json([
            'user_id' => $credentials['user_id'],
            'isBanned'=> true,
        ]);
    }
    public function unbanned($credentials)
    {
        $user = User::find($credentials['user_id']);
        $user->isBanned = false;
        $user->save();

        Log::alert("{${auth()->user()->name}} ({${\auth()->id()}}) unbanned user {$user->name}} ({$user->id})  ");

        return response()->json([
            'user_id' => $credentials['user_id'],
            'isBanned'=> false,
        ]);
    }

}
