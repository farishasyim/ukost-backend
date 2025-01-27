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

    protected $hidden = ["deleted_at"];
    protected $appends = ["image_link"];

    public function getImageLinkAttribute()
    {
        if (isset($this->photo)) {
            return env('APP_URL') . '/categories/' . $this->photo;
        }
        return env('APP_URL') . '/default.jpg';
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, "category_id");
    }
}
