<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                "title" => ['required'],
                "coach" => ['required'],
            ]
        );

        if($validator->fails()) {
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

        $training = Training::create([
            'date' => $request->input('date'),
            'time_start' => $request->input('startTime'),
            'time_stop' => $request->input('stopTime'),
            'available' => true,
            'coach_name' => $request->input('coach'),
            'title' => $request->input('title'),
        ]);


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
