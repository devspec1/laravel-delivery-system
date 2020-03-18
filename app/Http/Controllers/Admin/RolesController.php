<?php

/**
 * Roles Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Roles
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\RoleDataTable;
use App\Models\Role;
use App\Models\Admin;
use App\Models\Permission;
use App\Http\Start\Helpers;
use Auth;
use Validator;
use DB;

class RolesController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
    * Load Datatable for Roles
    *
    * @param array $dataTable  Instance of RolesDataTable
    * @return datatable
    */
    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('admin.roles.view');
    }

    /**
    * Add a New Role
    *
    * @param array $request  Input values
    * @return redirect     to Roles view
    */
    public function add(Request $request)
    {
        if(!$_POST) {
            $data['permissions'] = Permission::get();
            return view('admin.roles.add', $data);
        }
        else if($request->submit) {
            // Add Role Validation Rules
            $rules = array(
                'name'         => 'required|unique:roles',
                'display_name' => 'required',
                'description'  => 'required',
            );

            // Add Role Validation Custom Names
            $niceNames = array(
                'name'         => 'Name',
                'display_name' => 'Display Name',
                'description'  => 'Description',
                'permission'   => 'Permission'
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 
            if ($validator->fails()) {
            	// Form calling with Errors and Input values
                return back()->withErrors($validator)->withInput(); 
            }

            $request->permission = is_array($request->permission) ? $request->permission : [];
            $permission = [];
            $permission = $request->permission;
            $role = new Role;
            $role->name = $request->name;
            $role->display_name = $request->display_name;
            $role->description = $request->description;
            $role->save();

            if($request->permission){
                $role->perms()->sync($permission);
            }
            // Call flash message function
            $this->helper->flash_message('success', 'Added Successfully');
            return redirect('admin/roles');
        }

        return redirect('admin/roles');
    }

    /**
    * Update Role Details
    *
    * @param array $request    Input values
    * @return redirect     to Roles View
    */
    public function update(Request $request)
    {
        if(!$_POST) {
            $data['result'] = Role::find($request->id);
            $data['stored_permissions'] = Role::permission_role($request->id);
            $data['permissions'] = Permission::get();
            return view('admin.roles.edit', $data);
        }
        else if($request->submit) {
            // Edit Role Validation Rules
            $rules = array(
                    'name'         => 'required|unique:roles,name,'.$request->id,
                    'display_name' => 'required',
                    'description'  => 'required',
                    // 'permission'   => 'required'
                    );
            // Edit Role Validation Custom Fields Name
            $niceNames = array(
                        'name'         => 'Name',
                        'display_name' => 'Display Name',
                        'description'  => 'Description',
                        'permission'   => 'Permission'
                        );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $request->permission = is_array($request->permission) ? $request->permission : [];
            $permission = [];
            $permission = $request->permission;
            $role = Role::find($request->id);
            $role->name = $request->name;
            $role->display_name = $request->display_name;
            $role->description = $request->description;
            $role->save();
            $role->perms()->sync($permission);

            $this->helper->flash_message('success', 'Updated Successfully'); 
            return redirect('admin/roles');
        }
        return redirect('admin/roles');
    }

    /**
    * Delete Role
    *
    * @param array $request    Input values
    * @return redirect     to Roles View
    */
    public function delete(Request $request)
    {
        if (DB::table('role_user')->where('role_id', $request->id)->exists()) {
            $this->helper->flash_message('error','Sorry this role is already in use. So canont delete.');
            return redirect('admin/roles');
        }
        Role::where('id', $request->id)->delete();
        // Call flash message function
        $this->helper->flash_message('success', 'Deleted Successfully');
        return redirect('admin/roles');
    }
}
