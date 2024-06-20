<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user)
    {
        // You cannot follow yourself
        if ($user->id === auth()->id()) {
            return back()->with('failure', 'You cannot follow yourself');
        }

        // You cannot follow someone you are already following
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followed_user_id', '=', $user->id]])->count();

        if ($existCheck) {
            return back()->with('failure', 'You are already following this user');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->id();
        $newFollow->followed_user_id = $user->id;
        $newFollow->save();

        return back()->with('success', 'You are now following ' . $user->username);
    }

    public function removeFollow(User $user)
    {
        Follow::where([['user_id', '=', auth()->user()->id], ['followed_user_id', '=', $user->id]])->delete();

        return back()->with('success', 'You have unfollowed ' . $user->username);
    }
}
