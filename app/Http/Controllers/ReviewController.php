<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * レビュー一覧ページ
     */
    public function index(Restaurant $restaurant)
    {
        if(Auth::user()->subscribed('premium_plan')) {
            $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->paginate(5);
        } else {
            $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->take(3)->get();
        }

        return view('reviews.index', compact('restaurant', 'reviews'));
    }

    /**
     * Show the form for creating a new resource.
     * レビュー投稿ページ
     */
    public function create(Restaurant $restaurant)
    {
        return view('reviews.create', compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     * レビュー投稿機能
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'content' => 'required',
        ]);

        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = Auth::user()->id;
        $review->save();

        return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant->id])->with('flash_message', 'レビューを投稿しました。');

    }

    /**
     * Show the form for editing the specified resource.
     * レビュー編集ページ
     */
    public function edit(Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return view('reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        return view('reviews.edit', compact('restaurant', 'review'));
    }

    /**
     * Update the specified resource in storage.
     * レビュー更新機能
     */
    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'content' => 'required',
        ]);

        if ($review->user_id !== Auth::id()) {
            return view('reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = Auth::user()->id;
        $review->save();

        return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant->id])->with('flash_message', 'レビューを編集しました。');

    }

    /**
     * Remove the specified resource from storage.
     * レビュー削除機能
     */
    public function destroy(Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant->id])->with('error_message', '不正なアクセスです。');
        }

        $review->delete();

        return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant->id])->with('flash_message', 'レビューを削除しました。');

    }
}
