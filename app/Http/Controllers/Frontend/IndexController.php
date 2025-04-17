<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\MultiImage;
use App\Models\Property;
use App\Models\PropertyMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PropertyType;
use App\Models\Schedule;
use App\Models\State;

class IndexController extends Controller
{
    public function PropertyDetails($slug)
    {
        $property = Property::where('property_slug', $slug)->firstOrFail();
        $multiImage = MultiImage::where('property_id', $property->id)->get();
        $facility = Facility::where('property_id', $property->id)->get();

        $amenities = $property->amenities_id;
        $property_amen = explode(',', $amenities);

        $property_type = $property->ptype_id;
        $relatedProperty = Property::where('ptype_id', $property_type)
            ->where('id', '!=', $property->id)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        return view('frontend.property.property_details', compact('property', 'multiImage', 'property_amen', 'facility', 'relatedProperty'));
    }


    public function PropertyMessage(Request $request)
    {

        if (Auth::check()) {

            PropertyMessage::insert([

                'user_id' => Auth::user()->id,
                'agent_id' => $request->agent_id,
                'property_id' => $request->property_id,
                'msg_name' => $request->msg_name,
                'msg_email' => $request->msg_email,
                'msg_phone' => $request->msg_phone,
                'message' => $request->message,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' => 'Send Message Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Plz Login Your Account First',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    } // End Method

    public function AgentDetails($id)
    {

        $agent = User::findOrFail($id);
        $property = Property::where('agent_id', $id)->get();
        $featured = Property::where('featured', '1')->orderBy('id', 'DESC')->limit(3)->get();
        $rentproperty = Property::where('property_status', 'rent')->get();
        $buyproperty = Property::where('property_status', 'buy')->get();

        return view('frontend.agent.agent_details', compact('agent', 'property', 'featured', 'rentproperty', 'buyproperty'));
    } // End Method

    public function AgentDetailsMessage(Request $request)
    {

        $aid = $request->agent_id;

        if (Auth::check()) {

            PropertyMessage::insert([

                'user_id' => Auth::user()->id,
                'agent_id' => $aid,
                'msg_name' => $request->msg_name,
                'msg_email' => $request->msg_email,
                'msg_phone' => $request->msg_phone,
                'message' => $request->message,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' => 'Send Message Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
        } else {

            $notification = array(
                'message' => 'Plz Login Your Account First',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    } // End Method

    public function RentProperty()
    {
        $property = Property::where('status', '1')->where('property_status', 'rent')->paginate(3);
        $rentproperty = Property::where('property_status', 'rent')->get();
        $buyproperty = Property::where('property_status', 'buy')->get();

        return view('frontend.property.rent_property', compact('property', 'rentproperty', 'buyproperty'));
    } //End Method

    public function BuyProperty()
    {

        $property = Property::where('status', '1')->where('property_status', 'buy')->paginate(3);
        $rentproperty = Property::where('property_status', 'rent')->get();
        $buyproperty = Property::where('property_status', 'buy')->get();

        return view('frontend.property.buy_property', compact('property', 'rentproperty', 'buyproperty'));
    } //End Method

    public function PropertyType($id)
    {
        $property = Property::where('status', '1')->where('ptype_id', $id)->get();
        $rentproperty = property::where('property_status', 'rent')->get();
        $buyproperty = property::where('property_status', 'buy')->get();
        $pbread = PropertyType::where('id', $id)->first();
        return view('frontend.property.property_type', compact('property', 'rentproperty', 'buyproperty', 'pbread'));
    } //End Method

    public function StateDetails($id)
    {

        $property = Property::where('status', '1')->where('state', $id)->get();

        $bstate = State::where('id', $id)->first();
        return view('frontend.property.state_property', compact('property', 'bstate'));
    } // End Method

    public function BuyPropertySearch(Request $request)
{
    $request->validate(['search' => 'required']);

    $item = $request->search;
    $sstate = $request->state;
    $stype = $request->ptype_id;

    $property = Property::where('status', '1')
        ->where('property_name', 'like', '%' . $item . '%')
        ->where('property_status', 'buy')
        ->with('pstate', 'type');

    if (!empty($sstate)) {
        $property->whereHas('pstate', function ($q) use ($sstate) {
            $q->where('state_name', 'like', '%' . $sstate . '%');
        });
    }

    if (!empty($stype)) {
        $property->whereHas('type', function ($q) use ($stype) {
            $q->where('type_name', 'like', '%' . $stype . '%');
        });
    }

    $property = $property->get();

    $rentproperty = Property::where('property_status', 'rent')->get();
    $buyproperty = Property::where('property_status', 'buy')->get();

    return view('frontend.property.property_search', compact('property', 'rentproperty', 'buyproperty'));
}


    public function RentPropertySearch(Request $request)
    {
        $request->validate(['search' => 'required']);
        $item = $request->search;
        $sstate = $request->state;
        $stype = $request->ptype_id;

        $property = Property::where('status', '1')->where('property_name', 'like', '%' . $item . '%')
            ->where('property_status', 'rent')
            ->with('pstate', 'type')
            ->whereHas('pstate', function ($q) use ($sstate) {
                $q->where('state_name', 'like', '%' . $sstate . '%');
            })
            ->whereHas('type', function ($q) use ($stype) {
                $q->where('type_name', 'like', '%' . $stype . '%');
            })
            ->get();

        $rentproperty = property::where('property_status', 'rent')->get();
        $buyproperty = property::where('property_status', 'buy')->get();

        return view('frontend.property.property_search', compact('property', 'rentproperty', 'buyproperty'));
    }

    public function AllPropertySearch(Request $request)
    {
        $property_status = $request->property_status;
        $stype = $request->ptype_id;
        $sstate = $request->state;
        $bedrooms = $request->bedrooms;
        $bathrooms = $request->bathrooms;

        $property = Property::where('status', '1')->where('property_status', $property_status)->where('bedrooms', $bedrooms)->where('bathrooms', $bathrooms)
            ->with('type', 'pstate')
            ->whereHas('type', function ($q) use ($stype) {
                $q->where('type_name', 'like', '%' . $stype . '%');
            })
            ->whereHas('pstate', function ($q) use ($sstate) {
                $q->where('state_name', 'like', '%' . $sstate . '%');
            })->get();

        $rentproperty = property::where('property_status', 'rent')->get();
        $buyproperty = property::where('property_status', 'buy')->get();

        return view('frontend.property.property_search', compact('property', 'rentproperty', 'buyproperty'));
    }

    public function StoreSchedule(Request $request)
    {

        $pid = $request->property_id;
        $aid = $request->agent_id;

        if (Auth::check()) {

            Schedule::insert([

                'user_id' => Auth::user()->id,
                'property_id' => $pid,
                'agent_id' => $aid,
                'tour_date' => $request->tour_date,
                'tour_time' => $request->tour_time,
                'message' => $request->message,
                'created_at' =>  Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Send Request Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(

                'message' => 'Please Login Your Account First',
                'alert-type' => 'error'

            );
            return redirect()->back()->with($notification);
        }
    }

    public function AllProperty()
    {
        $property = Property::where('status', '1')->paginate(4);
        $rentproperty = Property::where('property_status', 'rent')->get();
        $buyproperty = Property::where('property_status', 'buy')->get();

        return view('frontend.property.all_property', compact('property', 'rentproperty', 'buyproperty'));
    }

    public function AllCategory()
    {
        $categories = PropertyType::all();
        $property = Property::where('status', '1')->where('featured', '1')->latest()->limit(3)->get();
        // dd($property);
        return view('frontend.all_category', compact('categories', 'property'));
    }
}
