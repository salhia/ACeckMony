<?php

namespace App\Http\Controllers;

use App\Models\SysAgentCommission;
use App\Models\SysAgentEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    public function index()
    {
        $agent_id = Auth::id();
        $commission = SysAgentCommission::where('agent_id', $agent_id)->first();
        $earnings = SysAgentEarning::where('agent_id', $agent_id)
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('agentuser.commission.index', compact('commission', 'earnings'));
    }

    public function calculator()
    {
        return view('agentuser.commission.calculator');
    }

    public function calculateCommission(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $commission_rate = $user->commission_rate ?? 0;

        $commission = ($amount * $commission_rate) / 100;
        $netAmount = $amount - $commission;

        return response()->json([
            'amount' => number_format($amount, 2),
            'commission_rate' => $commission_rate,
            'commission' => number_format($commission, 2),
            'netAmount' => number_format($netAmount, 2)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'fixed_commission' => 'required|numeric|min:0',
            'admin_fee_fixed' => 'required|numeric|min:0',
            'min_amount' => 'required|numeric|min:0'
        ]);

        SysAgentCommission::updateOrCreate(
            ['agent_id' => Auth::id()],
            [
                'commission_rate' => $request->commission_rate,
                'fixed_commission' => $request->fixed_commission,
                'admin_fee_fixed' => $request->admin_fee_fixed,
                'min_amount' => $request->min_amount,
                'is_active' => true
            ]
        );

        return redirect()->back()->with('success', 'تم تحديث العمولة بنجاح');
    }
}
