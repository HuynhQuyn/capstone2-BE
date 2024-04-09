<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cource;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function getData(Request $request)
    {
        $lessons = Lesson::where('lessons.id_cource', $request->id_cource)
                            ->where("lessons.id_chapter", $request->id_chapter)
                            ->orderBy('lessons.id', 'DESC')->paginate(5);
        if (count($lessons) > 0) {
            return response()->json([
                'lessons'  => $lessons,
            ], 200);
        }
        return response()->json(['error' => 'There are no lessons in the system'], 400);
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $cource = Cource::where('id', $request->id_cource)->first();

        $check = false;
        if($cource){
            $chapters = json_decode($cource->chapter);
            foreach($chapters as $value){
                if ($value->id == $request->id_chapter){
                    $check = true;
                };
            }
        }

        if($check){
            if(isset($request['lesson_video'])){
                $response = cloudinary()->uploadVideo($request['lesson_video']->getRealPath())->getSecurePath();
            }

            $data["lesson_video"] = $response ?? "";
            Lesson::create($data);
            return response()->json([
                'message' => 'Successfully added a new lesson',
            ], 200);
        }
        return response()->json([
            'error' => "The cource or chapter is not correct",
        ], 400);
    }

    public function destroy($id)
    {
        $lessons = Lesson::where("id", $id)->first();
        if ($lessons) {
            $lessons->delete();

            return response()->json([
                'message' => 'Successfully delete a lesson',
            ], 200);
        }
        return response()->json([
            'error' => "The lesson is not correct",
        ], 400);
    }

    public function getDataById($id)
    {
        $lessons = Lesson::where("id", $id)->first();
        if ($lessons) {
            return response()->json([
                'lesson'      => $lessons,
            ]);
        }
        return response()->json([
            'error' => "The lesson is not correct",
        ], 400);
    }


    public function update(Request $request)
    {
        $lesson = Lesson::where("id", $request->id)->first();
        $data = $request->all();
        $response = "";
        if(isset($request['lesson_video'])){
            if(!is_string($request['lesson_video'])){
                $response = cloudinary()->upload($request['lesson_video']->getRealPath())->getSecurePath();
            }else{
                $response = $request['lesson_video'];
            }
        }
        $data["lesson_video"] = $response;
        if ($lesson) {
            $lesson->update($data);
            return response()->json([
                'message' => 'Successfully update a lesson',
            ], 200);
        }
        return response()->json([
            'error' => "The lesson is not correct",
        ], 400);
    }
}
