<?php

namespace App\Http\Controllers;

use App\Models\SysCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\SysTransaction;
use App\Models\User;
use App\Models\CashBoxTransaction;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\State; // Ensure the correct namespace for the State model
use App\Models\SysRegion;

class TransferController extends Controller
{
    public function create()
    {
        $states = State::get();

        // Get the current user
        $user = auth()->user();

        // Step 1: Get the agent_id from the current user
        $agent_id = $user->parent_agent_id;

        // Step 2: Get all users where parent_agent_id matches the agent_id
        $subordinate_users = User::where('parent_agent_id', $agent_id)->get();

        // Step 3: Get all region_ids from these users
        $region_ids = $subordinate_users->pluck('region_id')->unique()->filter();

        // Step 4: Get all regions from sys_regions based on these region_ids
        $sys_regions = SysRegion::whereIn('id', $region_ids)->get();

        return view('agentuser.transfers.create', compact('states', 'sys_regions'));
    }

    public function searchCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'بحث غير صحيح'], 400);
        }

        $customer = SysCustomer::where(function($query) use ($request) {
                        $query->where('phone', $request->search)
                              ->orWhere('identity_number', $request->search);
                    })
                    ->first();

        if ($customer) {
            return response()->json([
                'found' => true,
                'customer' => $customer
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:sys_customers,phone',
            'identity_number' => 'nullable|string|max:50|unique:sys_customers,identity_number'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = SysCustomer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'identity_number' => $request->identity_number,
                'identity_type' => 'id_card',
                'registered_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'message' => 'تم إضافة الزبون بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إضافة الزبون: ' . $e->getMessage()
            ], 500);
        }
    }

// app/Http/Controllers/TransferController.php
public function applyDiscount(Request $request)
{
    $agent = auth()->user()->agent;
    $method = $request->input('discount_method');

    // التحقق من أن طريقة الخصم مسموحة للوكيل
    if (!in_array($method, $agent->discount_methods ?? [])) {
        abort(403, 'طريقة الخصم غير مسموحة لك');
    }

    // حساب الخصم حسب النوع
    $discount = $this->calculateDiscount(
        $request->amount,
        $method,
        $request->discount_value,
        $agent->max_discount
    );

    // ... باقي العملية ...
}


   public function store(Request $request)
{
    Log::info('TransferController@store: started', ['request' => $request->all()]);

    $validated = $request->validate([
        'sender_name' => 'required|string|max:100',
        'sender_phone' => 'nullable|string|max:20',
       // 'sender_identity_number' => 'nullable|string|max:50',
        'receiver_name' => 'required|string|max:100',
        'receiver_phone' => 'nullable|string|max:20',
      //  'receiver_identity_number' => 'nullable|string|max:50',
        'amount' => 'required|numeric|min:0',
        'commission' => 'required|numeric|min:0',
        'region_id' => 'required|exists:sys_regions,id',
        'notes' => 'nullable|string'
    ]);

    Log::info('TransferController@store: after validation', ['validated' => $validated]);

    $user = auth()->user();
    Log::info('TransferController@store: user found', ['user' => $user]);

    try {
        // Create or find sender
        $senderPhone = $request->sender_phone ?: null;
        $sender = SysCustomer::firstOrCreate(
            [
                'phone' => $senderPhone,
                'name'  => $validated['sender_name'],
            ],
            [
                'identity_type' => 'id_card',
                'registered_by' => auth()->id()
            ]
        );
        // Create or find receiver
        $receiverphone = $request->receiver_phone ?: null;
        $receiver = SysCustomer::firstOrCreate(
            [
                'phone' => $receiverphone,
                'name'  => $validated['receiver_name'],
            ],
            [
                'identity_type' => 'id_card',
                'registered_by' => auth()->id()
            ]
        );

        if ($sender->id == $receiver->id) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'لا يمكن أن يكون المرسل والمستلم نفس الشخص']);
        }

        $amount = $validated['amount'];
        $commission = $validated['commission'];
        $netAmount = $amount;
        $finalDeliveredAmount = $amount;

        $transactionCode = 'TRX-' . strtoupper(uniqid());

        // 1. حساب عمولة المستخدم (commission)
        $user_commission_rate = $user->commission_rate ?? 0;
        $commission = ($amount * $user_commission_rate) / 100;

        // 2. حساب عمولة المكتب الرئيسي (admin_fee)
        $parentAgent = User::find($user->parent_agent_id);
        $admin_commission_rate = $parentAgent->commission_rate ?? 0;
        $adminFee = ($amount * $admin_commission_rate) / 100;

        // 3. إنشاء العملية مع القيم المحسوبة
        $transaction = SysTransaction::create([
            'transaction_code' => $transactionCode,
            'sender_customer_id' => $sender->id,
            'receiver_customer_id' => $receiver->id,
            'sender_user_id' => $user->id,
            'sender_agent_id' => $user->parent_agent_id,
            'sender_region_id' => $user->region_id,
            'region_id' => $validated['region_id'],
            'receiver_agent_id' => null,
            'amount' => $amount,
            'commission' => $commission,      // عمولة الموظف/الفرع
            'admin_fee' => $adminFee,         // عمولة المكتب الرئيسي
            'agent_id' => $user->parent_agent_id,
            'net_amount' => $netAmount,
            'final_delivered_amount' => $finalDeliveredAmount,
            'transaction_type_id' => 1,
            'status' => 'delivered',
            'notes' => $request->input('notes'),
            'created_by' => $user->id
        ]);

        // تسجيل الحركة في الخزينة
        CashBoxTransaction::create([
            'user_id'     => auth()->id(),
            'date'        => now()->toDateString(),
            'type'        => 'deposit',
            'amount'      => $transaction->amount,
            'description' => 'Received from customer, Transaction ID: ' . $transaction->id,
        ]);

        // حساب العمولة
        $commissionRate = auth()->user()->commission_rate ?? 0; // أو حسب النظام عندك
        $commission = $transaction->amount * ($commissionRate / 100);

        // تسجيل حركة العمولة (تخصم من رصيد المستخدم)
        if ($commission > 0) {
            CashBoxTransaction::create([
                'user_id'     => auth()->id(),
                'date'        => now()->toDateString(),
                'type'        => 'commission',
                'amount'      => $commission,
                'description' => 'Commission for transaction ID: ' . $transaction->id,
            ]);
        }

        Log::info('TransferController@store: transaction created', ['transaction_id' => $transaction->id]);

        return redirect()->route('transfers.show', $transaction->id)
            ->with('success', 'تمت عملية التحويل بنجاح. رقم العملية: ' . $transactionCode);

    } catch (\Exception $e) {
        Log::error('TransferController@store: Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'حدث خطأ أثناء معالجة التحويل: ' . $e->getMessage()]);
    }
}

// app/Http/Controllers/TransferController.php

public function show($id)
{
    // Find the transaction or fail with 404
    $transaction = SysTransaction::with([
            'senderCustomer',
            'receiverCustomer',
            'agent',
            'creator'
        ])
        ->findOrFail($id);

    // Authorize the view (make sure you have the policy set up)
   // $this->authorize('view', $transaction);
    // Get states list (make sure to define this in config/states.php)
    $states = config('states');
     $sys_regions= SysRegion::all();
    return view('agentuser.transfers.transfers', [
        'transaction' => $transaction,
        'states' => $states,
        'sys_regions' => $sys_regions
    ]);
}

public function print($id)
{
    $transaction = SysTransaction::with(['senderCustomer', 'receiverCustomer', 'agent'])->findOrFail($id);
    $states = config('states'); // If you store states in a config file

    $pdf = Pdf::loadView('agentuser.transfers.print', compact('transaction', 'states'))
              ->setPaper('a4')
              ->setOption([
                  'tempDir' => public_path(),
                  'chroot' => public_path(),
              ]);

    return $pdf->download('transfer-receipt-' . $transaction->transaction_code . '.pdf');
}
public function index()
{
    $user = auth()->user();
    $dateFrom = request('date_from', now()->toDateString());
    $dateTo = request('date_to', now()->toDateString());

    $transactions = SysTransaction::with(['senderCustomer', 'receiverCustomer'])
        ->where('sender_user_id', auth()->id())
        ->whereDate('created_at', '>=', $dateFrom)
        ->whereDate('created_at', '<=', $dateTo)
        ->orderBy('created_at', 'desc')
        ->paginate(1000);

    // Balance summary logic (from CashBoxController)
    $userId = auth()->id();
    $today = now()->toDateString();
    $cashboxTransactions = \App\Models\CashBoxTransaction::where('user_id', $userId)
        ->where('date', $today)
        ->get();

    $opening = $cashboxTransactions->where('type', 'opening')->sum('amount');
    $deposits = $cashboxTransactions->where('type', 'deposit')->sum('amount');
    $withdrawals = $cashboxTransactions->where('type', 'withdraw')->sum('amount');
    $refill = $cashboxTransactions->where('type', 'refill')->where('approved', true)->sum('amount');
    $bank = $cashboxTransactions->where('type', 'bank')->sum('amount');
    $commission = $cashboxTransactions->where('type', 'commission')->sum('amount');
    $deductions = abs($withdrawals);
    $closing = $opening + $deposits + $refill + $withdrawals + $bank + $commission;

    return view('agentuser.transfers.index', compact(
        'transactions',
        'opening', 'deposits', 'commission', 'refill', 'bank', 'deductions', 'closing'
    ));
}

public function receivedTransfers(Request $request)
{
    $user = auth()->user();
    $region_id = $user->region_id ?? null;
    $regionName = $user->region->name ?? 'Unknown Office';

    if (!$region_id) {
        return back()->withErrors(['error' => 'Your agent account does not have a state assigned.']);
    }

    $status = $request->query('status');
    $dateFrom = $request->input('date_from', now()->toDateString());
    $dateTo = $request->input('date_to', now()->toDateString());

    $transactions = SysTransaction::with(['senderCustomer', 'receiverCustomer', 'senderUser.region', 'receiverUser.region'])
        ->where('region_id', $region_id)
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->whereDate('created_at', '>=', $dateFrom)
        ->whereDate('created_at', '<=', $dateTo)
        ->latest()
        ->get();

    return view('agentuser.transfers.received', compact('transactions', 'regionName'));
}


public function SenderTransfers(Request $request)
{
    $user = auth()->user();
    $regionName = $user->region->name ?? 'Unknown Office';

    $status = $request->query('status');
    $dateFrom = $request->input('date_from', now()->toDateString());
    $dateTo = $request->input('date_to', now()->toDateString());
    $regionId = $request->input('region_id');

    $transactions = SysTransaction::with([
            'senderCustomer',
            'receiverCustomer',
            'senderUser.region',
            'receiverUser.region',
            'region'
        ])
        ->where('created_by', auth()->id()) // Only show transactions created by current user
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->when($regionId, function ($query) use ($regionId) {
            $query->where('region_id', $regionId);
        })
        ->whereDate('created_at', '>=', $dateFrom)
        ->whereDate('created_at', '<=', $dateTo)
        ->latest()
        ->get();

    // Get all regions for the filter dropdown
    $regions = SysRegion::all();

    return view('agentuser.transfers.sending', compact('transactions', 'regionName', 'regions'));
}


public function updateStatusAjax(Request $request)
{
    try {
        $request->validate([
            'transaction_id' => 'required|exists:sys_transactions,id',
            'status' => 'required|in:pending,completed,rejected,delivered',
        ]);

        $transaction = SysTransaction::findOrFail($request->transaction_id);
        Log::info('Updating transaction status', [
            'transaction_id' => $transaction->id,
            'current_status' => $transaction->status,
            'new_status' => $request->status
        ]);

        // Prevent changing status if already completed
        if ($transaction->status === 'completed') {
            Log::warning('Attempt to change completed transaction status', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status of a completed transaction.',
            ], 403);
        }

        // فقط منشئ السجل يمكنه تعيين الحالة إلى completed
        // if ($request->status === 'completed' && $transaction->created_by !== auth()->id()) {
        //     Log::warning('Unauthorized status update attempt', [
        //         'user_id' => auth()->id(),
        //         'transaction_id' => $transaction->id
        //     ]);
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Only the creator can mark this transaction as completed.',
        //     ], 403);
        // }

        // تحديث الحقول حسب الحالة
            $dataToUpdate = ['status' => $request->status];
       // if ($request->status === 'delivered') {
            $dataToUpdate['delivered_at'] = now();
            $dataToUpdate['delivered_by_user_id'] = auth()->id();
            $dataToUpdate['delivery_confirmation'] = 1;
      //  }
        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Update transaction status
            $transaction->update($dataToUpdate);
            // Create cashbox transaction if status is delivered
            if ($request->status === 'completed') {
                // Check if cashbox transaction already exists for this transfer
                $existingTransaction = CashBoxTransaction::where('reference_id', $transaction->id)
                    ->where('reference_type', 'transfer')
                    ->where('type', 'withdraw')
                    ->first();

                if (!$existingTransaction) {
                    $cashboxTransaction = new CashBoxTransaction();
                    $cashboxTransaction->user_id = auth()->id();
                    $cashboxTransaction->date = now()->toDateString();
                    $cashboxTransaction->type = 'withdraw';
                    $cashboxTransaction->amount = -$transaction->amount;
                    $cashboxTransaction->description = 'Transfer delivered, Transaction ID: ' . $transaction->id;
                    $cashboxTransaction->reference_id = $transaction->id;
                    $cashboxTransaction->reference_type = 'transfer';
                    $cashboxTransaction->status = 'completed';
                    $cashboxTransaction->save();
                } else {
                    Log::info('Cashbox transaction already exists for transfer', [
                        'transaction_id' => $transaction->id,
                        'cashbox_transaction_id' => $existingTransaction->id
                    ]);
                }
            }

            DB::commit();
            Log::info('Transaction status updated successfully', [
                'transaction_id' => $transaction->id,
                'new_status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update transaction status', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction: ' . $e->getMessage()
            ], 500);
        }

    } catch (\Exception $e) {
        Log::error('Status update validation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Error updating status: ' . $e->getMessage()
        ], 500);
    }
}



}
