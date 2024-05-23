<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\ClassRoom;
use App\Models\Cource;
use App\Models\Excercise;
use App\Models\Participant;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class ExcerciseController extends Controller
{
    public function getExcerciseOfflineByID($id_cource, $id_excercise)
    {
        $user = auth()->user();
        $cource = Cource::where('id', $id_cource)->where('is_block', 0)->first();
        $paticipant = Participant::where('user_id', $user->id)
                                ->where('is_register', 1)->where('cource_id', $id_cource)->first();
        if ($cource && $paticipant) {
            $excercise = Excercise::where('id', $id_excercise)->where('excercise_type', 1)->first();
            if($excercise){
                $questions = json_decode($excercise->excercise_content);
                foreach($questions as $value){
                    $question = Question::where('id', $value->question_id)->first();
                    $value->question = $question;
                }
                $excercise->excercise_content = json_encode($questions);
                $check = (int) $id_excercise == $cource->final_excercise;
                return response()->json([
                    'excercise'  => $excercise,
                    'is_final '  => $check,
                ], 200);
            }
            return response()->json(['error' => 'There are no excercise in the system'], 400);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }

    public function getExcerciseOnlineByID($id_class, $id_excercise)
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
                if($value == $id_excercise){
                    $excercise = Excercise::where('id', $value)->first();
                    if($excercise){
                        $listQuestions = json_decode($excercise->excercise_content);
                        foreach($listQuestions as $v){
                            $question = Question::where('id', $v->question_id)->first();
                            $v->question = $question;
                        }
                        $excercise->excercise_content = json_encode($listQuestions);
                    }
                    return response()->json([
                        'excercise'  => $excercise,
                    ], 200);
                }
            }
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function submitExcerciseOnline(Request $request)
    {
        $user = auth()->user();
        $class = ClassRoom::whereJsonContains('class_rooms.students', (int)$user->id)
                    ->where('cources.is_block', 0)
                    ->where('class_rooms.id', $request->class_id)
                    ->join('cources', 'cources.id', 'class_rooms.id_cource')
                    ->select('class_rooms.*')
                    ->first();

        $excercise = Excercise::where('id', $request->excercise_id)->first();

        if ($class && $excercise) {
            Answer::create([
                'class_id' => $request->class_id,
                'excercise_id' => $request->excercise_id,
                'user_id' => $user->id,
                'answer_content' => $request->answer_content

            ]);
            return response()->json([
                'message' => 'Successfully submit answer',
            ], 200);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function getAnswerDetail($id_class, $id_excercise, $id_student)
    {
        $user = auth()->user();
        $class = ClassRoom::where(function($query) use ($user){
                                $query->whereJsonContains('class_rooms.students', (int)$user->id);
                                $query->orwhere('class_rooms.teacher', $user->id);
                            })
                            ->where('cources.is_block', 0)
                            ->where('class_rooms.id', $id_class)
                            ->join('cources', 'cources.id', 'class_rooms.id_cource')
                            ->select('class_rooms.*')
                            ->first();
        if ($class) {
            $answer = Answer::where('answers.class_id', $id_class)
                        ->where('answers.excercise_id', $id_excercise)
                        ->join('users', 'answers.user_id', 'users.id')
                        ->where('answers.user_id', $id_student)
                        ->select('answers.*')->first();
            if ($answer){
                return response()->json([
                    'answer'  => $answer,
                ], 200);
            }
            return response()->json(['error' => 'There are no answer in the system'], 400);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function gradeExcerciseOnline(Request $request)
    {
        $user = auth()->user();
        $class = ClassRoom::where('class_rooms.teacher', $user->id)
                    ->where('cources.is_block', 0)
                    ->where('class_rooms.id', $request->class_id)
                    ->join('cources', 'cources.id', 'class_rooms.id_cource')
                    ->select('class_rooms.*')
                    ->first();

        $excercise = Excercise::where('id', $request->excercise_id)->first();

        if ($class && $excercise) {
            $answer = Answer::where('class_id', $request->class_id)
                            ->where('excercise_id', $request->excercise_id)
                            ->where('user_id', $request->user_id)
                            ->first();
            if ($answer) {
                $answer->answer_content = $request->answer_content;
                $answer->status = 2;
                $answer->save();
                return response()->json([
                    'message' => 'Successfully grade answer',
                ], 200);
            }
            return response()->json(['error' => 'There are no answer in the system'], 400);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }
}
