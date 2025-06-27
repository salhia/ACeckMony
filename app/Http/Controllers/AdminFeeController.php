<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminFee;
use App\Models\SysTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminFeeController extends Controller
{
    /**
     * نقل الدفعات من sys_transactions إلى admin_fees
     */
    public function transferPayments()
    {
        try {
            DB::beginTransaction();

            // التحقق من وجود معاملات في sys_transactions
            $totalTransactions = SysTransaction::count();
            Log::info('Total transactions in sys_transactions: ' . $totalTransactions);

            // التحقق من المعاملات المكتملة
            $completedTransactions = SysTransaction::where('status', 'completed')->count();
            Log::info('Completed transactions: ' . $completedTransactions);

            // جلب المعاملات التي لم يتم نقلها بعد
            $transactions = SysTransaction::whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sys_admin_fees')
                    ->whereRaw('sys_admin_fees.transaction_id = sys_transactions.id');
            }) ->get();
           // ->where('status', 'completed')


            Log::info('Transactions to be processed: ' . $transactions->count());

            if ($transactions->isEmpty()) {
                Log::info('No new transactions to process');
                return response()->json([
                    'success' => true,
                    'message' => 'لا توجد معاملات جديدة للنقل',
                    'count' => 0
                ]);
            }

            $processedCount = 0;
            $errors = [];

            foreach ($transactions as $transaction) {
                try {
                    // التحقق من وجود المستخدم
                    $user = User::find($transaction->agent_id);


                    if (!$user) {
                        Log::error('User not found for transaction #' . $transaction->id);
                        $errors[] = 'المستخدم غير موجود للمعاملة #' . $transaction->id;
                        continue;
                    }

                    // الحصول على نسبة العمولة من جدول المستخدم
                    $percentage = $user->commission_rate ?? 0;

                    Log::info('Processing transaction #' . $transaction->id . ' - Amount: ' . $transaction->amount);

                    // إنشاء سجل جديد في admin_fees
                    AdminFee::create([
                        'user_id' => $transaction->agent_id,
                        'trnsferamount' => $transaction->amount,
                        'amount' => $transaction->admin_fee,
                        'percentage' => $percentage,

                        'status' => 'pending',
                        'description' => 'عمولة من معاملة #' . $transaction->id,
                        'transaction_id' => $transaction->id
                    ]);

                    $processedCount++;
                    Log::info('Successfully processed transaction #' . $transaction->id);

                } catch (\Exception $e) {
                    Log::error('Error processing transaction #' . $transaction->id . ': ' . $e->getMessage());
                    $errors[] = 'خطأ في معالجة المعاملة #' . $transaction->id . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'تم نقل الدفعات بنجاح',
                'count' => $processedCount,
                'total_transactions' => $totalTransactions,
                'completed_transactions' => $completedTransactions,
                'processed_transactions' => $transactions->count()
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Critical error in transferPayments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء نقل الدفعات: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * عرض تقرير الدفعات
     */
    public function index(Request $request)
    {
        try {
            // جلب جميع الدفعات مع العلاقات
            $query = AdminFee::with(['user', 'transaction']);

            // فلترة حسب التاريخ
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // فلترة حسب الوكيل
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // فلترة حسب الحالة
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            } else {
                // افتراضياً، عرض المدفوعات المعلقة فقط
                $query->where('status', 'pending');
            }

            // حساب الإجماليات من نفس الاستعلام
            $totalAmount = (clone $query)->sum('amount');
            $totalPending = (clone $query)->where('status', 'pending')->sum('amount');
            $totalPaid = (clone $query)->where('status', 'paid')->sum('amount');

            // جلب الوكلاء مع حساب المبالغ المعلقة لكل وكيل
            $agents = User::where('role', 'agent')->get()->map(function ($agent) {
                $pendingAmount = AdminFee::where('user_id', $agent->id)
                    ->where('status', 'pending')
                    ->sum('amount');

                $agent->pending_amount = $pendingAmount;
                return $agent;
            });

            // جلب الدفعات مع الترقيم
            $payments = $query->latest()->paginate(20);

            // للتأكد من وجود البيانات
            Log::info('Payments query:', [
                'payments_count' => $payments->items() ? count($payments->items()) : 0,
                'total_amount' => $totalAmount,
                'total_pending' => $totalPending,
                'total_paid' => $totalPaid,
                'filters' => [
                    'user_id' => $request->user_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'status' => $request->status
                ]
            ]);

            if ($payments->isEmpty()) {
                Log::info('No payments found with current filters');
            }

            return view('admin.reports.agent-payments', compact(
                'payments',
                'totalAmount',
                'totalPending',
                'totalPaid',
                'agents'
            ));

        } catch (\Exception $e) {
            Log::error('Error in AdminFeeController@index: ' . $e->getMessage());
            return view('admin.reports.agent-payments', [
                'payments' => collect(),
                'totalAmount' => 0,
                'totalPending' => 0,
                'totalPaid' => 0,
                'agents' => collect(),
                'error' => 'حدث خطأ أثناء جلب البيانات'
            ]);
        }
    }

    /**
     * تحديث حالة الدفع
     */
    public function updateStatus(Request $request, AdminFee $adminFee)
    {
        $request->validate([
            'status' => 'required|in:pending,paid',
            'amount' => 'required|numeric|min:0|max:' . $adminFee->amount,
            'notes' => 'nullable|string'
        ]);

        $adminFee->update([
            'status' => $request->status,
            'paid_amount' => $request->amount,
            'payment_notes' => $request->notes,
            'paid_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الدفع بنجاح'
        ]);
    }

    public function history(Request $request)
    {
        $query = AdminFee::with('user')
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc');

        // Filter by agent
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->paginate(10);
        $agents = User::where('role', 'agent')->get();

        // حساب مجموع المدفوع لكل وكيل
        $agentPaidTotals = AdminFee::where('status', 'paid')
            ->select('user_id', DB::raw('SUM(paid_amount) as total_paid'))
            ->groupBy('user_id')
            ->pluck('total_paid', 'user_id');

        return view('admin.reports.agent-payments-history', compact('payments', 'agents', 'agentPaidTotals'));
    }

    public function processPayments(Request $request)
    {
        try {
            DB::beginTransaction();

            // التحقق من وجود البيانات المطلوبة
            if (!$request->has('payment_ids') || !$request->has('amount')) {
                throw new \Exception('البيانات المطلوبة غير مكتملة');
            }

            $request->validate([
                'payment_ids' => 'required|array',
                'payment_ids.*' => 'exists:sys_admin_fees,id',
                'amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'paid_at' => 'required|date_format:Y-m-d\TH:i'
            ]);

            $paymentIds = $request->payment_ids;
            $totalAmount = floatval($request->amount);
            $notes = $request->notes;
            $paidAt = $request->paid_at;

            // التحقق من وجود الدفعات
            $payments = AdminFee::whereIn('id', $paymentIds)
                ->where('status', 'pending')
                ->get();

            if ($payments->isEmpty()) {
                throw new \Exception('لم يتم العثور على الدفعات المحددة أو أنها غير معلقة');
            }

            // التحقق من أن المبلغ المحدد يتطابق مع مجموع المبالغ المعلقة
            $pendingAmount = $payments->sum('amount');

            if (abs($pendingAmount - $totalAmount) > 0.01) { // السماح بفارق صغير بسبب التقريب
                throw new \Exception(sprintf(
                    'المبلغ المحدد (%s) لا يتطابق مع مجموع المبالغ المعلقة (%s)',
                    number_format($totalAmount, 2),
                    number_format($pendingAmount, 2)
                ));
            }

            // التحقق من أن جميع الدفعات تعود لنفس الوكيل
            $agentId = $payments->first()->user_id;
            if ($payments->where('user_id', '!=', $agentId)->isNotEmpty()) {
                throw new \Exception('يجب أن تكون جميع الدفعات لنفس الوكيل');
            }

            // تحديث حالة الدفعات
            foreach ($payments as $payment) {
                $payment->update([
                    'status' => 'paid',
                    'paid_amount' => $payment->amount,
                    'payment_notes' => $notes,
                    'paid_at' => $paidAt ? Carbon::parse($paidAt) : now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم معالجة الدفعات بنجاح',
                'data' => [
                    'processed_count' => $payments->count(),
                    'total_amount' => $totalAmount
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error in processPayments: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in processPayments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
