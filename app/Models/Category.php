<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = "categories";

    protected $fillable = [
        "name",
        "price",
        "description",
        "photo",
        "deleted_at",
    ];

    protected function casts()
    {
        return [
            "price" => "integer",
        ];
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, "category_id");
    }
}
