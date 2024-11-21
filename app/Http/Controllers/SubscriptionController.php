<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Controller;
use App\Models\User;

class SubscriptionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->user()->newSubscription(
            'premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL'
        )->create($request->paymentMethodId);

        return redirect()->route('home')->with('flash_message', '有料プランへの登録が完了しました。');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit() {
        $user = Auth::user();
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.edit', compact('user', 'intent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->user()->updateDefaultPaymentMethod($request->paymentMethodId);
        return redirect()->route('home')->with('flash_message', 'お支払い方法を変更しました。');
    }

    public function cancel()
    {
        return view('subscription.cancel');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->user()->subscription('premium_plan')->cancelNow();
        return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
    }

}
