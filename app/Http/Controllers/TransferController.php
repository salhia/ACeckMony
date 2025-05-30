<?php

namespace App\Http\Controllers;

use App\Models\SysCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\SysTransaction;
use App\Models\User;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\State; // Ensure the correct namespace for the State model
use App\Models\SysRegion;

class TransferController extends Controller
{
    public function create()
    {
        $states = State::get();

        $sys_regions= SysRegion::all();

    return view('agentuser.transfers.create', compact('states','sys_regions'));
        return view('agentuser.transfers.create');
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
        'sender_id' => 'required',
        'receiver_id' => 'required',
        'amount' => 'required|numeric|min:0',
        'commission' => 'required|numeric|min:0',
        'region_id' => 'required|exists:sys_regions,id',
    ]);
    Log::info('TransferController@store: after validation', ['validated' => $validated]);

    $user = auth()->user();
    Log::info('TransferController@store: user found', ['user' => $user]);

    $agent = null;
    $adminFee = 0;

    if ($user && $user->parent_agent_id) {

        $agent = User::find($user->parent_agent_id);
    }

    if (!$agent) {
        Log::error('TransferController@store: No agent found for user', ['user_id' => $user->id]);
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'No agent is associated with this user. Transfer cannot be completed.']);
    }

    $commission_rate = $agent->commission_rate ?? 0;


    if ($validated['sender_id'] == $validated['receiver_id']) {
        Log::warning('TransferController@store: sender and receiver are the same');
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'لا يمكن أن يكون المرسل والمستلم نفس الشخص']);
    }

    try {
        $commission = $validated['commission'];
        $amount = $validated['amount'] + $commission;
         $adminFee = ($amount * $commission_rate) / 100;
        $netAmount = $validated['amount'];
        $finalDeliveredAmount = $validated['amount'];

        Log::info('TransferController@store: calculated values', [
            'commission' => $commission,
            'amount' => $amount,
            'adminFee' => $adminFee,
            'agent_id' => $agent ? $agent->id : null,
        ]);

        $transactionCode = 'TRX-' . strtoupper(uniqid());

        $transaction = SysTransaction::create([
            'transaction_code' => $transactionCode,
            'sender_customer_id' => $validated['sender_id'],
            'receiver_customer_id' => $validated['receiver_id'],

            // معلومات المرسل
            'sender_user_id' => $user->id,
            'sender_agent_id' => $agent->id,
            'sender_region_id' => $user->region_id,

            // معلومات المستلم
            'region_id' => $validated['region_id'],
            'receiver_agent_id' => null, // سيتم تحديده عند الاستلام

            // المبالغ والعمولات
            'amount' => $amount,
            'commission' => $commission,
            'admin_fee' => $adminFee,
            'agent_id' => $agent->id,
            'net_amount' => $netAmount,
            'final_delivered_amount' => $finalDeliveredAmount,

            // معلومات إضافية
            'transaction_type_id' => 1,
            'status' => 'completed',
            'notes' => $request->input('notes'),
            'created_by' => $validated['sender_id']
        ]);

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
    // Example: show transactions for the logged-in user or agent
    $transactions = SysTransaction::with(['senderCustomer', 'receiverCustomer'])
                    ->where('sender_user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(1000);

    return view('agentuser.transfers.index', compact('transactions'));
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

    $transactions = SysTransaction::with(['senderCustomer', 'receiverCustomer', 'senderUser.region', 'receiverUser.region'])
     ->where('region_id', $region_id)
     ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->latest()
        ->get();

    return view('agentuser.transfers.received', compact('transactions', 'regionName'));
}


public function SenderTransfers(Request $request)
{
    $user = auth()->user();
    $region_id = $user->region_id;

    $regionName = $user->region->name ?? 'Unknown Office';
//return view('agentuser.transfers.received', compact('transactions', 'regionName'));

    if (!$region_id) {
        return back()->withErrors(['error' => 'Your agent account does not have a state assigned.']);
    }

    $status = $request->query('status');

    $transactions = SysTransaction::with([
            'senderCustomer',
            'receiverCustomer',
            'senderUser.region',
            'receiverUser.region',
            'region'
        ])
        ->whereHas('senderUser', function ($query) use ($region_id) {
            $query->where('region_id', $region_id);
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->latest()
        ->get();

    return view('agentuser.transfers.sending', compact('transactions', 'regionName'));
}


public function updateStatusAjax(Request $request)
{
    $request->validate([
        'transaction_id' => 'required|exists:sys_transactions,id',
        'status' => 'required|in:pending,completed,rejected,delivered',
    ]);

    $transaction = SysTransaction::findOrFail($request->transaction_id);

    // فقط منشئ السجل يمكنه تعيين الحالة إلى completed
    if ($request->status === 'completed' && $transaction->created_by !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Only the creator can mark this transaction as completed.',
        ], 403);
    }

    // تحديث الحقول حسب الحالة
    $dataToUpdate = ['status' => $request->status];

    if ($request->status === 'delivered') {
        $dataToUpdate['delivered_by'] = auth()->id();
        $dataToUpdate['delivery_confirmation'] = 1;
    }

    $transaction->update($dataToUpdate);

    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully',
        'new_status' => $request->status
    ]);
}



}
