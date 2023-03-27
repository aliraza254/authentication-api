<?php

namespace App\Http\Controllers\AuthApi;

use Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\Role as RoleResource;
use App\Http\Controllers\AuthApi\BaseController as BaseController;

class RoleController extends BaseController
{
    public function index()
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->get();
        return $this->sendResponse(RoleResource::collection($roles), 'Roles fetched.');
    }

    public function show(Role $role)
    {
        $role = Role::find($role);
        if (is_null($role)) {
            return $this->sendError('Role does not exist.');
        }
        return $this->sendResponse(new RoleResource($role), 'Role fetched.');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => ['required', 'min:3'],
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $role = Role::create($input);
        return $this->sendResponse(new RoleResource($role), 'Role created.');
    }

    public function update(Request $request, Role $role)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $role->name = $input['name'];
        $role->save();

        return $this->sendResponse(new RoleResource($role), 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return $this->sendResponse([], 'Role deleted.');
    }

    public function givePermission(Request $request, Role $role)
    {
        if($role->hasPermissionTo($request->permission)){
            return $this->sendError('Permission exists.');
        }
        $permission = Permission::where('name', $request->permission)->first();
        if (!$permission) {
            $permission = Permission::create(['name' => $request->permission]);
        }
        $role->givePermissionTo($permission);
        return $this->sendResponse([], 'Permission added.');
    }

    public function revokePermission(Role $role, Permission $permission)
    {
        if($role->hasPermissionTo($permission)){
            $role->revokePermissionTo($permission);
            return back()->with('message', 'Permission revoked.');
        }
        return back()->with('message', 'Permission not exists.');
    }
}
