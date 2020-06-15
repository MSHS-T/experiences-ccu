<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Plateau;
use App\User;

class PlateauController extends Controller
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
        return Plateau::all();
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
            'name'        => 'required|string',
            'description' => 'required|string',
            'manager_id'  => [
                'required',
                'integer',
                'exists:App\User,id',
                function ($attribute, $value, $fail) {
                    $manager = User::where('id', $value)->whereHas('roles', function (Builder $query) {
                        $query->where('key', 'PLAT');
                    })->first();
                    if (is_null($manager)) {
                        $fail("L'utilisateur ne possède pas le rôle 'Responsable plateau'");
                    }
                }
            ]
        ]);
        $plateau = Plateau::create($data);

        return $plateau;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Plateau::with('manipulations')->findOrFail($id);
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
        $plateau = Plateau::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string',
            'description' => 'required|string',
            'manager_id'  => [
                'required',
                'integer',
                'exists:App\User,id',
                function ($attribute, $value, $fail) {
                    $manager = User::where('id', $value)->whereHas('roles', function (Builder $query) {
                        $query->where('key', 'PLAT');
                    })->first();
                    if (is_null($manager)) {
                        $fail("L'utilisateur ne possède pas le rôle 'Responsable plateau'");
                    }
                }
            ]
        ]);
        $plateau->fill($data);

        $manager = User::where('id', $data['manager_id'])->first();
        $plateau->manager()->associate($manager);
        $plateau->save();

        return $plateau;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Plateau::findOrFail($id)->delete();
        return 204;
    }
}
