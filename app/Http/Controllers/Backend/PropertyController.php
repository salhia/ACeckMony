<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use App\Models\MultiImage;
use PHPUnit\Framework\Constraint\Count;
use App\Models\PropertyMessage;
use App\Models\State;


use function PHPUnit\Framework\fileExists;

class PropertyController extends Controller
{
    public function AdminAllProperty()
    {
        $property = Property::latest()->get();
        return view('backend.property.all_property', compact('property'));
    }

    public function AddProperty()
    {
        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        $pstate = State::latest()->get();
        $activeAgent = User::where('role', 'agent')->where('status', 'active')->latest()->get();
        return view('backend.property.add_property', compact('propertytype', 'amenities', 'activeAgent', 'pstate'));
    }

    public function StoreProperty(Request $request)
    {

        $request->validate([
            'property_name' => 'required|string|max:255|unique:properties,property_name',
            'property_status' => 'required|in:rent,buy',
            'lowest_price' => 'required|string|min:0',
            'property_thambnail' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address' => 'required|string|max:255',
            'state' => 'required|exists:states,id',
            'property_size' => 'required|string|min:0',
            'ptype_id' => 'required|exists:property_types,id',
            'short_descp' => 'required|string',
            'amenities_id' => 'required|array', // This ensures the field is required and is an array
            'amenities_id.*' => 'exists:amenities,amenitis_name', // This checks that each selected amenity exists in the `amenities` tabl
        ]);


        $amen = $request->amenities_id;
        $amenities = implode(",", $amen); //implode works on making single data to string data ("4,5,6,7 no are amenities_id", $amen)
        // dd($amenities); for show draft amenities_id on view page

        $pcode = IdGenerator::generate(['table' => 'properties', 'field' => 'property_code', 'length' => 5, 'prefix' => 'PC']);
        //composer require haruncpi/laravel-id-generator (need to install for this IdGenerator)
        //use Haruncpi\LaravelIdGenerator\IdGenerator;

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

            'agent_id' => $request->agent_id,
            'status' => 1,
            'property_thambnail' => $save_url, //variable declare on up
            'created_at' => Carbon::now(), //Present date should be inserted
        ]);

        // Multiple Image Upload from here
        $images = $request->file('multi_img');

        if ($images) { // Check if files are uploaded
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
            }
        } // end if

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
        $notification = array(
            'message' => 'Property Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('admin.all.property')->with($notification);
    }

    public function EditProperty($id)
    {
        $facilities = Facility::where('property_id', $id)->get();
        $property = Property::findOrFail($id);

        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        $pstate = State::latest()->get();
        $activeAgent = User::where('status', 'active')->where('role', 'agent')->latest()->get();

        $type = $property->amenities_id;
        $property_ami = explode(',', $type);

        $multiImage = MultiImage::where('property_id', $id)->get();

        return view('backend.property.edit_property', compact('property', 'propertytype', 'amenities', 'activeAgent', 'property_ami', 'multiImage', 'facilities', 'pstate'));
    }


    public function UpdateProperty(Request $request)
    {

        $amen = $request->amenities_id;
        $amenites = implode(",", $amen);

        $property_id = $request->id;

        Property::findOrFail($property_id)->update([

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
            'agent_id' => $request->agent_id,
            'updated_at' => Carbon::now(),

        ]);

        $notification = array(
            'message' => 'Property Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.all.property')->with($notification);
    }

    public function UpdatePropertyThambnail(Request $request)
    {

        $pro_id = $request->id;
        $oldImage = $request->old_img;

        $manager = new ImageManager(new Driver());
        $name_gen = hexdec(uniqid()) . '.' . $request->file('property_thambnail')->getClientOriginalExtension();
        $image = $manager->read($request->file('property_thambnail'));
        $image = $image->resize(370, 250);
        $image->toJpeg(80)->Save(base_path(('public/upload/property/thambnail/' . $name_gen)));
        $save_url = 'upload/property/thambnail/' . $name_gen;


        if (fileExists($oldImage)) {
            unlink($oldImage);

            Property::findOrFail($pro_id)->update([

                'property_thambnail' => $save_url,       //Property DB table column name => variable of photo path
                'updated_at' => Carbon::now(),
            ]);
        } else {

            Property::findOrFail($pro_id)->update([

                'property_thambnail' => $save_url,     //Property DB table column name => variable of photo path
                'updated_at' => Carbon::now(),
            ]);
        }

        $notification = array(
            'message' => 'Property Image Thambnail Updated Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function UpdatePropertyMultiimage(Request $request)
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


    public function PropertyMultiImageDelete($id)
    {

        $oldImage = MultiImage::findOrFail($id);
        unlink($oldImage->photo_name);
        MultiImage::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Property Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function StoreNewMultiimage(Request $request)
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

    public function UpdatePropertyFacilities(Request $request)
    {

        $pid = $request->id;

        if ($request->facility_name == NULL) {

            $notification = array(
                'message' => 'Property Facility not updated',
                'alert-type' => 'warning'
            );
            return back()->with($notification);
        } else {
            Facility::where('property_id', $pid)->delete();

            $facilities = Count($request->facility_name);

            for ($i = 0; $i < $facilities; $i++) {
                $fcount = new Facility();
                $fcount->property_id = $pid;
                $fcount->facility_name = $request->facility_name[$i];
                $fcount->distance = $request->distance[$i];
                $fcount->save();
            } // end for

            $notification = array(
                'message' => 'Property Facility Updated Successfully',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }
    } // End Method

    public function DeleteProperty($id)
    {

        $property = Property::findOrFail($id);
        unlink($property->property_thambnail);

        Property::findOrFail($id)->delete();

        $image = MultiImage::where('property_id', $id)->get();
        foreach ($image as $img) {
            unlink($img->photo_name);
            MultiImage::where('property_id', $id)->delete();
        }

        $facilitiesData = Facility::where('property_id', $id)->get();
        foreach ($facilitiesData as $item) {
            $item->facility_name;
            Facility::where('property_id', $id)->delete();
        }

        $notification = array(
            'message' => 'Property Facility Updated Successfully',
            'alert-type' => 'success'
        );

        return back()->with('$notification');
    }

    public function DetailsProperty($id)
    {

        $facilities = Facility::where('property_id', $id)->get();
        $property = Property::findOrFail($id);

        $type = $property->amenities_id;
        $property_ami = explode(',', $type);

        $multiImage = MultiImage::where('property_id', $id)->get();

        $propertytype = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        $activeAgent = User::where('status', 'active')->where('role', 'agent')->latest()->get();

        return view('backend.property.details_property', compact('property', 'propertytype', 'amenities', 'activeAgent', 'property_ami', 'multiImage', 'facilities'));
    }

    public function InactiveProperty(Request $request)
    {

        $pid = $request->id;
        Property::findOrFail($pid)->Update([
            'status' => 0,
        ]);

        $notification = array(
            'message' => 'Property Inactive Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.property')->with('$notification');
    } // End Method

    public function ActiveProperty(Request $request)
    {

        $pid = $request->id;
        Property::findOrFail($pid)->Update([
            'status' => 1,
        ]);

        $notification = array(
            'message' => 'Property Active Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.property')->with('$notification');
    } // End Method

    public function changePropertyStatus(Request $request)
    {

        $pro = Property::find($request->user_id);
        $pro->status = $request->status;
        $pro->save();

        return response()->json(['success' => 'Status Change Successfully']);
    } // End Method

    public function AdminPropertyMessage()
    {

        $usermsg = PropertyMessage::latest()->get()->sortDesc();
        return view('backend.message.all_message', compact('usermsg'));
    } // End Method

    public function AdminMessageDetails($id)
    {
        $msgdetails = PropertyMessage::findOrFail($id);
        return view('backend.message.message_details', compact('msgdetails'));
    } // End Method
}
