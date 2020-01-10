<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Equipment;

class EquipmentController extends Controller
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
        return Equipment::all();
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
            'type'        => 'required|string',
            'description' => 'required|string',
            'quantity'    => 'required|integer|min:0'
        ]);
        $equipment = Equipment::create($data);

        return $equipment;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Equipment::findOrFail($id);
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
        $equipment = Equipment::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string',
            'type'        => 'required|string',
            'description' => 'required|string',
            'quantity'    => 'required|integer|min:0'
        ]);
        $equipment->fill($data);
        $equipment->save();

        return $equipment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Equipment::findOrFail($id)->delete();
        return 204;
    }
}
