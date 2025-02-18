<?php

namespace App\Http\Controllers;

use App\Helper\WhatsappHelper as HelperWhatsappHelper;
use App\Helper\WhatsappHelper\WhatsappHelper;
use App\Models\PivotRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(request $request)
    {
        $users = User::with("pivot.room.category")->where(function ($query) use ($request) {
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
            "identity_card" => "mimes:jpg,jpeg,png",
            "profile_picture" => "mimes:jpg,jpeg,png",
            "gender" => "required",
            "phone" => "required",
            "date_of_birth" => "required"
        ]);

        $data = $request->toArray();

        if (isset($request->identity_card)) {
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->identity_card->extension();
            $request->identity_card->move("identity_card", $rename);
            $data["identity_card"] = $rename;
        }

        if (isset($request->profile_picture)) {
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->profile_picture->extension();
            $request->profile_picture->move("profile_picture", $rename);
            $data["profile_picture"] = $rename;
        }

        $user = User::create($data);

        $this->sentCredential($user->id);

        return $this->success("Success add user data", $user, [], 201);
    }

    public function update(int $id, request $request)
    {
        $request->validate([
            "name" => "required",
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
                File::delete("identity_card/" . $user->identity_card);
            }
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->identity_card->extension();
            $request->identity_card->move("identity_card", $rename);
            $data["identity_card"] = $rename;
        }

        if (isset($request->profile_picture)) {
            if ($user->profile_picture != null) {
                File::delete("profile_picture/" . $user->profile_picture);
            }
            $rename = rand(00000, 99999) . date("YmdHis") . "." . $request->profile_picture->extension();
            $request->profile_picture->move("profile_picture", $rename);
            $data["profile_picture"] = $rename;
        }

        $user->update($data);

        $user = User::where("id", $id)->first();

        return $this->success("Success update user data", $user, [], 201);
    }

    public function delete($id)
    {
        $user = User::where("id", $id)->first();

        if ($user->profile_picture != null) {
            File::delete("profile_picture/" . $user->profile_picture);
        }

        if ($user->identity_card != null) {
            File::delete("identity_card/" . $user->identity_card);
        }

        PivotRoom::where("customer_id", $id)->delete();

        $user->delete();

        return $this->success("Success delete user data", null, [], 201);
    }

    public function sentCredential(int $id)
    {
        $user = User::where("id", $id)->first();

        if ($user->is_default) {
            $this->sentMessage($user->phone, "Hai, akun anda telah berhasil dibuat segera lakukan aktivasi akun dengan cara login ke aplikasi menggunakan nomer telpon anda dan masukan password *11223344*, terima kasih!");
        }

        return $this->success("Success sent message");
    }
}
