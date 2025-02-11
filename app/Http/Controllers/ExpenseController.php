<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index(request $request)
    {
        $expenses = Expense::where(function ($builder) use ($request) {
            if (isset($request->date)) {
                return $builder->whereDate("created_at", $request->date);
            }
        })->cursorPaginate();

        return $this->paginate(null, $expenses);
    }

    public function store(request $request)
    {
        $request->validate([
            "customer_id" => "required",
            "title" => "required",
            "description" => "required",
        ]);

        $data = $request->all();

        $data["verified_by"] = auth()->user()->id;

        if (isset($request->photos)) {
            Validator::make($request->photos, [
                "mimes:jpg,jpeg,png"
            ]);
        }
    }
}
