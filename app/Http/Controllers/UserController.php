<?php

namespace App\Http\Controllers;

use App\Events\OurExampleEvent;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    private function getSharedData(User $user)
    {
        $isCurrentlyFollowing = 0;

        if (auth()->check()) {
            $isCurrentlyFollowing = Follow::where([
                ['user_id', '=', auth()->user()->id],
                ['followed_user_id', '=', $user->id]
            ])->count();
        }

        View::share('sharedData', [
            'username' => $user->username,
            'isCurrentlyFollowing' => $isCurrentlyFollowing,
            'avatar' => $user->avatar,
            'postCount' => $user->posts()->count(),
            'followerCount' => $user->followers()->count(),
            'followingCount' => $user->followingTheseUsers()->count(),
        ]);
    }

    public function showProfile(User $user)
    {
        $this->getSharedData($user);

        return view('profile-posts', [
            'posts' => $user->posts()->latest()->get(),
        ]);
    }

    public function showProfileRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-posts-only', [
                'posts' => $user->posts()->latest()->get()
            ])->render(),
            'docTitle' => $user->username . "'s Profile"
        ]);
    }

    public function showProfileFollowers(User $user)
    {
        $this->getSharedData($user);

        return view('profile-followers', [
            'followers' => $user->followers()->latest()->get(),
        ]);
    }

    public function showProfileFollowersRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-followers-only', [
                'followers' => $user->followers()->latest()->get()
            ])->render(),
            'docTitle' => $user->username . "'s Followers"
        ]);
    }

    public function showProfileFollowing(User $user)
    {
        $this->getSharedData($user);

        return view('profile-following', [
            'following' => $user->followingTheseUsers()->latest()->get(),
        ]);
    }


    public function showProfileFollowingRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-following-only', [
                'following' => $user->followingTheseUsers()->latest()->get()
            ])->render(),
            'docTitle' => $user->username . " Follows"
        ]);
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image'
        ]);

        $user = auth()->user();

        $filename = $user->id . '-' . uniqid() . '.jpg';

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        $imgData = $image->cover(120, 120)->toJpeg();

        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar !== 'fallback-avatar.jpg') {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Avatar updated successfully');
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed', [
                'posts' => auth()->user()->feedPosts()->latest()->paginate(4)
            ]);
        } else {
            $postCount = Cache::remember('postCount', 60, function () {
                return Post::count();
            });

            return view('homepage', ['postCount' => $postCount]);
        }
    }

    public function loginApi(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (
            auth()->attempt($incomingFields)
        ) {
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('app-token')->plainTextToken;

            return $token;
        }

        return 'bad login';
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);

        auth()->login($user);
        return redirect('/')->with('success', 'Registration successful');
    }
    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (
            auth()->attempt([
                'username' => $incomingFields['loginusername'],
                'password' => $incomingFields['loginpassword']
            ])
        ) {
            $request->session()->regenerate();

            event(new OurExampleEvent(
                [
                    'username' => auth()->user()->username,
                    'action' => 'login'
                ]
            ));

            return redirect('/')->with('success', 'Login successful');
        } else {
            return redirect('/')->with('failure', 'Login failed');
        }
    }

    public function logout()
    {
        event(new OurExampleEvent(
            [
                'username' => auth()->user()->username,
                'action' => 'logout'
            ]
        ));

        auth()->logout();

        return redirect('/')->with('success', 'Logout successful');
    }
}
