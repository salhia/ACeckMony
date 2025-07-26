<?php

namespace App\Http\Controllers;

use App\Models\SysTransaction;
use App\Models\SysAgentEarning;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CashBoxTransaction;
use App\Models\SysRegion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AgentDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $region_id = $user->region_id;
            $agent_id = $user->parent_agent_id;
            $user_id = $user->id;


        $today = \Carbon\Carbon::today()->toDateString();

       $exists =CashBoxTransaction::where('user_id', $user->id)
        ->where('date', $today)
        ->where('type', 'opening')
        ->exists();

    if (!$exists) {
        return redirect()->route('agentuser.cashbox.opening.form');
    }


            // Collect region statistics - update for sender and receiver
            $regionTransactions = SysTransaction::where(function($query) use ($region_id) {
                $query->where('region_id', $region_id) // Received in region
                      ->orWhere('sender_region_id', $region_id); // Sent from region
            })
            ->whereDate('created_at', Carbon::today())
            ->select(
                DB::raw('COALESCE(SUM(CASE WHEN sender_region_id = ' . $region_id . ' THEN amount ELSE 0 END), 0) as today_sent'),
                DB::raw('COALESCE(SUM(CASE WHEN region_id = ' . $region_id . ' AND status = "completed" THEN final_delivered_amount ELSE 0 END), 0) as today_received'),
                DB::raw('COUNT(DISTINCT CASE WHEN sender_region_id = ' . $region_id . ' THEN id END) as today_count'),
                DB::raw('COALESCE(SUM(CASE WHEN sender_region_id = ' . $region_id . ' THEN commission ELSE 0 END), 0) as today_commission')
            )->first();

            // Collect user statistics - update for sending and receiving agent
            $userTransactions = SysTransaction::where(function($query) use ($user_id, $region_id) {
                $query->where('sender_user_id', $user_id); // Sent transfers
            })
            ->whereDate('created_at', Carbon::today())
            ->select(
                // For sent transactions (by this agent)
                DB::raw('COALESCE(SUM(CASE WHEN sender_user_id = ' . $user_id . ' THEN amount ELSE 0 END), 0) as today_sent'),
                // Count unique transactions for this agent
                DB::raw('COUNT(DISTINCT CASE WHEN sender_user_id = ' . $user_id . ' OR receiver_agent_id = ' . $agent_id . ' THEN id END) as today_count'),
                // Commission for this agent
                DB::raw('COALESCE(SUM(CASE WHEN sender_user_id = ' . $user_id . ' OR receiver_agent_id = ' . $agent_id . ' THEN commission ELSE 0 END), 0) as today_commission')
            )->first();

            // Get raw transaction data for debugging
            $rawTransactions = SysTransaction::where(function($query) use ($agent_id) {
                $query->where('sender_agent_id', $agent_id)
                      ->orWhere('receiver_agent_id', $agent_id);
            })
            ->whereDate('created_at', Carbon::today())
            ->get();

            // Compile data into a single array
            $stats = [
                'region' => [
                    'name' => $user->region->name ?? 'Not Specified',
                    'sent' => [
                        'today' => $regionTransactions->today_sent ?? 0,
                        'total' => $regionTransactions->today_sent ?? 0,
                        'label' => 'Region Sent Amount Today'
                    ],
                    'received' => [
                        'today' => $regionTransactions->today_received ?? 0,
                        'total' => $regionTransactions->today_received ?? 0,
                        'label' => 'Region Received Amount Today'
                    ],
                    'commission' => [
                        'today' => $regionTransactions->today_commission ?? 0,
                        'total' => $regionTransactions->today_commission ?? 0,
                        'label' => 'Region Commission Today'
                    ],
                    'transactions' => [
                        'today' => $regionTransactions->today_count ?? 0,
                        'total' => $regionTransactions->today_count ?? 0,
                        'label' => 'Region Transactions Today'
                    ]
                ],
                'user' => [
                    'name' => $user->name,
                    'sent' => [
                        'today' => $userTransactions->today_sent ?? 0,
                        'total' => $userTransactions->today_sent ?? 0,
                        'label' => 'My Sent Amount Today'
                    ],
                    'commission' => [
                        'today' => $userTransactions->today_commission ?? 0,
                        'total' => $userTransactions->today_commission ?? 0,
                        'label' => 'My Commission Today'
                    ],
                    'transactions' => [
                        'today' => $userTransactions->today_count ?? 0,
                        'total' => $userTransactions->today_count ?? 0,
                        'label' => 'My Transactions Today'
                    ]
                ]
            ];

            // Add transactions using updated functions
            $stats['region']['received_transactions'] = $this->getReceivedTransactions($region_id);
            $stats['region']['sent_transactions'] = $this->getSentTransactions($region_id);

            // Update chart function to match the same logic
            $stats['region']['chart_data'] = $this->getChartData($region_id, null);

            // جلب بيانات الرصيد من CashBoxTransaction
            $userId = $user->id;
            $today = \Carbon\Carbon::today()->toDateString();
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

            return view('agentuser.index', compact(
                'stats',
                'opening', 'deposits', 'commission', 'refill', 'bank', 'deductions', 'closing'
            ));

        } catch (\Exception $e) {
            return view('agentuser.index')->with('error', 'Error loading data');
        }
    }

    private function getChartData($region_id = null, $agent_id = null)
    {
        $dates = [];
        $sentData = [];
        $receivedData = [];

        for($i = 23; $i >= 0; $i--) {
            $hour = Carbon::today()->addHours($i);
            $nextHour = Carbon::today()->addHours($i + 1);

            $dates[] = $hour->format('H:00');

            // Received transfers in region
            $receivedAmount = SysTransaction::where('region_id', $region_id)
                ->whereBetween('created_at', [$hour, $nextHour])
                ->sum('final_delivered_amount') ?? 0;

            // Sent transfers from region
            $sentAmount = SysTransaction::where('sender_region_id', $region_id)
                ->whereBetween('created_at', [$hour, $nextHour])
                ->sum('amount') ?? 0;

            $sentData[] = $sentAmount;
            $receivedData[] = $receivedAmount;
        }

        return [
            'dates' => $dates,
            'sent' => $sentData,
            'received' => $receivedData
        ];
    }

    // Update received transactions function
    private function getReceivedTransactions($region_id)
    {
        return SysTransaction::where('region_id', $region_id)
            ->whereDate('created_at', Carbon::today())
            ->with([
                'senderCustomer',
                'receiverCustomer',
                'senderAgent',
                'senderRegion'
            ])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    // Update sent transactions function
    private function getSentTransactions($region_id)
    {
        return SysTransaction::where('sender_region_id', $region_id)
            ->whereDate('created_at', Carbon::today())
            ->with([
                'senderCustomer',
                'receiverCustomer',
                'senderAgent',
                'senderRegion'
            ])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    // Reports page
    public function reports(Request $request)
    {
        $user = Auth::user();
        $region_id = $user->region_id;
        $type = $request->get('type', 'daily'); // default to daily if no type specified

        // Get agents in the same region (excluding admins)
        $agents = User::where('region_id', $region_id)
                     ->where('role', '!=', 'admin')
                     ->select('id', 'name')
                     ->get();

        $view_data = [
            'region' => SysRegion::find($region_id),
            'agents' => $agents,
            'type' => $type
        ];

        return view('agentuser.reports.index', $view_data);
    }

        public function getReportsData(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'agent_id' => 'nullable',
                'type' => 'nullable|in:daily,sent,received,commission,summary'
            ]);

            $user = Auth::user();
            $region_id = $user->region_id;
            $type = $request->get('type', 'daily');

            // Base query
            $query = SysTransaction::query();

            // Apply different filters based on report type
            switch ($type) {
                case 'sent':
                    $query->where('sender_region_id', $region_id);
                    break;
                case 'received':
                    $query->where('region_id', $region_id)
                          ->where('status', 'completed');
                    break;
                case 'commission':
                    $query->where(function($q) use ($region_id) {
                        $q->where('region_id', $region_id)
                          ->orWhere('sender_region_id', $region_id);
                    })->whereNotNull('commission');
                    break;
                case 'summary':
                    $query->where(function($q) use ($region_id) {
                        $q->where('region_id', $region_id)
                          ->orWhere('sender_region_id', $region_id);
                    });
                    break;
                default: // daily
                    $query->where(function($q) use ($region_id) {
                        $q->where('region_id', $region_id)
                          ->orWhere('sender_region_id', $region_id);
                    });
                    break;
            }

            // Date range filter
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);

            // Agent filter - Fixed to check the correct agent fields
            if ($request->filled('agent_id') && $request->agent_id != '' && $request->agent_id != 'all') {
                $agent_id = $request->agent_id;
                $query->where(function($q) use ($agent_id) {
                    $q->where('sender_user_id', $agent_id)
                      ->orWhere('sender_agent_id', $agent_id)
                      ->orWhere('receiver_agent_id', $agent_id)
                      ->orWhere('agent_id', $agent_id)
                      ->orWhere('created_by', $agent_id);
                });
            }

            // Get transactions with relationships
            $transactions = $query->with([
                'senderCustomer',
                'receiverCustomer',
                'senderAgent',
                'receiverAgent',
                'senderRegion'
            ])->orderBy('created_at', 'desc')->get();

            // Calculate statistics
            $stats = [
                'total_sent' => $transactions->where('sender_region_id', $region_id)->sum('amount'),
                'total_received' => $transactions->where('region_id', $region_id)->where('status', 'completed')->sum('final_delivered_amount'),
                'total_commission' => $transactions->sum('commission'),
                'count' => $transactions->count()
            ];

            return view('agentuser.reports.results', compact('transactions', 'stats', 'type'))
                ->render();

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error generating report',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
