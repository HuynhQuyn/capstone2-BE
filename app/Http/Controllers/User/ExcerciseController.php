<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cource;
use App\Models\Excercise;
use App\Models\Participant;
use App\Models\Question;
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
}
