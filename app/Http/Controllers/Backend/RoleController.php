<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermissionExport;
use App\Imports\PermissionImport;
use App\Models\User;
use Log;
use DB;

class RoleController extends Controller
{
    // All Permission Controller
    public function AllPermission()
    {
        $permissions = Permission::all();
        return view('backend.pages.permission.all_permission', compact('permissions'));
    }

    public function AddPermission()
    {
        return view('backend.pages.permission.add_permission');
    }

    public function StorePermission(Request $request)
    {
        $permission = Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Create Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function EditPermission($id)
    {
        $permission = Permission::findOrFail($id);
        return view('backend.pages.permission.edit_permission', compact('permission'));
    }

    public function UpdatePermission(Request $request)
    {

        $per_id = $request->id;

        Permission::findOrFail($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.permission')->with($notification);
    }

    public function DeletePermission($id)
    {

        Permission::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Permission Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }


    // Import-Export Excel File
    public function ImportPermission()
    {
        return view('backend.pages.permission.import_permission');
    }

    public function Export()
    {
        return Excel::download(new PermissionExport, 'permission.xlsx');
    }

    public function Import(Request $request)
    {
        // Check if the file is uploaded and is an Excel file
        if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {
            // Validate that the file is an Excel file (.xlsx or .xls)
            $this->validate($request, [
                'import_file' => 'mimes:xlsx,xls|required'
            ]);

            // Try to import the file
            try {
                Excel::import(new PermissionImport, $request->file('import_file'));

                // Success notification
                return redirect()->route('all.permission')->with([
                    'message' => 'Permission Imported Successfully',
                    'alert-type' => 'success'
                ]);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Import error: ' . $e->getMessage());
                \Log::error('File size: ' . $request->file('import_file')->getSize());
                \Log::error('Memory usage: ' . memory_get_usage());

                // If an error occurs, show a failure message
                return back()->with([
                    'message' => 'Error importing file: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ]);
            }
        } else {
            // If no file is uploaded or the file is not valid, show an error
            return back()->with([
                'message' => 'Please upload a valid Excel file.',
                'alert-type' => 'error'
            ]);
        }
    }


    // All Role Controller
    public function AllRole()
    {
        $roles = Role::all();
        return view('backend.pages.roles.all_roles', compact('roles'));
    }

    public function StoreRole(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check if the category already exists
        $ExistingRoles = Role::where('name', $request->name)->first();

        if ($ExistingRoles) {

            $notification = array(
                'message' => 'Role already exists',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        Role::create([
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'Role added successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function EditRole($id)
    {

        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function  UpdateRole(Request $request)
    {

        $role = $request->role_id;
        $existRole =
            Role::findOrFail($role)->update([
                'id' =>  $request->role_id,
                'name' => $request->name,
            ]);

        $notification = array(
            'message' => 'Role updated successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function  DeleteRole($id)
    {
        Role::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Role deleted successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }



    // Add Role Permission all Method
    public function AddRolesPermission()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        // Create a new instance of the User model
        $user = new User();
        $permission_groups = $user->getpermissionGroups(); // Non-static call

        return view('backend.pages.rolesetup.add_roles_permission', compact('roles', 'permissions', 'permission_groups'));
    }

    public function RolePermissionStore(Request $request)
    {

        $data = array();
        $permissions = $request->permission;

        foreach ($permissions as $key => $item) {

            $data['role_id'] = $request->role_id;
            $data['permission_id'] = $item;

            DB::table('role_has_permissions')->insert($data);
        }

        $notification = array(
            'message' => 'Role Permission Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.roles.permission')->with($notification);
    }

    public function AllRolesPermission()
    {
        $roles = Role::all();
        return view('backend.pages.roles.all_roles_permission', compact('roles'));
    }

    public function AdminEditRoles($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        // Create a User instance
        $user = new User();
        // Call the method on the instance
        $permission_groups = $user->getpermissionGroups();
        return view('backend.pages.rolesetup.edit_roles_permission', compact('role', 'permissions', 'permission_groups'));
    }

    public function AdminRolesUpdate(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->permission ?? []; // Default to empty array if no permissions are selected

        // Validate the permissions to ensure they exist for the 'web' guard
        $validPermissions = Permission::whereIn('id', $permissions)->where('guard_name', 'web')->pluck('id')->toArray();

        // Sync the valid permissions, whether it's empty or populated
        $role->syncPermissions($validPermissions);

        $notification = array(
            'message' => 'Role Permissions Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.roles.permission')->with($notification);
    }

    public function AdminDeleteRoles($id){

        $role = Role::findOrFail($id);
        if (!is_null($role)) {
            $role->delete();
        }

        $notification = array(
            'message' => 'Role Permission Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method
}
