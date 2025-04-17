<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PropertyType;
use App\Models\State;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class StateController extends Controller
{
    public function AllState()
    {
        $state = State::latest()->get();
        return view('backend.state.all_state', compact('state'));
    } // End Method

    public function AddState()
    {

        return view('backend.state.add_state');
    }

    public function StoreState(Request $request)
    {
        if ($request->file('state_image')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('state_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('state_image'));
            $image = $image->resize(370, 275);
            $image->toJpeg(80)->Save(base_path(('public/upload/state/' . $name_gen)));
            $save_url = 'upload/state/' . $name_gen;
        } else {
            $save_url = '';
        }

        State::insert([
            'state_name' => $request->state_name,
            'state_image' => $save_url,
        ]);

        $notification = array(
            'message' => 'State Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.state')->with($notification);
    }

    public function EditState($id)
    {

        $state = State::findOrFail($id);
        return view('backend.state.edit_state', compact('state'));
    }

    public function UpdateState(Request $request)
    {
        $state_id = $request->id;
        $state = State::findOrFail($state_id);

        // Check if a new image is uploaded
        if ($request->file('state_image')) {

            // Unlink the old image
            $old_image = $state->state_image;
            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            // Process and save the new image
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('state_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('state_image'));
            $image = $image->resize(370, 275);
            $image->toJpeg(80)->save(public_path('upload/state/' . $name_gen));
            $save_url = 'upload/state/' . $name_gen;

            // Update the state with the new image
            $state->update([
                'state_name' => $request->state_name,
                'state_image' => $save_url,
            ]);
        } else {
            // Update the state without changing the image
            $state->update([
                'state_name' => $request->state_name,
            ]);
        }

        $notification = array(
            'message' => 'State Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.state')->with($notification);
    }

    public function DeleteState($id)
    {
        // Find the state by ID or fail
        $state = State::findOrFail($id);
        $old_image = $state->state_image;

        // Attempt to delete the state and the old image
        try {
            // Check if the old image exists and delete it
            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            // Delete the state from the database
            $state->delete();

            // Prepare success notification
            $notification = [
                'message' => 'State deleted successfully.',
                'alert-type' => 'success'
            ];
        } catch (Exception $e) {
            // Log the error and prepare error notification
            Log::error("Error deleting state: " . $e->getMessage());
            return redirect()->route('all.state')->with([
                'message' => 'Failed to delete state: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }

        // Redirect with success notification
        return redirect()->route('all.state')->with($notification);
    }
    
}
