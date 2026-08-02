<?php

namespace App\Http\Controllers;

use DB;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }


    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        DB::transaction(function () use ($googleUser, &$user) {
            $user = User::firstOrCreate(
                [
                    'email' => $googleUser->email
                ],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'role_id' => RoleEnum::User->value,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $workspace = Workspace::create([
                    'name' => "{$user->name}'s Workspace",
                ]);

                $workspace->users()->attach($user->id);
            }
        });

        Auth::login($user);
        request()->session()->regenerate();
        return redirect('/dashboard');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}
