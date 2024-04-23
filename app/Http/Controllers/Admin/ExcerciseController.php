<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Excercise;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ExcerciseController extends Controller
{
    public function getData(Request $request)
    {
        $excercises = Excercise::orderBy('id', 'DESC');
        if($request->excercise_name != ""){
            $excercises = $excercises->where('excercise_name', 'like' , '%' . $request->excercise_name  . '%');
        }
        $excercises = $excercises->paginate(5);
        if (count($excercises) > 0) {
            return response()->json([
                'excercises'  => $excercises,
            ], 200);
        }
        return response()->json(['error' => 'There are no excercises in the system'], 400);
    }


    public function store(Request $request)
    {
        $data = $request->all();
        Excercise::create($data);
        return response()->json([
            'message' => 'Successfully added a new excercise',
        ], 200);
    }

    public function destroy($id)
    {
        $excercise = Excercise::where("id", $id)->first();
        if ($excercise) {
            $excerciseInLesson = Lesson::where('id_excercise', $id)->first();
            $excerciseInSchedule = ClassRoom::whereJsonContains('id_excercises', (int)$id)->first();
            if($excerciseInLesson || $excerciseInSchedule){
                return response()->json([
                    'error' => "The excercise being used",
                ], 400);
            }

            $excercise->delete();
            return response()->json([
                'message' => 'Successfully delete a excercise',
            ], 200);
        }
        return response()->json([
            'error' => "The excercise is not correct",
        ], 400);
    }

    public function getDataById($id)
    {
        $excercise = Excercise::where("id", $id)->first();
        if ($excercise) {
            return response()->json([
                'excercise'      => $excercise,
            ]);
        }
        return response()->json([
            'error' => "The excercise is not correct",
        ], 400);
    }


    public function update(Request $request)
    {
        $excercise = Excercise::where("id", $request->id)->first();
        $data = $request->all();
        if ($excercise) {
            $excercise->update($data);
            return response()->json([
                'message' => 'Successfully update a excercise',
            ], 200);
        }
        return response()->json([
            'error' => "The excercise is not correct",
        ], 400);
    }

    public function getList()
    {
        $excercises = Excercise::orderBy('id', 'DESC')->get();
        if (count($excercises) > 0) {
            return response()->json([
                'excercises'  => $excercises,
            ], 200);
        }
        return response()->json(['error' => 'There are no excercises in the system'], 400);
    }

    public function getListOffline()
    {
        $excercises = Excercise::orderBy('id', 'DESC')->where('excercise_type', 1)->get();
        if (count($excercises) > 0) {
            return response()->json([
                'excercises'  => $excercises,
            ], 200);
        }
        return response()->json(['error' => 'There are no excercises in the system'], 400);
    }
}
