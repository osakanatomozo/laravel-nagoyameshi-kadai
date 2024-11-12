<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * 一覧ページ
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        if ($keyword !== null) {
            $categories = Category::where('name', 'like', "%{$keyword}%")
                        ->paginate(15);
            $total = $categories->total();
        } else {
            $categories = Category::paginate(15);
            $total = 0;
            $keyword = null;
        }

        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }

    /**
     * Store a newly created resource in storage.
     * 登録機能
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $category = new Category();
        $category->name = $validatedData['name'];

        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }

    /**
     * Update the specified resource in storage.
     * 更新機能
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $category->name = $validatedData['name'];

        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     * 削除機能
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }
}
