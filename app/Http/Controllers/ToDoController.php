<?php

namespace App\Http\Controllers;

use App\Model\ToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Log;
class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = Carbon::now();
        $all = ToDo::whereDate('date',$date)->get()->toArray();
        return $all;
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
        Log::debug($request);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'date' => 'required',
            'reminder' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->getMessages(),
            ], 400);
        }

        $todo = new ToDo;
        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->date = Carbon::parse($request->date);
        $todo->reminder = $request->reminder;
        if($request->reminder)
        {
            if($request->from && $request->from!=null)
            {
                $todo->time = Carbon::parse($request->from)->format("H:i");
            }
            else{
            return  response()->json(['errors' => ["time" => ["Time is required"]]], 400);
            }
        }
        $todo->save();
    }

    public function weekly(Request $request)
    {
        $response = [];
        $date = Carbon::now();
        $monday = Carbon::now()->startOfWeek();
        $last = Carbon::now()->addWeek()->startOfWeek();
        Log::debug($last);
        for($i=$monday;$i<$last;$i->addDay())
        {
            $res = new \stdClass();
            $todo = ToDo::whereDate('date',$i)->get()->toArray();
            $res->date = $i->format("d-m-Y");
            $res->todos = $todo;
            array_push($response,$res);
        }
        return $response;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Model\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function show(ToDo $toDo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function edit(ToDo $toDo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ToDo $todo)
    {
        ToDo::where("id",$todo->id)->update(["completed" => $request->completed]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ToDo $todo)
    {
        $todo->delete();
    }
}
