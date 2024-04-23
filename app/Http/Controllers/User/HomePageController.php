<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cource;
use App\Models\Lesson;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function listCource(Request $request)
    {
        $cources = Cource::select('cources.*')
            ->orderBy('id', 'DESC');
        if($request->cource_name != ""){
            $cources = $cources->where('cource_name', 'like' , '%' . $request->cource_name  . '%');
        }
        $cources = $cources->paginate(5);
        if (count($cources) > 0) {
            return response()->json([
                'cources'  => $cources,
            ], 200);
        }
        return response()->json(['error' => 'There are no cources in the system'], 400);
    }

    public function listLesson(Request $request)
    {
        $lessons = Lesson::where('lessons.id_cource', $request->id_cource)
                            ->where("lessons.id_chapter", $request->id_chapter)
                            ->orderBy('lessons.id', 'DESC')->get();
        if (count($lessons) > 0) {
            return response()->json([
                'lessons'  => $lessons,
            ], 200);
        }
        return response()->json(['error' => 'There are no lessons in the system'], 400);
    }
}
