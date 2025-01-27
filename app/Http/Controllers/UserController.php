<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(request $request)
    {
        $users = User::with("pivot")->where(function ($query) use ($request) {
            if (isset($request->type)) {
                if ($request->type == "available") {
                    $query->doesntHave("pivot");
                } else if ($request->type == "unavailable") {
                    $query->has("pivot");
                }
            }
            return $query;
        })->where("role", "customer")->cursorPaginate();

        return $this->paginate(null, $users);
    }

    public function store(request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required",
            "identity_card" => "mimes:jpg,jpeg,png",
            "profile_picture" => "mimes:jpg,jpeg,png",
            "gender" => "required",
            "phone" => "required",
            "date_of_birth" => "required"
        ]);

        $data = $request->toArray();

        if (isset($request->identity_card)) {
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->identity_card->extension();
            $request->identity_card->move(public_path() . "/identity_card", $rename);
            $data["identity_card"] = $rename;
        }

        if (isset($request->profile_picture)) {
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->profile_picture->extension();
            $request->profile_picture->move(public_path() . "/profile_picture", $rename);
            $data["profile_picture"] = $rename;
        }

        $data["password"] = Hash::make(Str::random(10));

        $user = User::create($data);

        return $this->success("Success add user data", $user);
    }
    public function update(int $id, request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required",
            "identity_card" => "mimes:jpg,jpeg,png",
            "profile_picture" => "mimes:jpg,jpeg,png",
            "gender" => "required",
            "phone" => "required",
            "date_of_birth" => "required"
        ]);

        $data = $request->toArray();

        $user = User::where("id", $id)->first();

        if (isset($request->identity_card)) {
            if ($user->identity_card != null) {
                File::delete(public_path() . "/identity_card/" . $user->identity_card);
            }
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->identity_card->extension();
            $request->identity_card->move(public_path() . "/identity_card", $rename);
            $data["identity_card"] = $rename;
        }

        if (isset($request->profile_picture)) {
            if ($user->profile_picture != null) {
                File::delete(public_path() . "/profile_picture/" . $user->profile_picture);
            }
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->profile_picture->extension();
            $request->profile_picture->move(public_path() . "/profile_picture", $rename);
            $data["profile_picture"] = $rename;
        }

        $user->update($data);

        $user = User::where("id", $id)->first();

        return $this->success("Success update user data", $user);
    }

    public function delete($id)
    {
        $user = User::where("id", $id)->first();

        if ($user->profile_picture != null) {
            File::delete(public_path() . "/profile_picture/" . $user->profile_picture);
        }

        if ($user->identity_card != null) {
            File::delete(public_path() . "/identity_card/" . $user->identity_card);
        }

        $user->delete();

        return $this->success("Success delete user data");
    }
}
