<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('user')->cursorPaginate();

        return $this->paginate(null, $expenses);
    }

    public function report(request $request)
    {
        $expenses = Expense::with('user')->whereBetween("created_at", [$request->start, $request->end])->orderBy("created_at", "ASC")->get();

        return $this->success(null, $expenses);
    }

    public function store(request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "title" => "required",
                "description" => "required",
                "date" => "required",
                "price" => "required",
                'photos' => 'array|max:5',
                'photos.*' => 'mimes:jpeg,jpg,png',
            ]);

            $data = $request->all(["title", "description", "price"]);

            $data["user_id"] = auth()->user()->id;

            $photos = [];

            $data["photos"] = [];

            $data["created_at"] = $request->date;

            foreach (($request->photos ?? []) as $row) {
                $rename = rand(00000, 99999) . date("YmdHis") . "." . $row->extension();
                $row->move('receipt', $rename);
                $photos[] = $rename;
            };

            if (count($photos) > 0) {
                $data["photos"] = $photos;
            }

            $expense = Expense::create($data);

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
                "date" => "required",
                "price" => "required",
                'photos' => 'array|max:5',
                'photos.*' => 'mimes:jpeg,jpg,png',
            ]);

            $data = $request->all(["title", "description", "date", "price"]);

            $expense = Expense::where("id", $id)->first();

            $photos = $expense->photos;

            $photos =  array_values(array_diff($photos, $request->deleted ?? []));

            if (isset($request->deleted)) {
                foreach ($request->deleted as $row) {
                    File::delete("receipt/$row");
                }
            }

            $data["photos"] = [];

            $data["created_at"] = $request->date;

            foreach ($request->photos ?? [] as $row) {
                $rename = rand(00000, 99999) . date("YmdHis") . "." . $row->extension();
                $row->move('receipt', $rename);
                $photos[] = $rename;
            };

            if (count($photos) > 0) {
                $data["photos"] = $photos;
            }

            $expense->update($data);

            DB::commit();

            return $this->success("Success update data", $expense, [], 201);
        } catch (\Exception $e) {
            logger($e);
            foreach ($photos ?? [] as $name) {
                File::delete("receipt/$name");
            }
            throw $e;
        }
    }

    public function delete($id)
    {
        $expense = Expense::where("id", $id)->first();
        foreach ($expense->photos as $row) {
            File::delete("receipt/$row");
        }
        $expense->delete();

        return $this->success("Success delete data", null, [], 201);
    }
}
