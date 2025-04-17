<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:user');
    }
    public function AddToWishList(Request $request, $property_id)
    {
        if (Auth::check()) {
            $exists = Wishlist::where('user_id', Auth::id())->where('property_id', $property_id)->first();
            $user_id = Auth::id(); // Get the authenticated user's ID

            if (!$exists) {
                Wishlist::insert([
                    'user_id' => $user_id,
                    'property_id' => $property_id,
                    'created_at' => Carbon::now()
                ]);

                return response()->json(['success' => 'Successfully Added To Your Wishlist']);
            } else {
                return response()->json(['error' => 'Property Already In Wishlist']);
            }
        } else {
            return response()->json(['error' => 'Please Login First']);
        }
    }

    public function UserWishlist()
    {

        if (Auth::check()) {
            $id = Auth::user()->id;
            $userData = User::find($id);

            return view('frontend.dashboard.wishlist', compact('userData'));
        } else {
            return redirect()->route('login'); // Redirect to login page
        }
    } // End Method


    public function GetWishlistProperty(){

       $wishlist = Wishlist::with('property')->where('user_id',Auth::id())->latest()->get();
       $wishQty = wishlist::count();

       return response()->json(['wishlist' => $wishlist, 'wishQty' => $wishQty]);

    } //End Method


    public function WishlistRemove($id){

        $auth = Auth::id();
        Wishlist::where('user_id',$auth)->where('id',$id)->delete();
        return response()->json(['success' => 'Successfully Property Remove']);
    }//End Method



}
