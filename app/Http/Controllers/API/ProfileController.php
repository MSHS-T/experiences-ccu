<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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
        return Auth::user();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $old_email = $user->email;
        $data = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => [
                'required',
                'email',
                Rule::unique('users')->ignore($user)
            ],
            'password' => 'sometimes|required|confirmed'
        ], [
            'email.unique' => 'Cette adresse est déjà utilisée.'
        ]);

        $user->fill($data);
        $user->save();

        if ($user->email !== $old_email) {
            // TODO : Add email verification if user changed his email
        }

        return $user;
    }
}
