<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Manipulation;
use App\User;

class ManipulationController extends Controller
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
        return Manipulation::with('slots')->get();
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
            'name'                       => 'required|string',
            'description'                => 'required|string',
            'plateau_id'                 => 'required|integer|exists:App\Plateau,id',
            'duration'                   => 'required|integer|min:0',
            'target_slots'               => 'required|integer|min:0',
            'start_date'                 => 'required|date',
            'location'                   => 'required|string',
            'available_hours'            => 'required|array|size:7',
            'available_hours.*.enabled'  => ['required', 'boolean'],
            'available_hours.*.am'       => ['required', 'boolean'],
            'available_hours.*.start_am' => ['required_if:available_hours.*.am,1', 'regex:/^\d{2}:\d{2}$/'],
            'available_hours.*.end_am'   => ['required_if:available_hours.*.am,1', 'regex:/^\d{2}:\d{2}$/'],
            'available_hours.*.pm'       => ['required', 'boolean'],
            'available_hours.*.start_pm' => ['required_if:available_hours.*.pm,1', 'regex:/^\d{2}:\d{2}$/'],
            'available_hours.*.end_pm'   => ['required_if:available_hours.*.pm,1', 'regex:/^\d{2}:\d{2}$/'],
            'requirements'               => 'required|array',
            'requirements.*'             => 'required|string',
            'managers'                   => 'required|array|between:1,2',
            'managers.*'                 => [
                'required',
                'integer',
                'exists:App\User,id',
                function ($attribute, $value, $fail) {
                    $manager = User::where('id', $value)->whereHas('roles', function (Builder $query) {
                        $query->where('key', 'MANIP');
                    })->first();
                    if (is_null($manager)) {
                        $fail("L'utilisateur ne possède pas le rôle 'Responsable manipulation'");
                    }
                }
            ]
        ], [
            'managers.between' => 'Veuillez sélectionner entre 1 et 2 responsables.'
        ]);
        $manipulation = Manipulation::create($data);

        // Set managers
        $managers = User::whereIn('id', $data['managers'])->get();
        $manipulation->managers()->sync($managers->modelKeys());

        $manipulation->save();

        return $manipulation;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Manipulation::with('plateau')->findOrFail($id);
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
        $manipulation = Manipulation::findOrFail($id);
        // TODO : Prevent duration and start_date change if slots already exist
        $data = $request->validate([
            'name'                       => 'required|string',
            'description'                => 'required|string',
            'plateau_id'                 => 'required|integer|exists:App\Plateau,id',
            'duration'                   => 'required|integer|min:0',
            'target_slots'               => 'required|integer|min:0',
            'start_date'                 => 'required|date',
            'location'                   => 'required|string',
            'available_hours'            => 'required|array|size:7',
            'available_hours.*.enabled'  => ['required', 'boolean'],
            'available_hours.*.am'       => ['required', 'boolean'],
            'available_hours.*.start_am' => ['required_if:available_hours.*.am,1', 'regex:/^\d{2}:\d{2}$/'],
            'available_hours.*.end_am'   => ['required_if:available_hours.*.am,1', 'regex:/^\d{2}:\d{2}$/'],
            'available_hours.*.pm'       => ['required', 'boolean'],
            'available_hours.*.start_pm' => ['required_if:available_hours.*.pm,1', 'regex:/^\d{2}:\d{2}$/'],
            'available_hours.*.end_pm'   => ['required_if:available_hours.*.pm,1', 'regex:/^\d{2}:\d{2}$/'],
            'requirements'               => 'required|array',
            'requirements.*'             => 'required|string',
            'managers'                   => 'required|array|between:1,2',
            'managers.*'                 => [
                'required',
                'integer',
                'exists:App\User,id',
                function ($attribute, $value, $fail) {
                    $manager = User::where('id', $value)->whereHas('roles', function (Builder $query) {
                        $query->where('key', 'MANIP');
                    })->first();
                    if (is_null($manager)) {
                        $fail("L'utilisateur ne possède pas le rôle 'Responsable manipulation'");
                    }
                }
            ]
        ], [
            'managers.between' => 'Veuillez sélectionner entre 1 et 2 responsables.'
        ]);
        $manipulation->fill($data);

        // Set managers
        $managers = User::whereIn('id', $data['managers'])->get();
        $manipulation->managers()->sync($managers->modelKeys());

        $manipulation->save();

        return $manipulation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Manipulation::findOrFail($id)->delete();
        return 204;
    }
}
