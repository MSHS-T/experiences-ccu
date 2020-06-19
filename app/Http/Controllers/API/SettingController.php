<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Setting;

class SettingController extends Controller
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
        return Setting::all();
    }

    /**
     * Update all settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_cancellation_delay' => 'required|integer|min:0',
            'booking_opening_delay'      => 'required|integer|min:0',
            'manipulation_overbooking'   => 'required|integer|min:100',
        ]);
        foreach ($data as $name => $value) {
            Setting::updateOrCreate(['name' => $name], ['value' => $value]);
        }

        return Setting::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Setting::findOrFail($id);
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
        $Setting = Setting::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string',
            'type'        => 'required|string',
            'description' => 'required|string',
            'quantity'    => 'required|integer|min:0'
        ]);
        $Setting->fill($data);
        $Setting->save();

        return $Setting;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();
        return 204;
    }
}
