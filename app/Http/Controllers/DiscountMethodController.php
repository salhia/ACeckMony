<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;

class DiscountMethodController extends Controller
{
    public function edit()
    {
        $agent = auth()->user()->agent;
        $availableMethods = config('discounts.methods'); // من ملف الإعدادات

        return view('agent.discount.methods', compact('agent', 'availableMethods'));
    }

    public function update(Request $request)
    {
        $agent = auth()->user()->agent;

        $request->validate([
            'methods' => 'nullable|array',
            'methods.*' => 'in:' . implode(',', array_keys(config('discounts.methods'))),
            'max_discount' => 'required|numeric|min:1|max:50' // الحد الأقصى 50%
        ]);

        $agent->update([
            'discount_methods' => $request->methods,
            'max_discount' => $request->max_discount
        ]);

        return back()->with('success', 'تم تحديث طرق الخصم بنجاح');
    }
}
