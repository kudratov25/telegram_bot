<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        $model = Food::where('is_available', true)->all();
        return view('carts', $model);
    }
}
