<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SysTransaction;
use App\Models\SysRegion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request)
    {
        $query = SysTransaction::with([
            'senderCustomer',
            'receiverCustomer',
            'senderAgent',
            'receiverAgent',
            'senderUser',
            'receiverUser',
            'creator',
            'region'
        ]);

        // Apply filters if provided
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(10);

        return view('admin.transactions.transaction_history', compact('transactions'));
    }

    /**
     * Display the specified transaction.
     */
    public function show($id)
    {
        $transaction = SysTransaction::with([
            'senderCustomer',
            'receiverCustomer',
            'senderAgent',
            'receiverAgent',
            'senderUser',
            'receiverUser',
            'creator',
            'region'
        ])->findOrFail($id);

        return view('admin.transactions.transaction_details', compact('transaction'));
    }

    /**
     * Print the transaction receipt.
     */
    public function print($id)
    {
        $transaction = SysTransaction::with([
            'senderCustomer',
            'receiverCustomer',
            'senderAgent',
            'receiverAgent',
            'senderUser',
            'receiverUser',
            'creator',
            'region'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.transactions.print_receipt', compact('transaction'))
                  ->setPaper('a4')
                  ->setOption([
                      'tempDir' => public_path(),
                      'chroot' => public_path(),
                  ]);

        return $pdf->download('transaction-receipt-' . $transaction->transaction_code . '.pdf');
    }

    /**
     * Update the transaction status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:completed,pending,rejected,delivered'
        ]);

        $transaction = SysTransaction::findOrFail($id);

        // Only allow status updates for pending transactions
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending transactions can be updated.');
        }

        $transaction->update([
            'status' => $request->status,
            'delivered_by' => $request->status === 'delivered' ? auth()->id() : null,
            'delivery_confirmation' => $request->status === 'delivered' ? 1 : 0
        ]);

        return redirect()->back()->with('success', 'Transaction status updated successfully.');
    }
}
