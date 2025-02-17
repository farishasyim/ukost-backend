<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";

    protected $fillable = [
        "pivot_room_id",
        "admin_id",
        "price",
        "start_period",
        "end_period",
        "status",
        "invoice",
        "date",
        "proof_payment",
    ];

    protected $appends = ["url", "due_date"];

    public function pivotRoom()
    {
        return $this->belongsTo(PivotRoom::class, "pivot_room_id");
    }

    public function getDueDateAttribute()
    {
        return date("Y-m-d", strtotime("+7 day", strtotime($this->start_period)));
    }

    public function getUrlAttribute()
    {
        if ($this->proof_payment != null) {
            return env('APP_URL') . '/proof_payment/' . $this->proof_payment;
        }
    }
}
