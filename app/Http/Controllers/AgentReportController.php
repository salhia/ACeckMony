<?php

namespace App\Http\Controllers;

use App\Models\SysTransaction;
use App\Models\User;
use App\Models\SysRegion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class AgentReportController extends Controller
{
    private function getSubAgentIds($agentId)
    {
        try {
            Log::info('Getting sub agents for agent ID: ' . $agentId);

            $ids = DB::table('users')
                ->where('parent_agent_id', $agentId)
                ->limit(1000)
                ->pluck('id')
                ->toArray();

            $result = array_map('intval', $ids);
            Log::info('Found ' . count($result) . ' sub agents');
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getSubAgentIds: ' . $e->getMessage(), [
                'agent_id' => $agentId,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    private function getUsersByRegion($regionId)
    {
        try {
            Log::info('Getting users for region ID: ' . $regionId);

            $ids = DB::table('users')
                ->where('region_id', $regionId)
                ->limit(1000)
                ->pluck('id')
                ->toArray();

            $result = array_map('intval', $ids);
            Log::info('Found ' . count($result) . ' users in region');
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getUsersByRegion: ' . $e->getMessage(), [
                'region_id' => $regionId,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    private function getAuthorizedQuery($query)
    {
        try {
            $user = Auth::user();
            Log::info('Getting authorized query for user: ' . $user->id);

            if ($user->is_super_agent || $user->type === 'super_agent') {
                Log::info('User is super agent, returning full query');
                return $query;
            }

            $subAgentIds = $this->getSubAgentIds($user->id) ?: [];
            $regionUsers = $user->region_id ? $this->getUsersByRegion($user->region_id) : [];

            $allIds = array_merge($subAgentIds, $regionUsers);
            $allIds[] = (int)$user->id;

            $authorizedIds = array_values(array_unique(array_map('intval', $allIds)));
            Log::info('Found ' . count($authorizedIds) . ' authorized users');

            // Check if we're querying the users table or sys_transactions table
            if ($query instanceof \Illuminate\Database\Query\Builder && $query->from === 'users') {
                return $query->whereIn('id', $authorizedIds);
            }

            if ($query instanceof \Illuminate\Database\Eloquent\Builder && $query->getModel() instanceof User) {
                return $query->whereIn('id', $authorizedIds);
            }

            return $query->whereIntegerInRaw('sender_user_id', $authorizedIds);
        } catch (\Exception $e) {
            Log::error('Error in getAuthorizedQuery: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if we're querying the users table or sys_transactions table
            if ($query instanceof \Illuminate\Database\Query\Builder && $query->from === 'users') {
                return $query->where('id', Auth::id());
            }

            if ($query instanceof \Illuminate\Database\Eloquent\Builder && $query->getModel() instanceof User) {
                return $query->where('id', Auth::id());
            }

            return $query->where('sender_user_id', Auth::id());
        }
    }

    public function officeSummary(Request $request)
    {
        try {
            Log::info('Starting office summary report', $request->all());

            $baseQuery = SysTransaction::query()
                ->select(
                    'sender_region_id',
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('COALESCE(SUM(CAST(amount as DECIMAL(10,2))), 0) as total_amount'),
                    DB::raw('COALESCE(SUM(CAST(commission as DECIMAL(10,2))), 0) as total_commission'),
                    DB::raw('COALESCE(SUM(CAST(net_amount as DECIMAL(10,2))), 0) as total_net_amount')
                );

            if ($request->filled('date_from')) {
                $baseQuery->whereDate('created_at', '>=', $request->date_from);
                Log::info('Filtering by date_from: ' . $request->date_from);
            }

            if ($request->filled('date_to')) {
                $baseQuery->whereDate('created_at', '<=', $request->date_to);
                Log::info('Filtering by date_to: ' . $request->date_to);
            }

            $query = $this->getAuthorizedQuery($baseQuery);

            $results = $query
                ->with('senderRegion:id,name')
                ->groupBy('sender_region_id')
                ->orderBy('sender_region_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'sender_region_id' => (int)$item->sender_region_id,
                        'region_name' => $item->senderRegion ? $item->senderRegion->name : 'Unknown',
                        'total_transactions' => (int)$item->total_transactions,
                        'total_amount' => (float)$item->total_amount,
                        'total_commission' => (float)$item->total_commission,
                        'total_net_amount' => (float)$item->total_net_amount
                    ];
                });

            // Get regions using the model
            $regions = SysRegion::select('id', 'name')->get();
            Log::info('Found ' . $regions->count() . ' regions');

            return view('agent.reports.office_summary', [
                'summaryReport' => $results,
                'regions' => $regions
            ])->render();

        } catch (\Exception $e) {
            Log::error('Error in officeSummary: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            if (config('app.debug')) {
                return response()->json([
                    'error' => 'An error occurred while generating the report',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }

            return response()->json([
                'error' => 'An error occurred while generating the report'
            ], 500);
        }
    }

    public function officeDetailed(Request $request)
    {
        try {
            $perPage = 50;
            Log::info('Starting office detailed report', $request->all());

            $query = SysTransaction::query()
                ->select(
                    'sender_region_id',
                    'sender_user_id',
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('COALESCE(SUM(CAST(amount as DECIMAL(10,2))), 0) as total_amount'),
                    DB::raw('COALESCE(SUM(CAST(commission as DECIMAL(10,2))), 0) as total_commission'),
                    DB::raw('COALESCE(SUM(CAST(net_amount as DECIMAL(10,2))), 0) as total_net_amount')
                )
                ->when($request->filled('sender_region_id'), function($q) use ($request) {
                    return $q->where('sender_region_id', $request->sender_region_id);
                })
                ->when($request->filled('date_from'), function($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->date_to);
                })
                ->groupBy(['sender_region_id', 'sender_user_id']);

            $query = $this->getAuthorizedQuery($query);

            $results = $query
                ->with([
                    'senderUser:id,name,role,parent_agent_id,region_id',
                    'senderUser.parentAgent:id,name',
                    'senderUser.region:id,name',
                    'senderRegion:id,name'
                ])
                ->orderBy('sender_region_id')
                ->orderBy('sender_user_id')
                ->paginate($perPage);

            return view('agent.reports.office_detailed', [
                'detailedReport' => $results,
                'regions' => SysRegion::select('id', 'name')->get(),
                'users' => User::whereIn('id', $this->getAuthorizedQuery(User::query())->pluck('id'))
                    ->select('id', 'name')
                    ->get()
            ])->render();

        } catch (\Exception $e) {
            Log::error('Error in officeDetailed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            if (config('app.debug')) {
                return response()->json([
                    'error' => 'An error occurred while generating the report',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }

            return response()->json([
                'error' => 'An error occurred while generating the report'
            ], 500);
        }
    }

    public function userTransactions(Request $request)
    {
        $perPage = 20;
        $cacheKey = 'user_transactions_' . md5(json_encode($request->all()) . Auth::id() . $request->get('page', 1));

        return Cache::remember($cacheKey, 30, function () use ($request, $perPage) {
            $query = SysTransaction::query()
                ->select('id', 'sender_user_id', 'sender_region_id', 'amount', 'commission', 'net_amount', 'created_at')
                ->when($request->filled('sender_region_id'), function($q) use ($request) {
                    return $q->where('sender_region_id', $request->sender_region_id);
                })
                ->when($request->filled('user_id'), function($q) use ($request) {
                    return $q->where('sender_user_id', $request->user_id);
                })
                ->when($request->filled('date_from'), function($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->date_to);
                });

            $query = $this->getAuthorizedQuery($query);

            $transactions = $query->with([
                    'senderRegion:id,name',
                    'senderUser:id,name,parent_agent_id',
                    'senderUser.parentAgent:id,name',
                    'senderCustomer:id,name',
                    'receiverCustomer:id,name'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return view('agent.reports.user_transactions', [
                'transactions' => $transactions,
                'regions' => SysRegion::select('id', 'name')->get(),
                'users' => User::whereIn('id', $this->getAuthorizedQuery(User::query())->pluck('id'))
                    ->select('id', 'name')
                    ->get()
            ])->render();
        });
    }

    public function commissionReport(Request $request)
    {
        $perPage = 50;
        $cacheKey = 'commission_report_' . md5(json_encode($request->all()) . Auth::id() . $request->get('page', 1));

        return Cache::remember($cacheKey, 30, function () use ($request, $perPage) {
            $query = SysTransaction::query()
                ->select(
                    'sender_user_id',
                    'sender_region_id',
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(commission) as total_commission')
                )
                ->when($request->filled('sender_region_id'), function($q) use ($request) {
                    return $q->where('sender_region_id', $request->sender_region_id);
                })
                ->when($request->filled('date_from'), function($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->date_to);
                });

            $query = $this->getAuthorizedQuery($query);

            $commissionReport = $query
                ->with([
                    'senderUser:id,name,parent_agent_id',
                    'senderUser.parentAgent:id,name',
                    'senderRegion:id,name'
                ])
                ->groupBy('sender_user_id', 'sender_region_id')
                ->paginate($perPage);

            return view('agent.reports.commission', [
                'commissionReport' => $commissionReport,
                'regions' => SysRegion::select('id', 'name')->get(),
                'users' => User::whereIn('id', $this->getAuthorizedQuery(User::query())->pluck('id'))
                    ->select('id', 'name')
                    ->get()
            ])->render();
        });
    }

    // For admin: show transactions for a specific agent
    public function agentTransactions($agentId)
    {
        $agent = User::findOrFail($agentId);
        $transactions = SysTransaction::where('sender_user_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.agents.transactions', compact('agent', 'transactions'));
    }

    // For agent: show their own transactions
    public function myTransactions()
    {
        $agentId = auth()->id();
        $transactions = SysTransaction::where('sender_user_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('agent.transactions.history', compact('transactions'));
    }
}
