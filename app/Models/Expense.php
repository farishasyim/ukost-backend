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

    protected $appends = ["urls"];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
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
}
