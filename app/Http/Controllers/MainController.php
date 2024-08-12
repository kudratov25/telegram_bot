<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $foods = Food::where('is_available', true)->get();
        return view('carts', compact('foods'));
    }
}
