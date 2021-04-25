<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    public function pop(Request $request)
    {
        try {
            $training = Training::find($request->input('id'));
            $res = $training->delete();
            return [
                'status' => true,
                'res' => $res
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function book(Request $request)
    {
        try {
            $training = Training::find($request->input('id'));
            $training->available = false;
            $training->client_name = $request->input('name');
            $res = $training->save();
            return [
                'status' => true,
                'res' => $res
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function load(Request $request)
    {
        try {
            $res = Training::select('*')
                ->whereBetween('date', [$request->input('startDate'), $request->input('stopDate')]);
            if ($request->input('coach') != "all") {
                $res = $res->where('coach_name', $request->input('coach'));
            }
            $res = $res->get();
            return [
                'status' => true,
                'table' => $res
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool[]
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "date" => ['required'],
                "startTime" => ['required'],
                "stopTime" => ['required'],
                "title" => ['required']
            ]
        );

        if ($validator->fails()) {
            return [
                "status" => false,
                "errors" => $validator->messages()
            ];
        }

//        dd([
//            'date' => $request->input('date'),
//            'time_start' => $request->input('startTime'),
//            'time_stop' => $request->input('stopTime'),
//            'available' => true,
//            'coach_name' => $request->input('coach'),
//            'title' => $request->input('title'),
//        ]);

        if ($request->input('coach') != 'no') {
            $training = Training::create([
                'date' => $request->input('date'),
                'time_start' => $request->input('startTime'),
                'time_stop' => $request->input('stopTime'),
                'available' => true,
                'coach_name' => $request->input('coach'),
                'title' => $request->input('title'),
            ]);
        }
        else {
            $training = Training::create([
                'date' => $request->input('date'),
                'time_start' => $request->input('startTime'),
                'time_stop' => $request->input('stopTime'),
                'available' => false,
                'coach_name' => 'Без тренера',
                'client_name' => $request->input('client'),
                'title' => $request->input('title'),
            ]);
        }



        return [
            "status" => true,
            "training" => $training
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
