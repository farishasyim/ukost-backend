<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(request $request)
    {
        $categories = Category::with("rooms")->cursorPaginate();

        return $this->paginate(null, $categories);
    }

    public function store(request $request)
    {
        $request->validate([
            "category_id" => "required",
        ]);
        $rooms = Room::where("category_id", $request->category_id)->count();
        $validate = true;
        $count = 0;

        for ($i = 0; $i < $rooms; $i++) {
            $name = "Kamar No." . $i + 1;
            $room = Room::where("name", $name)->first();
            if (!isset($room)) {
                $count += 1;
                Room::create([
                    "category_id" => $request->category_id,
                    "name" => $name,
                ]);
                $validate = false;
            }
        }

        if ($validate) {
            $room = Room::create([
                "category_id" => $request->category_id,
                "name" => "Kamar No." . $rooms + 1,
            ]);
        }

        return $this->success($validate ? "Success store data" : "Success store $count rooms");
    }

    public function delete($id)
    {
        Room::where("id", $id)->delete();

        return $this->success("Data has been deleted");
    }
}
