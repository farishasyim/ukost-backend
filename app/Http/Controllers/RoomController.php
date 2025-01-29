<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PivotRoom;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(request $request)
    {
        $categories = Category::with("rooms.pivot")->cursorPaginate();

        return $this->paginate(null, $categories);
    }

    public function show(int $id)
    {
        $room = Room::with(["category", "pivot.user"])->where("id", $id)->first();

        return $this->success(null, $room);
    }

    public function store(request $request)
    {
        $request->validate([
            "name" => "required",
            "category_id" => "required",
        ]);

        $room = Room::create($request->all());

        return $this->success("Success store data", $room);
    }

    public function update(int $id, request $request)
    {
        $request->validate([
            "name" => "required",
        ]);

        $room = Room::where("id", $id)->first();

        $room->update($request->all());

        return $this->success("Success update data");
    }

    public function delete($id)
    {
        Room::where("id", $id)->delete();
        PivotRoom::where("room_id", $id)->delete();

        return $this->success("Data has been deleted");
    }

    public function storePivot(request $request)
    {
        $request->validate([
            "room_id" => "required",
            "customer_id" => "required",
        ]);

        $pivot = PivotRoom::whereNull("left_at")->where([
            ["room_id", "=", $request->room_id],
            ["customer_id", "=", $request->customer_id],
        ])->orderBy("created_at", "DESC")->first();

        $data = $request->only(["room_id", "customer_id"]);

        if (!isset($pivot)) {
            $data["created_at"] = $request->date;
            $pivot = PivotRoom::create($data);
        } else {
            $data = [];
            if (isset($request->left_at)) {
                $data["left_at"] = $request->left_at;
            }

            $pivot->update($data);
        }

        return $this->success(isset($pivot) ? "Update data has been successed" : "Store data has been successed");
    }
}
