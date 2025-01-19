<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $table = "rooms";

    protected $fillable = [
        "name",
        "category_id",
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }
}
