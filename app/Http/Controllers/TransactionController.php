<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(["pivotRoom" => [
            "user",
            "room.category",
        ]])->cursorPaginate();

        return $this->paginate(null, $transactions);
    }

    public function recentTransaction()
    {
        $date = [];
    }

    public function store(request $request)
    {
        $rules = [
            "pivot_room_id" => "required",
            "price" => "required",
            "photo" => "mimes:jpg,jpeg,png",
            "status" => "required",
            "date" => "required",
        ];

        $request->validate($rules);

        $data = $request->all(["pivot_room_id", "price", "photo", "status"]);

        $data["admin_id"] = $request->user()->id;

        $data["invoice"] = "INV-" . $request->user()->id .  rand(000, 999) . date("YmdHis");

        $data["created_at"] = $request->date;

        if ($data["status"] == "paid") {
            $data["date"] = $request->date;
            if (isset($request->photo)) {
                $rename = rand(00000, 99999) . "." . $request->photo->extension();
                $request->photo->move("proof_payment", $rename);
                $data["proof_payment"] = $rename;
            }
        }

        $transaction = Transaction::create($data);

        return $this->success("Success store data", $transaction, [], 201);
    }

    public function update(int $id, request $request)
    {
        $rules = [
            "photo" => "mimes:jpg,jpeg,png",
            "status" => "required",
        ];

        $request->validate($rules);

        $transaction = Transaction::where("id", $id)->first();

        $data = $request->all();

        if ($data["status"] == "paid") {
            $data["date"] = $request->date;
            if (isset($request->photo)) {
                $rename = rand(00000, 99999) . "." . $request->photo->extension();
                $request->photo->move("proof_payment", $rename);
                $data["proof_payment"] = $rename;
            }
        }

        $transaction->update($data);

        return $this->success("Success update data", null, [], 201);
    }

    public function delete(int $id)
    {
        $transaction = Transaction::where("id", $id)->first();
        File::delete("proof_payment/$transaction->proof_payment");
        $transaction->delete();

        return $this->success("Success delete data", null, [], 201);
    }
}
