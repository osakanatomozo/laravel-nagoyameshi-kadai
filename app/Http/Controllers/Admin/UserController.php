<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 一覧ページ
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        if ($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")
                        ->orWhere('kana', 'like', "%{$keyword}%")
                        ->paginate(15);
            $total = $users->total();
        } else {
            $users = User::paginate(15);
            $total = 0;
            $keyword = null;
        }

        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }

    // /**
    //  * Show the form for creating a new resource.
    //  作成ページ*/
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  作成機能*/
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     * 詳細ページ
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  更新ページ*/
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  更新機能*/
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

//     /**
//      * Remove the specified resource from storage.
//      削除機能*/
//     public function destroy(string $id)
//     {
//         //
//     }
}
