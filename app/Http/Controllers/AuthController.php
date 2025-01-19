<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required",
        ]);

        $user = User::where('email', $request->email)->first();

        if (!isset($user) || !Hash::check($request->password, $user->password)) {
            return $this->invalid("Wrong email or password", null, 400);
        }

        $token = $user->createToken("personal-access-$user->id")->accessToken;

        return $this->success(null, $user, ["token" => $token]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->success("Request logout has been success");
    }
}
