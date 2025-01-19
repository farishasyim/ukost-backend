<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expenses";

    protected $fillable = [
        "customer_id",
        "title",
        "description",
        "photos",
        "verified_by",
    ];
}
