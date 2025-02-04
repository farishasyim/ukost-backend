<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::cursorPaginate();

        return $this->paginate(null, $expenses);
    }

    public function store(request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "customer_id" => "required",
                "title" => "required",
                "description" => "required",
                'photos' => 'array|max:5',
                'photos.*' => 'mimes:jpeg,jpg,png',
            ]);

            $data = $request->all(["customer_id", "title", "description"]);

            $data["verified_by"] = auth()->user()->id;

            $photos = [];

            foreach ($request->photos ?? [] as $row) {
                $rename = rand(00000, 99999) . date("YmdHis") . "." . $row->extension();
                $row->move('receipt', $rename);
                $photos[] = $rename;
            }

            if (count($photos) > 0) {
                $data["photos"] = json_encode($photos);
            }

            $expense = Expense::create($data);

            DB::commit();

            return $this->success("Success add data", $expense);
        } catch (\Exception $e) {
            logger($e);
            if (isset($data["photos"])) {
                foreach ($data["photos"] as $name) {
                    File::delete("receipt/$name");
                }
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
