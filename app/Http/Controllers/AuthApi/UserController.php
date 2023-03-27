<?php

namespace App\Http\Controllers\AuthApi;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\User as UserResource;
use App\Http\Controllers\AuthApi\BaseController as BaseController;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return $this->sendError('No users found.');
        }

        return $this->sendResponse(UserResource::collection($users), 'Users fetched.');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'confirm_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->guard_name = 'sanctum';
        $user->assignRole('user');
        return $this->sendResponse(new UserResource($user), 'User created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::find($user);
        if (is_null($user)) {
            return $this->sendError('User does not exist.');
        }
        return $this->sendResponse(new UserResource($user), 'User fetched.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = bcrypt($input['password']);
        $user->save();

        return $this->sendResponse(new UserResource($user), 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->sendResponse([], 'User deleted.');
    }


    public function assignRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Role is required.', $validator->errors());
        }

        if ($user->hasRole($request->role)) {
            return $this->sendError('Role exists.');
        }

        $user->assignRole($request->role);

        return $this->sendResponse(new UserResource($user), 'Role assigned.');
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return $this->sendResponse(new UserResource($user), 'Role removed.');
        }

        return $this->sendError('Role does not exist.');


    }

    public function givePermission(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Permission is required.', $validator->errors());
        }

        if ($user->hasRole($request->permission)) {
            return $this->sendError('Permission exists.');
        }

        $user->assignRole($request->role);

        return $this->sendResponse(new UserResource($user), 'Permission added.');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return $this->sendResponse('Permission revoked.');
        }
        return $this->sendError('Permission does not exists.');
    }
}
