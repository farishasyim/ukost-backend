<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TransactionController extends Controller
{
    public function index(request $request)
    {
        $transactions = Transaction::with(["pivotRoom" => [
            "user",
            "room.category",
        ]])->whereHas("pivotRoom", function ($query) {
            if (auth()->user()->role == "customer") {
                return $query->where("customer_id", auth()->user()->id);
            }
        })->where(function ($query) use ($request) {
            if (isset($request->status)) {
                return $query->where("status", $request->status);
            }
        })->cursorPaginate();

        return $this->paginate(null, $transactions);
    }

    public function report(request $request)
    {
        $transactions = Transaction::with(["pivotRoom" => ["user", "room.category"]])->whereHas("pivotRoom", fn($query) => $query->where("customer_id", $request->customer_id))->where(function ($query) use ($request) {
            if (isset($request->status)) {
                $query->whereIn("status", $request->status);
            }
            return $query;
        })->orderBy("start_period", "DESC")->get();

        return $this->success(null, $transactions);
    }

    public function sentInvoice(int $id)
    {
        $transaction = Transaction::where("id", $id)->first();

        $date = date("M Y", strtotime($transaction->start_period));

        $price = number_format($transaction->price);

        return $this->sentMessage($transaction->pivotRoom->user->phone, "(*$transaction->invoice*)\nAnda memiliki tagihan $date sebesar *Rp.$price*");
    }

    public function recentTransaction()
    {
        $res = [];
        for ($i = -6; $i <= 0; $i++) {
            $price = Transaction::where("status", "paid")->whereDate("date", date("Y-m-d", strtotime("-$i day")))->sum("price");
            $x = date("d/m/y", strtotime("-$i day"));
            $res[] = [
                "x_axis" => $x,
                "y_axis" => $price,
            ];
        }

        return $this->success(null, $res);
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

        $data["start_period"] = $request->date;

        $data["end_period"] = date("Y-m-d", strtotime("+1 month", strtotime($request->date)));

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
            "date" => "required",
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
