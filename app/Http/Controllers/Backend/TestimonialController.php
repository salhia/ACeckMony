<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Exists;

class TestimonialController extends Controller
{
    public function AllTestimonials()
    {
        $testimonial = Testimonial::latest()->get();
        return view('backend.testimonial.all_testimonial', compact('testimonial'));
    }

    public function AddTestimonials()
    {
        return view('backend.testimonial.add_testimonial');
    }

    public function StoreTestimonials(Request $request)
    {
        // Define the directory path
        $directoryPath = base_path('public/upload/testimonial/');

        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true);
        }

        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(370, 275);
            $image->toJpeg(80)->Save($directoryPath . $name_gen);
            $save_url = 'upload/testimonial/' . $name_gen;
        } else {
            $save_url = '';
        }

        Testimonial::insert([
            'name' => $request->name,
            'position' => $request->position,
            'message' => $request->message,
            'image' => $save_url,
        ]);

        $notification = array(
            'message' => 'Testimonial Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.testimonials')->with($notification);
    }

    public function  EditTestimonials($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return view('backend.testimonial.edit_testimonial', compact('testimonial'));
    }

    public function UpdateTestimonials(Request $request)
    {
        $testimonial = $request->id;
        $testimonial = Testimonial::findOrFail($testimonial);

        // Check if a new image is uploaded
        if ($request->file('image')) {

            // Unlink the old image
            $old_image = $testimonial->image;
            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            // Process and save the new image
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(370, 275);
            $image->toJpeg(80)->save(public_path('upload/testimonial/' . $name_gen));
            $save_url = 'upload/testimonial/' . $name_gen;

            //Update with image
            $testimonial->update([
                'name' => $request->name,
                'position' => $request->position,
                'message' => $request->message,
                'image' => $save_url,
            ]);
        } else {
            // Update without changing the image
            $testimonial->update([
                'name' => $request->name,
                'position' => $request->position,
                'message' => $request->message,
            ]);
        }

        $notification = array(
            'message' => 'Testimonial  Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.testimonials')->with($notification);
    }

    public function DeleteTestimonials($id)
    {
        $test = Testimonial::findOrFail($id);
        $old_image = $test->image;

        if (!empty($old_image) && file_exists(public_path($old_image))) {
            unlink(public_path($old_image));
        }

        $test->delete();

        $notification = [
            'message' => 'State deleted successfully.',
            'alert-type' => 'success'
        ];

        return back()->with($notification);
    }
}
