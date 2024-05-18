<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Excercise;
use App\Models\Question;
use Illuminate\Http\Request;

class CourceController extends Controller
{
    public function getListMyClassLearning(Request $request)
    {
        $user = auth()->user();
        $classes = ClassRoom::whereJsonContains('class_rooms.students', (int)$user->id)
                    ->where('cources.is_block', 0)
                    ->join('cources', 'cources.id', 'class_rooms.id_cource')
                    ->join('users', 'users.id', 'class_rooms.teacher');

        if($request->cource_name != ""){
            $classes = $classes->where('cources.cource_name', 'like' , '%' . $request->cource_name  . '%');
        }
        $classes = $classes->select('class_rooms.*', 'cources.cource_name', 'users.full_name as teacher_name')->paginate(5);

        if (count($classes) > 0) {
            return response()->json([
                'classes'  => $classes,
            ], 200);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function getListExcerciseByClass($id_class)
    {
        $user = auth()->user();
        $class = ClassRoom::whereJsonContains('class_rooms.students', (int)$user->id)
                    ->where('cources.is_block', 0)
                    ->where('class_rooms.id', $id_class)
                    ->join('cources', 'cources.id', 'class_rooms.id_cource')
                    ->select('class_rooms.*')
                    ->first();

        if ($class) {
            $list_excercise_id = json_decode($class->id_excercises);
            foreach($list_excercise_id as $value){
                $tmp = Excercise::where('id', $value)->first();
                $excercises[] = $tmp;
            }
            return response()->json([
                'excercises'  => $excercises,
            ], 200);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }
}
