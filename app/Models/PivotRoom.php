<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotRoom extends Model
{
    protected $table = "pivot_rooms";

    protected $fillable = [
        "customer_id",
        "room_id",
        "left_at",
        "created_at",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "customer_id");
    }

    public function room()
    {
        return $this->belongsTo(Room::class, "room_id");
    }
}
