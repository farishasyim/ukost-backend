<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(request $request)
    {
        $categories = Category::with("rooms")->cursorPaginate();

        return $this->paginate(null, $categories);
    }
}
