<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = "incomes";

    protected $fillable = [
        "customer_id",
        "user_id",
        "room_id",
        "quantity",
        "total",
    ];
}
