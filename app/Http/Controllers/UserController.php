<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     public function Index(){
         return view('agentuser.index');
     }

     public function userDashboard(){
        return view('agentuser.index');
    }

    public function UserProfile(){
        $id = Auth::user()->id; //collect user data from database
        $profileData = User::find($id); //Laravel Eloquent
        return view('agent.agent_profile_view',compact('profileData'));
    }

    public function UserProfileStore(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/user_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();
        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function UserLogout (Request $request){
        // Auth::logout();
        // return redirect('/');
        //laravel default and its find on Aut->AuthenticatedSessionController.php
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'User Logout Successfully',
            'alert-type' => 'success'
        );

           return redirect()->route('login')->with($notification);

    }

    public function UserChangePassword(){
        $id = Auth::user()->id; //collect user data from database
        $profileData = User::find($id); //Laravel Eloquent
        return view('agent.agent_change_password',compact('profileData'));
    }

    public function UserPasswordUpdate(Request $request) {
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
            'message' => 'Password change successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }


    //User Schedule Request
    // public function UserScheduleRequest(){
    //     $id = Auth::user()->id;

    //     $srequest = Schedule::where('user_id',$id)->get();
    //     return view('frontend.dashboard.schedule_request', compact('srequest'));
    // }
}
