<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\SysRegion;
use Illuminate\Support\Facades\DB;
use App\Models\SysTransaction;
use App\Models\CashBoxTransaction;

use App\Models\AdminFee;
use PDF;

class AgentController extends Controller
{
    public function AgentDashboard(Request $request){
        $id = Auth::user()->id;
        $user = User::find($id);


        // Get date range from request or default to today
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        // Get the parent agent (the actual agent)
        $agentId = $user->parent_agent_id;
        $agent = User::find($agentId);

        // Get agent's region

        // Get total registered users for this agent
        $totalUsers = User::where('parent_agent_id', $agentId)->count();

        // Initialize stats array
        $stats = [
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'total_users' => $totalUsers,
            'region' => [
                'name' => $region->name ?? 'Not Specified',
                'sent' => [
                    'label' => 'Total Sent',
                    'today' => 0,
                    'total' => 0
                ],
                'received' => [
                    'label' => 'Total Received',
                    'today' => 0,
                    'total' => 0
                ],
                'commission' => [
                    'label' => 'Total Commission',
                    'today' => 0,
                    'total' => 0
                ],
                'transactions' => [
                    'label' => 'Total Transactions',
                    'today' => 0,
                    'total' => 0
                ],
                'chart_data' => [
                    'sent' => [],
                    'received' => [],
                    'dates' => []
                ],
                'state_stats' => [],
                'user_stats' => [],
                'top_states' => [],
                'top_users' => []
            ]
        ];

        // Get all transactions for this agent within date range
        $allTransactions = SysTransaction::where('agent_id', $agentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['senderCustomer', 'receiverAgent', 'region'])
            ->get();

        // Get today's transactions
        $todayTransactions = $allTransactions->where('created_at', '>=', Carbon::today());

        // Calculate total statistics
        $stats['region']['sent']['total'] = $allTransactions->sum('amount');
        $stats['region']['received']['total'] = $allTransactions->sum('amount');
        $stats['region']['commission']['total'] = $allTransactions->sum('commission');
        $stats['region']['transactions']['total'] = $allTransactions->count();

        // Calculate today's statistics
        $stats['region']['sent']['today'] = $todayTransactions->sum('amount');
        $stats['region']['received']['today'] = $todayTransactions->sum('amount');
        $stats['region']['commission']['today'] = $todayTransactions->sum('commission');
        $stats['region']['transactions']['today'] = $todayTransactions->count();

        // Get state-wise statistics
        $stateStats = $allTransactions->groupBy('region_id')
            ->map(function($transactions) {
                return [
                    'name' => $transactions->first()->region->name ?? 'Unknown',
                    'total_amount' => $transactions->sum('amount'),
                    'total_transactions' => $transactions->count(),
                    'total_commission' => $transactions->sum('commission')
                ];
            });

        // Get user-wise statistics
        $userStats = $allTransactions->groupBy('sender_user_id')
            ->map(function($transactions) {
                return [
                    'name' => $transactions->first()->senderUser->name ?? 'Unknown',
                    'total_amount' => $transactions->sum('amount'),
                    'total_transactions' => $transactions->count(),
                    'total_commission' => $transactions->sum('commission')
                ];
            });

        // Get top 5 states by amount
        $stats['region']['top_states'] = $stateStats->sortByDesc('total_amount')
            ->take(5)
            ->values()
            ->toArray();

        // Get top 5 users by amount
        $stats['region']['top_users'] = $userStats->sortByDesc('total_amount')
            ->take(5)
            ->values()
            ->toArray();

        // Add state and user statistics
        $stats['region']['state_stats'] = $stateStats->values()->toArray();
        $stats['region']['user_stats'] = $userStats->values()->toArray();

        // Get daily data for chart
        $dateRange = collect();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateRange->push($currentDate->copy());
            $currentDate->addDay();
        }

        foreach ($dateRange as $date) {
            $stats['region']['chart_data']['dates'][] = $date->format('Y-m-d');

            // Get sent amount for this date
            $sentAmount = SysTransaction::where('agent_id', $agentId)
                ->whereDate('created_at', $date)
                ->sum('amount');
            $stats['region']['chart_data']['sent'][] = $sentAmount;

            // Get received amount for this date
            $receivedAmount = SysTransaction::where('agent_id', $agentId)
                ->whereDate('created_at', $date)
                ->sum('amount');
            $stats['region']['chart_data']['received'][] = $receivedAmount;
        }

        // Get pending and paid amounts
        $pendingAmount = AdminFee::where('user_id', $agentId)
            ->where('status', 'pending')
            ->sum('amount');

        $paidAmount = AdminFee::where('user_id', $agentId)
            ->where('status', 'paid')
            ->sum('paid_amount');

        return view('agent.index', compact('stats', 'pendingAmount', 'paidAmount'));
    }

    public function AgentLogin(){
        return view('agent.agent_login');
    }

    public function AgentRegister(Request $request){

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'agent',
            'status' => 'inactive',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::AGENT);
    }

    public function AgentLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Agent Succesfully Logout',
            'alert-type' => 'success'
        );

           return redirect()->route('login')->with($notification);
    }

    public function AgentProfile(){
        $id = Auth::user()->id; //collect user data from database
        $profileData = User::find($id); //Laravel Eloquent
        return view('agent.agent_profile_view',compact('profileData'));
    } //End Method

    public function AgentProfileStore(Request $request){
        $id = Auth::user()->id; //collect user data from database
        $data = User::find($id); //Laravel Eloquent
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->description = $request->description;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/agent_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/agent_images'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Agent Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function AgentChangePassword(){

        $id = Auth::user()->id; //collect user data from database
        $profileData = User::find($id); //Laravel Eloquent
        return view('agent.agent_change_password',compact('profileData'));
    }

    public function AgentUpdatePassword(Request $request){

        //Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        //Match The old password
        if(!Hash::check($request->old_password, auth::user()->password)){

            $notification = array(
                'message' => 'Old password does not match',
                'alert-type' => 'error'
            );

            return back()->with($notification);
        };

        //update the password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Passord change successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }


    public function AllAgent()
    {

        $allagent = User::where('role', 'user')
       ->where('parent_agent_id', auth()->id())
       ->with(['region', 'parentAgent'])
       ->get();


        return view('agent.agentuser.all_agent', compact('allagent'));
    }

    public function AddAgent()
    {
         $regions = SysRegion::all(); // Fetch all regions
$agents = User::where('role', 'agent')
                  ->where('parent_agent_id', auth()->id())
                  ->get();
    return view('agent.agentuser.add_agent', compact('regions'));
    }

    public function StoreAgent(Request $request)
    {
        // Validate the incoming request
       $request->validate([
    'name' => 'required|string|max:255',
 //   'email' => 'required|email|unique:users,email,' . ($request->id ?? 'NULL'),
    'phone' => 'required|string|max:15',
    'address' => 'required|string|max:255',
    'region_id' => 'required|integer',
    'commission_rate' => 'nullable|numeric|min:0',
    'transfer_limit' => 'nullable|numeric|min:0',
    'parent_agent_id' => 'nullable|exists:users,id',
    'description' => 'nullable|string',
    'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
]);
        // Initialize $save_url variable
        $save_url = null;

        // Check if a photo file is uploaded
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Generate a unique filename with the original extension
            $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            // Move the uploaded file to the desired directory
            $file->move(public_path('upload/agent_images'), $filename);

            // Set the save URL to store in the database
            $save_url = 'upload/agent_images/' . $filename;
        }

        // Create a new agent record
        User::create([
    'name' => $request->name,
    'username' => $request->username,
  //  'email' => $request->email,
    'phone' => $request->phone,
    'address' => $request->address,
    'photo' => $save_url,
    'password' => Hash::make($request->password),
    'status' => 'active',
    'role' => 'user',
    'region_id' => $request->region_id,
    'commission_rate' => $request->commission_rate ?? 0,
    'parent_agent_id' => auth()->id(),
    'transfer_limit' => $request->transfer_limit ?? 0,
    'is_active' => 1,
    'description' => $request->description,
]);

        // Prepare a success notification
        $notification = array(
            'message' => 'Agent Successfully Created',
            'alert-type' => 'success'
        );

        // Redirect to the agents list page with notification
        return redirect()->route('all.agent')->with($notification);
    }


    public function EditAgent($id)
    {
        $allagent = User::findOrFail($id);
        return view('agent.agentuser.edit_agent', compact('allagent'));
    }

    public function UpdateAgent(Request $request)
    {
        $user_id = $request->id;
        $user = User::findOrFail($user_id); // Retrieve the user by ID
        // Check if a new photo was uploaded
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Generate a unique filename with the original extension
            $filename = date('YmdHi') . '.' . $file->getClientOriginalExtension();
            // Move the uploaded file to the desired directory
            $file->move(public_path('upload/agent_images'), $filename);
            // Delete the old photo if it exists
            if ($user->photo && file_exists(public_path('upload/agent_images/' . $user->photo))) {
                @unlink(public_path('upload/agent_images/' . $user->photo));
            }
            // Update the user's photo attribute
            $user->photo = $filename;
        }
        // Update the user's other attributes
     $user->name = $request->name;
  //   $user->email = $request->email;
     $user->phone = $request->phone;
     $user->address = $request->address;
     $user->region_id = $request->region_id;
     $user->commission_rate = $request->commission_rate ?? 0;
     $user->transfer_limit = $request->transfer_limit ?? 0;
     $user->username = $request->username;
   //  $user->parent_agent_id = $request->parent_agent_id ?? null;
     $user->description = $request->description ?? '';
     $user->updated_at = Carbon::now();
        // Save the changes to the database
        $user->save();
        $notification = [
            'message' => 'Agent Profile Updated Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.agent')->with($notification);
    }

    public function DeleteAgent(Request $request)
    {
        $user_id = $request->id;
        $user = User::findOrFail($user_id);
        // Check if the user has a photo
        if ($user->photo) {
            // Get the photo path
            $photo_path = public_path($user->photo);
            // Debugging: Log the photo path
            Log::info('Photo path: ' . $photo_path);
            // Check if the file exists and delete it
            if (file_exists($photo_path)) {
                // Debugging: Log the file exists
                Log::info('File exists, attempting to delete: ' . $photo_path);
                if (unlink($photo_path)) {
                    // Debugging: Log successful deletion
                    Log::info('File successfully deleted: ' . $photo_path);
                } else {
                    // Debugging: Log failure to delete
                    Log::warning('Failed to delete file: ' . $photo_path);
                }
            } else {
                // Debugging: Log file does not exist
                Log::warning('File does not exist: ' . $photo_path);
            }
        }
        // Delete the user from the database
        $user->delete();
        $notification = array(
            'message' => 'Agent Successfully Deleted',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }


    public function changeStatus(Request $request)
    {

        $user = User::find($request->user_id);
        // Toggle status between 'active' and 'inactive'
        $user->status = $user->status == 'active' ? 'inactive' : 'active';
        //$user->status = $request->status ==1? 'active' : 'inactive';
        $user->save();
        return response()->json(['success' => 'Status Change Successfully']);
    } // End Method

    public function paymentHistory(Request $request)
    {
        $agentId = Auth::user()->id;
        $query = AdminFee::where('user_id', $agentId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->start_date . ' 00:00:00';
            $end = $request->end_date . ' 23:59:59';
            $query->whereBetween('paid_at', [$start, $end]);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // PDF Export
        if ($request->has('export_pdf')) {
            // Get all results for PDF (not paginated)
            $allPayments = $query->orderBy('created_at', 'desc')->get();
            $pdf = PDF::loadView('agent.reports.payment_history_pdf', compact('allPayments'));
            return $pdf->download('payment-history.pdf');
        }

        return view('agent.reports.payment_history', compact('payments'));
    }

    public function paymentsGroupedByDate(Request $request)
    {
        $agentId = Auth::user()->id;
        $query = AdminFee::where('user_id', $agentId)
            ->where('status', 'paid');

        if ($request->filled('date')) {
            $query->whereDate('paid_at', $request->date);
        }

        $payments = $query->orderBy('paid_at', 'desc')->get()
            ->groupBy(function($item) {
                return $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('Y-m-d') : 'No Date';
            });

        // Handle PDF export
        if ($request->has('export_pdf')) {
            $pdf = PDF::loadView('agent.reports.payments_grouped_by_date_pdf', compact('payments'));
            return $pdf->download('payments-grouped-by-date.pdf');
        }

        return view('agent.reports.payments_grouped_by_date', compact('payments'));
    }
}

