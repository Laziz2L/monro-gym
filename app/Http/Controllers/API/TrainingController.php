<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function pop(Request $request)
    {
        try {
            $training = Training::find($request->input('id'));

            $capacityQuery = DB::table('capacities')->select('value')
                ->where('date', $training->date)
                ->where('hour', substr($training->time_start, 0, 2));

            $capacity = 0;
            if ($capacityQuery->exists()) {
                $capacity = $capacityQuery->first()->value;
            }

            if ($capacity == 1) {
                $capacityQuery->update(['value' => 0]);
            } else if ($capacity == 2) {
                $name = $training->client_name;
                if ($name) {
                    if (stripos($name, '+')) {
                        $capacityQuery->update(['value' => 0]);
                    } else $capacityQuery->update(['value' => 1]);
                }
            }

            $res = $training->delete();
            return [
                'status' => true,
                'res' => $res,
                'msg' => 'Успешно удалено!'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function book(Request $request)
    {
        try {
            $clientsCount = 1;

            $training = Training::find($request->input('id'));

            $capacityQuery = DB::table('capacities')->select('value')
                ->where('date', $training->date)
                ->where('hour', substr($training->time_start, 0, 2));

            $capacity = 0;
            if ($capacityQuery->exists()) {
                $capacity = $capacityQuery->first()->value;
            }

            if ($capacity >= 2) {
                return [
                    'status' => false,
                    'msg' => 'На это время мест больше нет'
                ];
            }

            $client_name = $request->input('name');
            if ($request->input('second')) {
                if ($capacity == 1) {
                    return [
                        'status' => false,
                        'msg' => 'На это время места для двоих не хватит'
                    ];
                }
                $clientsCount = 2;
                $client_name = $client_name . "+" . $request->input('second');
            }


            if (!DB::table('capacities')->updateOrInsert(
                [
                    'date' => $training->date,
                    'hour' => substr($training->time_start, 0, 2)
                ],
                [
                    'value' => ($capacity + $clientsCount)
                ]
            )) return [
                'status' => false,
                'msg' => 'capacity table error'
            ];


            $training->available = false;
            $training->client_name = $client_name;
            $training->client_id = $request->input('clientId');
            $training->capacity = $clientsCount;
            $res = $training->save();

            if ($res) {
                $coachName = $training->coach_name;
                $azalia = '818164532';
                $tatyana = '394040682';
                $messageToCoach = 'Новая запись  Дата: ' . $training->date . '  Время: ' . $training->time_start. '  Имя: ' . $training->client_name;
                $ch = curl_init();
                curl_setopt_array(
                    $ch,
                    array(
                        CURLOPT_URL => 'https://api.telegram.org/bot1742712187:AAFWrq0pw162p2ZFizHdJlfCxnDUeMZgrYc/sendMessage',
                        CURLOPT_POST => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_POSTFIELDS => array(
                            'chat_id' => $coachName == 'Азалия' ? $azalia : $tatyana,
                            'text' => $messageToCoach,
                        ),
                    )
                );
                curl_exec($ch);
            }

            return [
                'status' => true,
                'res' => $res,
                'msg' => 'Успешно записано!'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage() . ' on line ' . $e->getLine()
            ];
        }
    }

    public function unBook(Request $request) {
        try {
            $training = Training::find($request->input('id'));
            if (!$training) {
                return [
                    'status' => false,
                    'msg' => 'Нет такой тренировки'
                ];
            }

            $capacityQuery = DB::table('capacities')->select('value')
                ->where('date', $training->date)
                ->where('hour', substr($training->time_start, 0, 2));

            $capacity = 2;
            if ($capacityQuery->exists()) {
                $capacity = $capacityQuery->first()->value;
            }

            if (!DB::table('capacities')->updateOrInsert(
                [
                    'date' => $training->date,
                    'hour' => substr($training->time_start, 0, 2)
                ],
                [
                    'value' => ($capacity - $training->capacity)
                ]
            )) return [
                'status' => false,
                'msg' => 'capacity table error'
            ];

            $clientName = $training->client_name;
            $training->available = true;
            $training->client_name = null;
            $training->client_id = null;
            $training->capacity = 0;
            $res = $training->save();

            if ($res) {
                $coachName = $training->coach_name;
                $azalia = '818164532';
                $tatyana = '394040682';
                $messageToCoach = 'Отмена записи  Дата: ' . $training->date . '  Время: ' . $training->time_start. '  Имя: ' . $clientName;
                $ch = curl_init();
                curl_setopt_array(
                    $ch,
                    array(
                        CURLOPT_URL => 'https://api.telegram.org/bot1742712187:AAFWrq0pw162p2ZFizHdJlfCxnDUeMZgrYc/sendMessage',
                        CURLOPT_POST => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_POSTFIELDS => array(
                            'chat_id' => $coachName == 'Азалия' ? $azalia : $tatyana,
                            'text' => $messageToCoach,
                        ),
                    )
                );
                curl_exec($ch);
            }

            return [
                'status' => true,
                'res' => $res,
                'msg' => 'Успешно отменено!'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage()
            ];
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
            return [
                'status' => false,
                'msg' => $e->getMessage()
            ];
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
        try {
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
                    "msg" => $validator->messages()
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

            $capacityQuery = DB::table('capacities')->select('value')
                ->where('date', $request->input('date'))
                ->where('hour', substr($request->input('startTime'), 0, 2));

            $capacity = 0;

            if ($capacityQuery->exists()) {
                $capacity = $capacityQuery->first()->value;
            }

            if ($capacity >= 2) {
                return [
                    'status' => false,
                    'msg' => 'На это время мест больше нет'
                ];
            }

            if ($request->input('coach') != 'no') {
                $training = Training::create([
                    'creator' => $request->input('creator'),
                    'date' => $request->input('date'),
                    'time_start' => $request->input('startTime'),
                    'time_stop' => $request->input('stopTime'),
                    'available' => true,
                    'coach_name' => $request->input('coach'),
                    'title' => $request->input('title'),
                ]);
            } else {
                if (!DB::table('capacities')->updateOrInsert(
                    [
                        'date' => $request->input('date'),
                        'hour' => substr($request->input('startTime'), 0, 2)
                    ],
                    [
                        'value' => ($capacity + 1)
                    ]
                )) return [
                    'status' => false,
                    'msg' => 'capacity table error'
                ];

                $training = Training::create([
                    'creator' => $request->input('creator'),
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
                "training" => $training,
                "msg" => "Успешно записано!"
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }
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
