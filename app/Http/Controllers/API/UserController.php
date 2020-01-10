<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Role;
use App\User;

class UserController extends Controller
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwtauth');

        // TODO : add authorization gates (https://laravel.com/docs/6.x/authorization)
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return User::with('roles')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required',
            'roles'      => 'required|array'
        ], [
            'email.unique' => 'Cette adresse est déjà utilisée.'
        ]);
        $user = User::create($data);

        // Set roles
        $allRoles = Role::all();
        $userRoles = $allRoles->reject(function($r) use ($data){
            return !in_array($r->key, $data["roles"]);
        })->map(function($r){ return $r->id; });
        $user->roles()->sync($userRoles->toArray());

        $user->save();

        // TODO : Add email verification

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::with('roles')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => [
                'required',
                'email',
                Rule::unique('users')->ignore($user)
            ],
            'roles'      => 'required|array'
        ], [
            'email.unique' => 'Cette adresse est déjà utilisée.'
        ]);
        $user->fill($data);

        // Set roles
        $allRoles = Role::all();
        $userRoles = $allRoles->reject(function($r) use ($data){
            return !in_array($r->key, $data["roles"]);
        })->map(function($r){ return $r->id; });
        $user->roles()->sync($userRoles->toArray());

        $user->save();

        // TODO : Add email verification

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return 204;
    }
}
