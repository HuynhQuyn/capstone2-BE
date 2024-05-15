<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ClassDetail;
use App\Models\ClassRoom;
use App\Models\Cource;
use App\Models\Lesson;
use App\Models\Participant;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function listCource(Request $request)
    {
        $user = auth()->user();
        $cources = Cource::select('cources.*')
            ->orderBy('cources.id', 'DESC')->where('cources.is_block', 0);
        if($request->cource_name != ""){
            $cources = $cources->where('cources.cource_name', 'like' , '%' . $request->cource_name  . '%');
        }
        if($request->is_register == 1){
            $cources = $cources->join('participants', 'participants.cource_id', 'cources.id')
            ->where('participants.user_id', $user->id)
            ->where('participants.is_register', 1);
        }
        if(is_integer($request->cource_type)){
            $cources = $cources->where('cources.cource_type', $request->cource_type);
        }
        $cources = $cources->select('cources.*')->paginate(5);
        if (count($cources) > 0) {
            return response()->json([
                'cources'  => $cources,
            ], 200);
        }
        return response()->json(['error' => 'There are no cources in the system'], 400);
    }

    public function courceDetail($id)
    {
        $cource = Cource::where('id', $id)->where('is_block', 0)->first();
        $lessons = [];
        if($cource){
            if($cource->cource_type == 1){
                $chapters = json_decode($cource->chapter);
                foreach($chapters as $value){
                    $listLessonOfChapter = Lesson::where('lessons.id_cource', $id)
                            ->where("lessons.id_chapter", $value->id)
                            ->orderBy('lessons.id', 'DESC')->get();
                    foreach($listLessonOfChapter as $v){
                        $lessons[] = $v;
                    }
                }

                return response()->json([
                    'cource'   => $cource,
                    'lessons'  => $lessons,
                ], 200);
            }
            return response()->json([
                'cource'   => $cource,
            ], 200);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }

    public function listSchedule()
    {
        $user = auth()->user();
        $schedules = ClassRoom::whereJsonContains('class_rooms.students', (int)$user->id)
                                ->where('cources.is_block', 0)
                                ->join('class_details', 'class_details.id_class', 'class_rooms.id')
                                ->join('cources', 'cources.id', 'class_rooms.id_cource')
                                ->select('class_rooms.*', 'class_details.title', 'class_details.date', 'class_details.link')
                                ->get();
        if (count($schedules) > 0) {
            return response()->json([
                'schedules'  => $schedules,
            ], 200);
        }
        return response()->json(['error' => 'There are no schedule in the system'], 400);
    }

    public function registerCource($id_cource)
    {
        $user = auth()->user();
        $cource = Cource::where('id', $id_cource)->where('is_block', 0)->first();
        if ($cource) {
            $paticipant = Participant::where('user_id', $user->id)->where('cource_id', $id_cource)->first();
            if($paticipant){
                $paticipant->is_register = 1;
                $paticipant->save();
            }else{
                Participant::create([
                    'user_id' => $user->id,
                    'cource_id' => $cource->id,
                    'is_register' => 1
                ]);
            }
            return response()->json([
                'message'  => 'Successfully registered for the course',
            ], 200);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }

    public function unregisterCource($id_cource)
    {
        $user = auth()->user();
        $cource = Cource::where('id', $id_cource)->where('is_block', 0)->first();
        if ($cource) {
            $paticipant = Participant::where('user_id', $user->id)
                                        ->where('is_certificate', 0)
                                        ->where('cource_id', $id_cource)->first();
            if($paticipant){
                $paticipant->delete();
                return response()->json([
                    'message'  => 'Successfully un-registered for the course',
                ], 200);
            }
            return response()->json([
                'error'  => 'Not possible to unsubscribe from a completed course',
            ], 400);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }

    public function checkRegisterCource($id_cource)
    {
        $user = auth()->user();
        $cource = Cource::where('id', $id_cource)->where('is_block', 0)->first();
        if ($cource) {
            $paticipant = Participant::where('user_id', $user->id)
                                    ->where('is_register', 1)
                                    ->where('cource_id', $id_cource)->first();
            $check = false;
            if($paticipant){
                $check = true;
            }

            return response()->json([
                'is_register'  => $check,
            ], 200);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }

    public function registerCertificate($id_cource)
    {
        $user = auth()->user();
        $cource = Cource::where('id', $id_cource)->where('is_block', 0)->first();
        $paticipant = Participant::where('user_id', $user->id)
                                ->where('is_register', 1)
                                ->where('cource_id', $id_cource)->first();

        if ($cource && $paticipant) {
            $paticipant->is_certificate = 1;
            $paticipant->date_range = date("Y/m/d");
            $paticipant->date_expired = date('Y-m-d', strtotime(date("Y-m-d") . " + 365 day"));
            $paticipant->save();

            return response()->json([
                'message'  => 'Successfully registered certificate for the course',
            ], 200);
        }
        return response()->json(['error' => 'There are no cource in the system'], 400);
    }
}
