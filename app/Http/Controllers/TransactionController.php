<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::cursorPaginate();

        return $this->paginate(null, $transactions);
    }

    public function store(request $request)
    {
        $rules = [
            "pivot_room_id" => "required",
            "price" => "required",
            "photo" => "mimes:jpg,jpeg,png",
        ];

        if (isset($request->date)) {
            $rules["date"] = "required";
        }

        $request->validate($rules);

        $data = $request->all();

        $data["admin_id"] = $request->user()->id;

        $data["invoice"] = "INV-" . $request->user()->id .  rand(000, 999) . date("YmdHis");

        if (isset($request->date)) {
            $data["status"] = "paid";
            if (isset($request->photo)) {
                $rename = rand(00000, 99999) . "." . $request->photo->extension();
                $request->photo->move("proof_payment", $rename);
                $data["proof_payment"] = $rename;
            }
        }

        $transaction = Transaction::create($data);

        return $this->success("Success store data", $transaction, [], 201);
    }
}
