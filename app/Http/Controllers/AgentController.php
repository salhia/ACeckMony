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

class AgentController extends Controller
{
    public function AgentDashboard(){
        return view('agent.index');
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

        return redirect('/')->with($notification);
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
    return view('agent.agentuser.add_agent', compact('regions', 'agents'));
    }

    public function StoreAgent(Request $request)
    {
        // Validate the incoming request
       $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email,' . ($request->id ?? 'NULL'),
    'phone' => 'required|string|max:15',
    'address' => 'required|string|max:255',
    'region_id' => 'required|integer',
    'commissionRate' => 'nullable|numeric|min:0',
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
    'email' => $request->email,
    'phone' => $request->phone,
    'address' => $request->address,
    'photo' => $save_url,
    'password' => Hash::make($request->password),
    'status' => 'active',
    'role' => 'user',
    'region_id' => $request->region_id,
    'commissionRate' => $request->commissionRate ?? 0,
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
     $user->email = $request->email;
     $user->phone = $request->phone;
     $user->address = $request->address;
     $user->region_id = $request->region_id;
     $user->commissionRate = $request->commissionRate ?? 0;
     $user->transfer_limit = $request->transfer_limit ?? 0;
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




}

