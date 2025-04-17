<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\PackagePlan;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyTypeController extends Controller
{
    public function AllType()
    {
        $types = PropertyType::latest()->get();
        return view('backend.type.all_type', compact('types'));
    }

    public function AddType()
    {
        return view('backend.type.add_type');
    }

    public function StoreType(Request $request)
    {
        $request->validate([
            'type_name' => 'required|unique:property_types|max:200',
            'type_icon' => 'required'
        ]);

        PropertyType::insert([
            'type_name' => $request->type_name, //'database table column name' => ->name of add_type view file form name='type_name'
            'type_icon' => $request->type_icon,
        ]);

        $notification = array(
            'message' => 'Property Type Create Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.type')->with($notification);
    }

    public function EditType($id)
    {

        $types = PropertyType::findOrFail($id);
        return view('backend.type.edit_type', compact('types'));
    }


    public function UpdateType(Request $request)
    {

        $pid = $request->id;

        PropertyType::findOrFail($pid)->update([
            'type_name' => $request->type_name, //'database table column name' => ->name of add_type view file form name='type_name'
            'type_icon' => $request->type_icon,
        ]);

        $notification = array(
            'message' => 'Property Type Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.type')->with($notification);
    }


    public function DeleteType($id)
    {

        PropertyType::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Property Deleted Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }



    //////// Amenities All Method ///////////

    public function AllAmenitie()
    {
        $amenities = Amenities::latest()->get();
        return view('backend.amenities.all_amenities', compact('amenities'));
    }

    public function AddAmenitie()
    {
        return view('backend.amenities.add_amenities');
    }

    public function StoreAmenitie(Request $request)
    {

        Amenities::insert([
            'amenitis_name' => $request->amenitis_name,
        ]);

        $notification  = array(
            'message' => 'Aminities Create Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.amenitie')->with($notification);
    }


    public function EditAmenitie($id)
    {

        $amenities = Amenities::findOrFail($id);
        return view('backend.amenities.edit_amenities', compact('amenities'));
    } // End Method

    public function UpdateAmenitie(Request $request)
    {

        $ame_id = $request->id;

        Amenities::findOrFail($ame_id)->update([

            'amenitis_name' => $request->amenitis_name,
        ]);

        $notification = array(

            'message' => 'Amenities Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.amenitie')->with($notification);
    } // End Method


    public function DeleteAmenitie($id)
    {

        Amenities::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Amenities Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method


    public function AdminPackageHistory()
    {

        $packagehistory = PackagePlan::latest()->get();

        return view('backend.package.package_history', compact('packagehistory'));
    }

    public function PackageInvoice($id)
    {

        $packagehistory = PackagePlan::where('id', $id)->first();

        $pdf = Pdf::loadView('backend.package.package_history_invoice', compact('packagehistory'))->setPaper('a4')->setOption([

            'tempDir' => public_path(),
            'chroot' => public_path(),

        ]);

        return $pdf->download('invoice.pdf');
    }
}
