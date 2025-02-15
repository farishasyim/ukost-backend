<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ComplainController extends Controller
{
    public function index()
    {
        $complains = Complain::with("customer", "admin")->where(function ($query) {
            if (auth()->user()->role == "customer") {
                return $query->where("customer_id", auth()->user()->id);
            }
        })->cursorPaginate();

        return $this->paginate(null, $complains);
    }

    public function store(request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "title" => "required",
                "description" => "required",
                'photos' => 'array|max:5',
                'photos.*' => 'mimes:jpeg,jpg,png',
            ]);

            $data = $request->all(["title", "description"]);

            $data["customer_id"] = auth()->user()->id;

            $photos = [];

            $data["photos"] = [];

            foreach (($request->photos ?? []) as $row) {
                $rename = rand(00000, 99999) . date("YmdHis") . "." . $row->extension();
                $row->move('complains', $rename);
                $photos[] = $rename;
            };

            if (count($photos) > 0) {
                $data["photos"] = $photos;
            }

            $expense = Complain::create($data);

            DB::commit();

            return $this->success("Success add data", $expense, [], 201);
        } catch (\Exception $e) {
            logger($e);
            foreach ($photos as $name) {
                File::delete("receipt/$name");
            }
            throw $e;
        }
    }

    public function update(int $id, request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "title" => "required",
                "description" => "required",
                'photos' => 'array|max:5',
                'photos.*' => 'mimes:jpeg,jpg,png',
            ]);

            $data = $request->all(["title", "description"]);

            $complain = Complain::where("id", $id)->first();

            $photos = $complain->photos;

            $photos =  array_values(array_diff($photos, $request->deleted ?? []));

            if (isset($request->deleted)) {
                foreach ($request->deleted as $row) {
                    File::delete("complains/$row");
                }
            }

            $data["photos"] = [];

            if (auth()->user()->role == "admin") {
                $data["admin_id"] = auth()->user()->id;
            }

            foreach ($request->photos ?? [] as $row) {
                $rename = rand(00000, 99999) . date("YmdHis") . "." . $row->extension();
                $row->move('complains', $rename);
                $photos[] = $rename;
            };

            if (count($photos) > 0) {
                $data["photos"] = $photos;
            }

            $complain->update($data);

            DB::commit();

            return $this->success("Success update data", $complain, [], 201);
        } catch (\Exception $e) {
            logger($e);
            foreach ($photos ?? [] as $name) {
                File::delete("complains/$name");
            }
            throw $e;
        }
    }

    public function delete(int $id)
    {
        $complain = Complain::where("id", $id)->first();
        foreach ($complain->photos as $row) {
            File::delete("complains/$row");
        }
        $complain->delete();

        return $this->success("Success delete data", null, [], 201);
    }
}
