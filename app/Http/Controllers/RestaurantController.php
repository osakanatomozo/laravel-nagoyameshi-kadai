<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     * 店舗一覧ページ
     */
    public function index(Request $request)
    {
        // 検索条件を取得
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $price = $request->input('price');
        $sort = $request->input('sort', 'created_at desc');

        // 並べ替え条件を設定
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
        ];

        // デフォルト並び順を設定
        $sort_query = [];
        $sorted = "created_at desc";

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        $query = Restaurant::query();

        // キーワード検索
        if ($keyword) {
            $restaurants = Restaurant::where('name', 'like', "%$keyword%")
                    ->orWhere('address', 'like', "%$keyword%")
                    ->orWhereHas('categories', function ($query) use ($keyword) {
                        $query->where('categories.name', 'like', "%$keyword%");
                    })
                    ->sortable($sort_query)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        } elseif ($category_id) {
            $restaurants = Restaurant::whereHas('categories', function ($query) use ($category_id) {
                $query->where('categories.id', $category_id);
            })->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        } elseif ($price) {
            $restaurants = Restaurant::where('lowest_price', '<=', $price)->sortable($sort_query)->orderBy('lowest_price', 'asc')->paginate(15);
        } else {
            $restaurants = Restaurant::sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        }

        // カテゴリ検索
        if ($category_id) {
            $query->whereHas('categories', function ($subQuery) use ($category_id) {
                $subQuery->where('id', $category_id);
            });
        }

        // 総件数を取得
        $total = $restaurants->total();

        // カテゴリを取得
        $categories = Category::all();

        // ビューに渡すデータ
        return view('restaurants.index', compact(
            'keyword', 'category_id', 'price', 'sorts', 'sorted',
            'restaurants', 'categories', 'total'
        ));
    }

    public function show(Restaurant $restaurant) {
        return view('restaurants.show', compact('restaurant'));
    }
}
