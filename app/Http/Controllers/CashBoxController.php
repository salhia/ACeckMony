<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashBoxTransaction;
use Carbon\Carbon;

class CashBoxController extends Controller
{
    // عرض النموذج
    public function showOpeningForm()
    {
        $userId = auth()->id();
        $today = Carbon::today()->toDateString();

        $exists = CashBoxTransaction::where('user_id', $userId)
            ->where('date', $today)
            ->where('type', 'opening')
            ->exists();

        return view('agentuser.cashbox.opening', compact('exists'));
    }

    // حفظ الرصيد الافتتاحي
    public function storeOpening(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $userId = auth()->id();
        $today = Carbon::today()->toDateString();

        $exists = CashBoxTransaction::where('user_id', $userId)
            ->where('date', $today)
            ->where('type', 'opening')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Opening balance already set for today.');
        }

        CashBoxTransaction::create([
            'user_id' => $userId,
            'date' => now()->toDateString(),
            'type' => 'opening',
            'amount' => $request->amount,
            'description' => 'Opening balance for ' . $today,
        ]);

        return back()->with('success', 'Opening balance set successfully.');
    }

    // نموذج تغذية الخزينة
    public function showRefillForm()
    {
        return view('agentuser.cashbox.refill');
    }

    public function storeRefill(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        CashBoxTransaction::create([
            'user_id' => auth()->id(),
            'date' => now()->toDateString(),
            'type' => 'refill',
            'amount' => $request->amount,
            'description' => $request->description,
            'approved' => false,
        ]);

        return back()->with('success', 'Refill transaction added successfully.');
    }

    // نموذج توريد البنك
    public function showBankForm()
    {
        return view('agentuser.cashbox.bank');
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        CashBoxTransaction::create([
            'user_id' => auth()->id(),
            'date' => Carbon::today()->toDateString(),
            'type' => 'bank',
            'amount' => -abs($request->amount), // بالسالب لأنها سحب من الخزينة
            'description' => $request->description ?? 'Bank deposit',
        ]);

        return back()->with('success', 'Bank deposit transaction added successfully.');
    }

    public function dailyReport(Request $request)
    {
        $userId = auth()->id();
        $date = $request->input('date', Carbon::today()->toDateString());

        $transactions = CashBoxTransaction::where('user_id', $userId)
            ->where('date', $date)
            ->orderBy('created_at')
            ->get();

        $opening = $transactions->where('type', 'opening')->sum('amount');
        $deposits = $transactions->where('type', 'deposit')->sum('amount');
        $withdrawals = $transactions->where('type', 'withdraw')->sum('amount');
        $refill = $transactions->where('type', 'refill')->where('approved', true)->sum('amount');
        $bank = $transactions->where('type', 'bank')->sum('amount');
        $commission = $transactions->where('type', 'commission')->sum('amount');
        $deductions = abs($withdrawals); // تحويل السحوبات إلى خصومات

        // الرصيد الختامي
        $closing = $opening + $deposits + $refill + $withdrawals + $bank + $commission;

        $pendingRefills = CashBoxTransaction::where('type', 'refill')->where('approved', false)->get();

        return view('agentuser.cashbox.daily_report', compact(
            'date', 'transactions', 'opening', 'deposits', 'withdrawals', 'refill', 'bank', 'commission', 'closing', 'pendingRefills', 'deductions'
        ));
    }
}
