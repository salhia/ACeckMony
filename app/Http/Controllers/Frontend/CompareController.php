<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Compare;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CompareController extends Controller
{
    public function AddToCompare(Request $request, $property_id){

        if(Auth::check()){

            $authUser = Auth::id();
            $compareExists = Compare::where('user_id',$authUser)->where('property_id',$property_id)->first();

            if(!$compareExists){

                Compare::insert([

                    'user_id' => $authUser,
                    'property_id' => $property_id,
                    'created_at' => Carbon::now()

                ]);

                return response()->json(['success' => 'Successfully Added On Your Compare']);

            }else{

                return response()->json(['error' => 'This Property Has Already in your CompareList']);
            }

        } else{

            return response()->json(['error' => 'At First Login Your Account']);

        }

    } //End Method

    public function UserCompare(){

        return view('frontend.dashboard.compare');

    } //End Method

    public function GetCompareProperty(){

        $auth_id = Auth::user()->id;
        $compare = Compare::with('property')->where('user_id',$auth_id)->latest()->get();

        return response()->json($compare);

    } //End Method


    public function CompareRemove($id){

        $auth_id = Auth::user()->id;
        Compare::where('user_id',$auth_id)->where('id',$id)->delete();
        return response()->json(['success' => 'Successfully Property Remove']);
    }


}
