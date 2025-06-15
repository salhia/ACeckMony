<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;
use function PHPUnit\Framework\fileExists;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\SysTransaction;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function AdminDashboard(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        // Get total agents
        $totalAgents = User::where('role', 'agent')->count();

        // Get all transactions within date range
        $allTransactions = SysTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['senderCustomer', 'receiverAgent', 'sendRegion', 'senderAgent'])
            ->get();

        // Get today's transactions
        $todayTransactions = $allTransactions->where('created_at', '>=', Carbon::today());

        // Calculate total statistics
        $stats = [
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'total_agents' => $totalAgents,
            'sendRegion' => [
                'name' => 'All Regions',
                'sent' => [
                    'label' => 'Total Transfers',
                    'today' => $todayTransactions->sum('amount'),
                    'total' => $allTransactions->sum('amount')
                ],
                'received' => [
                    'label' => 'Total Received',
                    'today' => $todayTransactions->sum('amount'),
                    'total' => $allTransactions->sum('amount')
                ],
                'commission' => [
                    'label' => 'Total Admin Fee',
                    'today' => $todayTransactions->sum('admin_fee'),
                    'total' => $allTransactions->sum('admin_fee')
                ],
                'transactions' => [
                    'label' => 'Total Transactions',
                    'today' => $todayTransactions->count(),
                    'total' => $allTransactions->count()
                ],
                'chart_data' => [
                    'sent' => [],
                    'received' => [],
                    'dates' => []
                ],
                // State-wise statistics (by sender_region_id)
                'state_stats' => $allTransactions->groupBy('sender_region_id')
                    ->map(function($transactions) {
                        return [
                            'name' => $transactions->first()->sendRegion->name ?? 'Unknown',
                            'total_amount' => $transactions->sum('amount'),
                            'total_transactions' => $transactions->count(),
                            'total_admin_fee' => $transactions->sum('admin_fee')
                        ];
                    })->values()->toArray(),
                // Top 5 states by amount (by sender_region_id)
                'top_states' => $allTransactions->groupBy('sender_region_id')
                    ->map(function($transactions) {
                        return [
                            'name' => $transactions->first()->sendRegion->name ?? 'Unknown',
                            'total_amount' => $transactions->sum('amount'),
                            'total_transactions' => $transactions->count(),
                            'total_admin_fee' => $transactions->sum('admin_fee')
                        ];
                    })->sortByDesc('total_amount')->take(5)->values()->toArray(),
                // Agent-wise statistics (by sender_agent_id)
                'agent_stats' => $allTransactions->groupBy('sender_agent_id')
                    ->map(function($transactions) {
                        return [
                            'name' => $transactions->first()->senderAgent->name ?? 'Unknown',
                            'total_amount' => $transactions->sum('amount'),
                            'total_transactions' => $transactions->count(),
                            'total_admin_fee' => $transactions->sum('admin_fee')
                        ];
                    })->values()->toArray(),
                // Top 5 agents by amount (by sender_agent_id)
                'top_agents' => $allTransactions->groupBy('sender_agent_id')
                    ->map(function($transactions) {
                        return [
                            'name' => $transactions->first()->senderAgent->name ?? 'Unknown',
                            'total_amount' => $transactions->sum('amount'),
                            'total_transactions' => $transactions->count(),
                            'total_admin_fee' => $transactions->sum('admin_fee')
                        ];
                    })->sortByDesc('total_amount')->take(5)->values()->toArray(),
            ]
        ];

        // Get daily data for chart
        $dateRange = collect();
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateRange->push($currentDate->copy());
            $currentDate->addDay();
        }
        foreach ($dateRange as $date) {
            $stats['sendRegion']['chart_data']['dates'][] = $date->format('Y-m-d');
            // Get sent amount for this date (by sender_region_id)
            $sentAmount = SysTransaction::whereDate('created_at', $date)
                ->sum('amount');
            $stats['sendRegion']['chart_data']['sent'][] = $sentAmount;
            // Get received amount for this date (by sender_region_id)
            $receivedAmount = SysTransaction::whereDate('created_at', $date)
                ->sum('amount');
            $stats['sendRegion']['chart_data']['received'][] = $receivedAmount;
        }

        return view('admin.index', compact('stats'));
    }

    public function AdminReports(Request $request)
    {
        // Get date range from request or default to today
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        // Get all transactions within date range
        $transactions = SysTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['senderCustomer', 'receiverAgent', 'region', 'senderAgent'])
            ->get();

        // Get state-wise statistics
        $stateStats = $transactions->groupBy('region_id')
            ->map(function($transactions) {
                return [
                    'name' => $transactions->first()->region->name ?? 'Unknown',
                    'total_amount' => $transactions->sum('amount'),
                    'total_transactions' => $transactions->count(),
                    'total_admin_fee' => $transactions->sum('admin_fee')
                ];
            });

        // Get agent-wise statistics
        $agentStats = $transactions->groupBy('sender_agent_id')
            ->map(function($transactions) {
                return [
                    'name' => $transactions->first()->senderAgent->name ?? 'Unknown',
                    'total_amount' => $transactions->sum('amount'),
                    'total_transactions' => $transactions->count(),
                    'total_admin_fee' => $transactions->sum('admin_fee')
                ];
            });

        return view('admin.reports', compact('transactions', 'stateStats', 'agentStats', 'startDate', 'endDate'));
    }

    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Admin Logout Successfully',
            'alert-type' => 'success'
        );

        return redirect('/login')->with($notification);
    } // End Method

    public function AdminLogin()
    {
        return view('admin.admin_login');
    }

    public function AdminProfile()
    {
        $id = Auth::user()->id; //collect user data from database
        $profileData = User::find($id); //Laravel Eloquent
        return view('admin.admin_profile_view', compact('profileData'));
    } //End Method

    public function AdminProfileStore(Request $request)
    {
        $id = Auth::user()->id; //collect user data from database
        $data = User::find($id); //Laravel Eloquent
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/' . $data->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function AdminChangePassword()
    {

        $id = Auth::user()->id; //collect user data from database
        $profileData = User::find($id); //Laravel Eloquent
        return view('admin.admin_change_password', compact('profileData'));
    }

    public function AdminUpdatePassword(Request $request)
    {

        //Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        //Match The old password
        if (!Hash::check($request->old_password, auth::user()->password)) {

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
        $allagent = User::where('role', 'agent')->get();
        return view('backend.agentuser.all_agent', compact('allagent'));
    }

    public function AddAgent()
    {
        return view('backend.agentuser.add_agent');
    }

    public function StoreAgent(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'password' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'commission_rate' => 'nullable|numeric|min:0',
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
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $save_url,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'role' => 'agent',
            'commission_rate' => $request->commission_rate,
        ]);
        // Prepare a success notification
        $notification = array(
            'message' => 'Agent Successfully Created',
            'alert-type' => 'success'
        );
        // Redirect to the agents list page with notification
        return redirect()->route('all.superagent')->with($notification);
    }

    public function EditAgent($id)
    {
        $allagent = User::findOrFail($id);
        return view('backend.agentuser.edit_agent', compact('allagent'));
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
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->commission_rate = $request->commission_rate;
        $user->updated_at = Carbon::now();

        // Save the changes to the database
        $user->save();

        $notification = [
            'message' => 'Agent Profile Updated Successfully',
            'alert-type' => 'success'
        ];


        return redirect()->route('all.superagent')->with($notification);
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



    /////////// Admin User All Method ////////////
    public function AllAdmin()
    {
        $alladmin = User::where('role', 'admin')->get();
        return view('backend.pages.admin.all_admin', compact('alladmin'));
    } // End Method

    public function AddAdmin()
    {
        $roles = Role::all();
        return view('backend.pages.admin.add_admin', compact('roles'));
    }

    public function StoreAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
        ]);

        $user = new User();  //new object for user table
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password =  Hash::make($request->password);
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();

        // if ($request->roles) {
        //     $user->assignRole($request->roles);   //assignRole default spaite function, Assign roles to user
        // }

        if ($request->roles) {
            // Fetch the role(s) by their IDs and assign them to the user
            $roles = Role::whereIn('id', (array) $request->roles)->pluck('name');
            $user->assignRole($roles); //assignRole default spaite function, Assign roles to user
        }

        $notification = array(
            'message' => 'New Admin Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification);
    }

    public function EditAdmin($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('backend.pages.admin.edit_admin', compact('user', 'roles'));
    }

    public function UpdateAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();

        $user->roles()->detach(); //spaite default function , Detach all roles from user
        if ($request->roles) {
            $roles = Role::whereIn('id', (array) $request->roles)->pluck('name');
            $user->assignRole($roles);
        }

        $notification = array(
            'message' => 'New Admin Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification);
    } // End Method

    public function DeleteAdmin($id){

        $user = User::findOrFail($id);
        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
                'message' => 'Admin Deleted Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);

      }// End Method

}
