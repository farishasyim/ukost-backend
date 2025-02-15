<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $table = "complains";

    protected $fillable = [
        "customer_id",
        "admin_id",
        "title",
        "description",
        "photos",
        "created_at",
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
                $data[] = env('APP_URL') . '/complains/' . $row;
            }
        }
        return $data;
    }

    public function customer()
    {
        return $this->belongsTo(User::class, "customer_id");
    }

    public function admin()
    {
        return $this->belongsTo(User::class, "admin_id");
    }
}
