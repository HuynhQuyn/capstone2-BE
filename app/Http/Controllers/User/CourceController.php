<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\ClassRoom;
use App\Models\Cource;
use App\Models\Excercise;
use App\Models\Participant;
use App\Models\User;
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
                $answer = Answer::where('excercise_id', $value)
                        ->where('class_id', $id_class)
                        ->where('user_id', $user->id)->first();
                $tmp->stattus = 0;
                if($answer){
                    $tmp->stattus = $answer->status;
                }
                $excercises[] = $tmp;
            }
            return response()->json([
                'excercises'  => $excercises,
            ], 200);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function getListMyClassTeaching(Request $request)
    {
        $user = auth()->user();
        $classes = ClassRoom::where('class_rooms.teacher', $user->id)
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

    public function getListExcerciseTeachingByClass($id_class)
    {
        $user = auth()->user();
        $class = ClassRoom::where('class_rooms.teacher', $user->id)
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

    public function getListUserDoneExcercise($id_class, $id_excercise)
    {
        $user = auth()->user();
        $class = ClassRoom::where('class_rooms.teacher', $user->id)
                    ->where('cources.is_block', 0)
                    ->where('class_rooms.id', $id_class)
                    ->join('cources', 'cources.id', 'class_rooms.id_cource')
                    ->select('class_rooms.*')
                    ->first();

        if ($class) {
            $users = User::where('answers.class_id', $id_class)
                        ->where('answers.excercise_id', $id_excercise)
                        ->join('answers', 'answers.user_id', 'users.id')
                        ->select('users.*', 'answers.status as answer_status')->get();
            return response()->json([
                'users'  => $users,
            ], 200);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function getListUserBelongClass($id_class)
    {
        $user = auth()->user();
        $class = ClassRoom::where('class_rooms.teacher', $user->id)
                    ->where('cources.is_block', 0)
                    ->where('class_rooms.id', $id_class)
                    ->join('cources', 'cources.id', 'class_rooms.id_cource')
                    ->select('class_rooms.*')
                    ->first();
        if ($class) {
            $students = json_decode($class->students);
            $users = User::whereIn('users.id', $students)
                        ->select('users.*')
                        ->get();
            if(count($users) > 0){
                foreach($users as $value){
                    $check = Participant::where('class_id', $id_class)
                                        ->where('user_id', $value->id)
                                        ->where('is_certificate', 1)
                                        ->first();

                    $value->is_certificate = 0;
                    if($check){
                        $value->is_certificate = 1;
                    }
                }
                return response()->json([
                    'users'  => $users,
                    'class'  => $class,
                ], 200);
            }
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function registerCertificateCourceOnline(Request $request)
    {
        $cource = Cource::where('id', $request->id_cource)->where('is_block', 0)->first();
        if ($cource) {
            $paticipant = Participant::where('user_id', $request->user_id)
                                        ->where('class_id', $request->id_class)
                                        ->where('cource_id', $request->id_cource)->first();
            if($paticipant){
                return response()->json(['error' => 'This user has been issued a certificate'], 400);
            }else{
                Participant::create([
                    'user_id' => $request->user_id,
                    'cource_id' => $request->id_cource,
                    'class_id' => $request->id_class,
                    'is_certificate' => 1,
                    'date_range' => date("Y/m/d"),
                    'date_expired' => date('Y-m-d', strtotime(date("Y-m-d") . " + 365 day"))
                ]);
                return response()->json([
                    'message'  => 'Successfully registered certificate for the course',
                ], 200);
            }
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }

    public function unregisterCertificateCourceOnline(Request $request)
    {
        $cource = Cource::where('id', $request->id_cource)->where('is_block', 0)->first();
        if ($cource) {
            $paticipant = Participant::where('user_id', $request->user_id)
                                        ->where('class_id', $request->id_class)
                                        ->where('cource_id', $request->id_cource)->first();
            if($paticipant){
                $paticipant->delete();
                return response()->json([
                    'message'  => 'Successfully un-registered certificate for the course',
                ], 200);
            }
            return response()->json([
                'error'  => 'The course does not have a certificate',
            ], 400);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }
}
