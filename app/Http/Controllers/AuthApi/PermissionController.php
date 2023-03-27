<?php

namespace App\Http\Controllers\AuthApi;

use Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\Permission as PermissionResource;
use App\Http\Controllers\AuthApi\BaseController as BaseController;

class PermissionController extends BaseController
{
    public function index()
    {
        $permissions = Permission::all();
        return $this->sendResponse(PermissionResource::collection($permissions), 'Permissions fetched.');
    }

    public function show(Permission $permission)
    {
        $permission = Permission::find($permission);
        if (is_null($permission)) {
            return $this->sendError('Role does not exist.');
        }
        return $this->sendResponse(new PermissionResource($permission), 'Permission fetched.');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $permission = Permission::create($input);
        return $this->sendResponse(new PermissionResource($permission), 'Permission created.');
    }

    public function update(Request $request, Permission $permission)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $permission->name = $input['name'];
        $permission->save();
        return $this->sendResponse(new PermissionResource($permission), 'User updated.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return $this->sendResponse([], 'Permission deleted.');
    }

    public function assignRole(Request $request, Permission $permission)
    {
        if ($permission->hasRole($request->role)) {
            return back()->with('message', 'Role exists.');
        }

        $permission->assignRole($request->role);
        return back()->with('message', 'Role assigned.');
    }

    public function removeRole(Permission $permission, Role $role)
    {
        if ($permission->hasRole($role)) {
            $permission->removeRole($role);
            return back()->with('message', 'Role removed.');
        }

        return back()->with('message', 'Role not exists.');
    }
}
