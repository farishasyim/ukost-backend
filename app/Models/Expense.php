<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expenses";

    protected $fillable = [
        "user_id",
        "title",
        "description",
        "photos",
        "price",
        "created_at",
    ];

    protected $appends = ["urls"];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'price' => 'integer',
        ];
    }

    public function getUrlsAttribute()
    {
        $data = [];
        if (isset($this->photos)) {
            foreach ($this->photos as $row) {
                $data[] = env('APP_URL') . '/receipt/' . $row;
            }
        }
        return $data;
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
