<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transations";

    protected $fillable = [
        "pivot_room_id",
        "admin_id",
        "price",
        "status",
        "invoice",
        "date",
        "proof_payment",
    ];
}
