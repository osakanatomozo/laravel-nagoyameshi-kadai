<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $highly_rated_restaurants = Restaurant::take(6)->get();
        $categories = Category::all();
        $new_restaurants = Restaurant::orderBy('created_at', 'asc')->take(6)->get();

        return view('home', compact('highly_rated_restaurants', 'categories', 'new_restaurants'));
    }
}
