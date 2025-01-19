<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PHPUnit\TextUI\XmlConfiguration\Logging\Logging;

class CategoryController extends Controller
{
    public function store(request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                "description" => "required",
                "name" => "required",
                "price" => "required",
                "photo" => "mimes:jpg,jpeg,png",
                "total_rooms" => "required",
            ]);

            $data = $request->only(["name", "price", "description"]);

            if (isset($request->photo)) {
                $rename = date("YmdHis") . rand(0000000, 9999999) . "." . $request->photo->extension();
                $request->photo->move(public_path() . "/categories", $rename);
                $data["photo"] = $rename;
            }

            $category = Category::create($data);

            for ($i = 0; $i < $request->total_rooms; $i++) {
                Room::create([
                    "name" => "Kamar No." . $i + 1,
                    "category_id" => $category->id,
                ]);
            }

            DB::commit();

            return $this->success("Success add category data", $category);
        } catch (\Exception $e) {
            logger($e);
            throw $e;
        }
    }

    public function update($id, request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                "description" => "required",
                "name" => "required",
                "price" => "required",
                "photo" => "mimes:jpg,jpeg,png",
            ]);

            $category = Category::where("id", $id)->first();

            $data = $request->all();

            if (isset($request->photo)) {
                if ($category->photo != null) {
                    File::delete(public_path() . "/categories/" . $category->photo);
                }
                $rename = date("YmdHis") . rand(0000000, 9999999) . "." . $request->photo->extension();
                $request->photo->move(public_path() . "/categories", $rename);
                $data["photo"] = $rename;
            }

            $category->update($data);

            DB::commit();

            return $this->success("Data has been updated", $category);
        } catch (\Exception $e) {
            logger($e);
            throw $e;
        }
    }

    public function delete($id)
    {
        $category = Category::where("id", $id)->first();

        $category->delete();

        return $this->success("Success delete category");
    }
}
