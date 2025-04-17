<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Mail\ScheduleMail;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\Amenities;
use App\Models\PackagePlan;
use App\Models\PropertyMessage;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\User;
use App\Models\Schedule;
use Intervention\Image\Facades\Image;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class AgentPropertyController extends Controller
{
    public function AgentAllProperty()
    {

        $id = Auth::user()->id;
        $property = Property::where('agent_id', $id)->latest()->get();
        return view('agent.property.all_property', compact('property'));
    } // End Method

    public function AgentAddProperty()
    {

        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();

        $id = Auth::User()->id;
        $property = User::where('id', $id)->where('role', 'agent')->first();
        $pcount = $property->credit;
        $pstate = State::latest()->get();
        //    dd($pcount);

        if ($pcount == 1) {
            $notification = array(
                'message' => 'Buy a package first',
                'alert-type' => 'error'
            );
            return redirect()->route('buy.package')->with($notification);
        } else {
            return view('agent.property.add_property', compact('propertytype', 'amenities', 'pstate'));
        }
    }

    public function AgentStoreProperty(Request $request)
    {

        $id = Auth::User()->id;
        $uid = User::findOrFail($id);
        $nid = $uid->credit;

        $amen = $request->amenities_id; //amenities_id column of Properties DB
        $amenities = implode(",", $amen);

        $pcode = IdGenerator::generate(['table' => 'properties', 'field' => 'property_code', 'length' => 5, 'prefix' => 'PC']);

        if ($request->file('property_thambnail')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('property_thambnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('property_thambnail'));
            $image = $image->resize(370, 250);
            $image->toJpeg(80)->Save(base_path(('public/upload/property/thambnail/' . $name_gen)));
            $save_url = 'upload/property/thambnail/' . $name_gen;
        }

        $property_id = Property::insertGetId([
            'ptype_id' => $request->ptype_id,
            'amenities_id' => $amenities,
            'property_name' => $request->property_name,
            'property_slug' => strtolower(str_replace('', '-', $request->property_name)),
            'property_code' => $pcode,   //variable declare on up
            'property_status' => $request->property_status,
            'lowest_price' => $request->lowest_price,
            'max_price' => $request->max_price,
            'short_descp' => $request->short_descp,
            'long_descp' => $request->long_descp,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'garage' => $request->garage,
            'garage_size' => $request->garage_size,


            'property_size' => $request->property_size,
            'property_video' => $request->property_video,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,


            'neighborhood' => $request->neighborhood,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'featured' => $request->featured,
            'hot' => $request->hot,

            'agent_id' => Auth::user()->id, //agent_id will be stored which agent login currently
            'status' => 1,
            'property_thambnail' => $save_url, //variable declare on up
            'created_at' => Carbon::now(), //Present date should be inserted
        ]);

        // Multiple Image Upload from here
        $images = $request->file('multi_img');

        foreach ($images as $img) {
            $manager = new ImageManager(new Driver());
            $make_name = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();
            $image = $manager->read($img);
            $image = $image->resize(370, 250);
            $image->toJpeg(80)->Save(base_path(('public/upload/property/multi-image/' . $make_name)));
            $uploadPath = 'upload/property/multi-image/' . $make_name;

            MultiImage::insert([
                'property_id' => $property_id,
                'photo_name' => $uploadPath,
                'created_at' => Carbon::now(),

            ]);
        } //end foreach

        // Facilities Add From Here
        $facilities = Count($request->facility_name);
        if ($facilities != NULL) {
            for ($i = 0; $i < $facilities; $i++) {
                $fcount = new Facility();       //Another method to declare object
                $fcount->property_id = $property_id;    //variable with database field name = declare $property_id on up
                $fcount->facility_name = $request->facility_name[$i];
                $fcount->distance = $request->distance[$i]; //variable with database field name = name from on add_property page
                $fcount->save();
            }
        }
        // Facilities Ends From Here

        User::where('id', $id)->update([
            // 'credit' => DB::raw('1 + ' . $nid),
            'credit' => DB::raw($nid . '-1'),
        ]);

        $notification = array(
            'message' => 'Property Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('agent.all.property')->with($notification);
    }

    public function AgentEditProperty($id)
    {

        $property = Property::findOrFail($id);

        $type = $property->amenities_id;
        $property_ami = explode(',', $type);
        $pstate = State::latest()->get();

        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();

        $multiImage = MultiImage::where('property_id', $id)->get();

        $facilities = Facility::where('property_id', $id)->get();

        return view('agent.property.edit_property', compact('property', 'propertytype', 'amenities', 'property_ami', 'multiImage', 'facilities', 'pstate'));
    }

    public function AgentUpdateProperty(Request $request)
    {
        $property_id = $request->id;
        $amen = $request->amenities_id;
        $amenites = implode(",", $amen);

        Property::findOrFail($property_id)->Update([
            'ptype_id' => $request->ptype_id,
            'amenities_id' => $amenites,
            'property_name' => $request->property_name,
            'property_slug' => strtolower(str_replace(' ', '-', $request->property_name)),
            'property_status' => $request->property_status,

            'lowest_price' => $request->lowest_price,
            'max_price' => $request->max_price,
            'short_descp' => $request->short_descp,
            'long_descp' => $request->long_descp,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'garage' => $request->garage,
            'garage_size' => $request->garage_size,

            'property_size' => $request->property_size,
            'property_video' => $request->property_video,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,

            'neighborhood' => $request->neighborhood,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'featured' => $request->featured,
            'hot' => $request->hot,
            'agent_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),

        ]);

        $notification = array(
            'message' => 'Property Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('agent.all.property')->with($notification);
    }

    public function AgentUpdatePropertyThambnail(Request $request)
    {
        $pro_id = $request->id;
        $oldImage = $request->old_img;

        if ($request->file('property_thambnail')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('property_thambnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('property_thambnail'));
            $image = $image->resize(370, 250);
            $image->toJpeg(80)->Save(base_path(('public/upload/property/thambnail/' . $name_gen)));
            $save_url = 'upload/property/thambnail/' . $name_gen;
        }

        if (file_exists($oldImage)) {
            unlink($oldImage);
        }

        Property::findOrFail($pro_id)->update([
            'property_thambnail' => $save_url,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Property Image Thambnail Updated Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }
    public function AgentUpdatePropertyMultiimage(Request $request)
    {

        $imgs = $request->multi_img;

        foreach ($imgs as $id => $img) {    //$id from edit blade   name="multi_img[{{ $img->id }}]"

            $imgDel = MultiImage::findOrFail($id);
            unlink($imgDel->photo_name);

            $manager = new ImageManager(new Driver());
            $make_name = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();
            $image = $manager->read($img);
            $image = $image->resize(370, 250);
            $image->toJpeg(80)->Save(base_path(('public/upload/property/multi-image/' . $make_name)));
            $uploadPath = 'upload/property/multi-image/' . $make_name;

            MultiImage::where('id', $id)->update([ //where('MultiImage DB table id', Request blade file $id)

                'photo_name' => $uploadPath,        //MultiImage DB table column name => variable of photo path
                'updated_at' => Carbon::now(),
            ]);
        }

        $notification = array(
            'message' => 'Property Multi Image Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    public function AgentPropertyMultiimgDelete($id)
    {

        $oldImg = MultiImage::findOrFail($id);
        unlink($oldImg->photo_name);

        MultiImage::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Property Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
    public function AgentStoreNewMultiimage(Request $request)
    {

        $new_multi = $request->imageid;

        $img = $request->file('multi_img');

        $manager = new ImageManager(new Driver());
        $make_name = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();
        $image = $manager->read($img);
        $image = $image->resize(370, 250);
        $image->toJpeg(80)->Save(base_path(('public/upload/property/multi-image/' . $make_name)));
        $uploadPath = 'upload/property/multi-image/' . $make_name;

        MultiImage::insert([
            'property_id' => $new_multi,
            'photo_name' => $uploadPath,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Property Multi Image Added Successfully',
            'alert-type' => 'success',
        );

        return back()->with($notification);
    }

    public function AgentUpdatePropertyFacilities(Request $request)
    {

        $pid = $request->id;
        $facility = $request->facility_name;

        if ($facility == NULL) {

            $notification = array(
                'message' => 'Property Facility not updated',
                'alert-type' => 'warning'
            );
            return back()->with($notification);
        } else {

            Facility::where('property_id', $pid)->delete();

            $facilities = Count($facility);

            for ($i = 0; $i < $facilities; $i++) {
                $fcount = new Facility();
                $fcount->property_id = $pid;
                $fcount->facility_name = $facility[$i];
                $fcount->distance = $request->distance[$i];
                $fcount->save();
            }


            $notification = array(
                'message' => 'Property Facility Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
        }
    }

    public function AgentDetailsProperty($id)
    {

        $facilities = Facility::where('property_id', $id)->get();
        $property = Property::findOrFail($id);

        $type = $property->amenities_id;
        $property_ami = explode(',', $type);

        $multiImage = MultiImage::where('property_id', $id)->get();

        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();


        return view('agent.property.details_property', compact('property', 'propertytype', 'amenities', 'property_ami', 'multiImage', 'facilities'));
    }

    public function AgentDeleteProperty($id)
    {

        $property = Property::findOrFail($id);
        unlink($property->property_thambnail);

        Property::findOrFail($id)->delete();


        $Image = MultiImage::where('property_id', $id)->get();
        foreach ($Image as $img) {
            unlink($img->photo_name);
            MultiImage::where('property_id', $id)->delete();
        }


        $facilitiesData = Facility::where('property_id', $id)->get();
        foreach ($facilitiesData as $item) {
            Facility::where('property_id', $id)->delete();
        }


        $notification = array(
            'message' => 'Property Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function BuyPackage()
    {
        return view('agent.package.buy_package');
    }

    public function BuyBusinessPlan()
    {

        $id = Auth::User()->id;
        $user = User::find($id);
        return view('agent.package.business_plan', compact('user'));
    }

    public function StoreBusinessPlan(Request $request)
    {

        $id = Auth::User()->id;
        $uid = User::findOrFail($id);
        $nid = $uid->credit;

        PackagePlan::insert([
            'user_id' => $id,
            'package_name' => 'Business',
            'package_credits' => '3',
            'invoice' => 'RS' . mt_rand(10000000, 99999999),
            'package_amount' => '20',
            'created_at' => Carbon::now(),

        ]);


        User::where('id', $id)->update([
            'credit' => DB::raw('3+ ' . $nid),
        ]);


        $notification = array(
            'message' => 'You have purchase Basic Package Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('agent.all.property')->with($notification);
    }

    public function BuyProfessionalPlan()
    {

        $id = Auth::User()->id;
        $data = User::find($id);

        return view('agent.package.professional_plan', compact('data'));
    }

    public function StoreProfessionalPlan(Request $request)
    {

        $id = Auth::user()->id;
        $uid = User::findOrFail($id);
        $nid = $uid->credit;

        PackagePlan::insert([

            'user_id' => $id,
            'package_name' => 'Professional',
            'package_credits' => '10',
            'invoice' => 'RS' . mt_rand(10000000, 99999999),
            'package_amount' => '50',
            'created_at' => Carbon::now(),
        ]);

        User::where('id', $id)->update([
            'credit' => DB::raw('10 + ' . $nid),
        ]);

        $notification = array(
            'message' => 'You have purchase Professional Package Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('agent.all.property')->with($notification);
    }

    public function PackageHistory()
    {

        $id = Auth::user()->id;
        $packagehistory = PackagePlan::where('user_id', $id)->get();
        // dd($packagehistory);

        return view('agent.package.package_history', compact('packagehistory'));
    }

    public function AgentPackageInvoice($id)
    {

        $packagehistory = PackagePlan::where('id', $id)->first();

        $pdf = Pdf::loadView('agent.package.package_history_invoice', compact('packagehistory'))->setPaper('a4')->setOption([

            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->download('invoice.pdf');
    }

    public function AgentPropertyMessage()
    {
        $id = Auth::user()->id;
        $usermsg = PropertyMessage::where('agent_id', $id)->whereNotNull('property_id')->latest()->get();
        return view('agent.message.all_message', compact('usermsg'));
    }

    public function AgentMessageDetails($id)
    {
        $authid = Auth::user()->id;
        $usermsg = PropertyMessage::where('user_id', $authid)->get();
        $msgdetails = PropertyMessage::findOrFail($id);

        return view('agent.message.message_details', compact('usermsg', 'msgdetails'));
    }

    public function AgentProfileMessage()
    {
        $id = Auth::user()->id;

        // Fetch messages where 'agent_id' matches the logged-in user's ID and 'property_id' is NULL
        $promsg = PropertyMessage::where('agent_id', $id)
            ->whereNull('property_id')
            ->get();

        return view('agent.message.all_message', compact('promsg'));
    }

    public function AgentProfileMessageDetails($id)
    {
        $authid = Auth::user()->id;
        $promsg = PropertyMessage::where('user_id', $authid)->whereNull('property_id')->get();
        $promsgdetails = PropertyMessage::findOrFail($id);

        return view('agent.message.profile_message_details', compact('promsg', 'promsgdetails'));
    }

    //Agent Schedule Request
    public function AgentScheduleRequest()
    {
        $id = Auth::user()->id;
        $usermsg = Schedule::where('agent_id', $id)->get();
        return view('agent.schedule.schedule_request', compact('usermsg'));
    }

    public function AgentDetailsSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        // dd(vars: $schedule);
        return view('agent.schedule.schedule_details', data: compact('schedule'));
    }

    public function AgentUpdateSchedule(Request $request)
    {
        $sid = $request->id;
        $schedule = Schedule::findOrFail($sid);

        $schedule->update([
            'status' => '1',
        ]);

        //Start Email

            $data = [
                'tour_date' => $schedule->tour_date,
                'tour_time' => $schedule->tour_time,
            ];

            $user_email = $request->email;
            Mail::to($user_email)->send(new ScheduleMail($data));

        //End Email

        $notification = array(
            'message' => 'You have Confirm Schedule Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('agent.schedule.request')->with($notification);
    }
}
