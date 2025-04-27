<?php
// app/Http/Controllers/TransferController.php
namespace App\Http\Controllers;

use App\Models\SysCustomer;
use App\Models\SysTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    // عرض صفحة التحويل
    public function create()
    {
        $customers = SysCustomer::where('registered_by', auth()->id())->get();
        return view('agentuser.transfers.create', compact('customers'));
    }

    // البحث عن الزبون
    public function searchCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'بحث غير صحيح'], 400);
        }

        $searchTerm = $request->search;

        $customer = SysCustomer::where(function($query) use ($searchTerm) {
                            $query->where('phone', $searchTerm)
                                  ->orWhere('identity_number', $searchTerm);
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

    // إضافة زبون جديد
    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:sys_customers,phone',
            'identity_number' => 'nullable|string|max:50|unique:sys_customers,identity_number'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $customer = SysCustomer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'identity_number' => $request->identity_number,
            'identity_type' => 'id_card',
            'registered_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    // حفظ عملية التحويل
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:sys_customers,id',
            'receiver_id' => 'required|exists:sys_customers,id',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ]);

        // ... (كود معالجة التحويل) ...

        return redirect()->route('transfers.index')->with('success', 'تمت عملية التحويل بنجاح');
    }
}
