<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function getData(Request $request)
    {
        $questions = Question::orderBy('id', 'DESC');
        if($request->question_name != ""){
            $questions = $questions->where('question_name', 'like' , '%' . $request->question_name  . '%');
        }
        $questions = $questions->paginate(5);
        if (count($questions) > 0) {
            return response()->json([
                'questions'  => $questions,
            ], 200);
        }
        return response()->json(['error' => 'There are no questions in the system'], 400);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Question::create($data);
        return response()->json([
            'message' => 'Successfully added a new question',
        ], 200);
    }

    public function destroy($id)
    {
        $question = Question::where("id", $id)->first();
        if ($question) {
            $question->delete();

            return response()->json([
                'message' => 'Successfully delete a question',
            ], 200);
        }
        return response()->json([
            'error' => "The question is not correct",
        ], 400);
    }

    public function getDataById($id)
    {
        $question = Question::where("id", $id)->first();
        if ($question) {
            return response()->json([
                'question'      => $question,
            ]);
        }
        return response()->json([
            'error' => "The question is not correct",
        ], 400);
    }

    public function update(Request $request)
    {
        $question = Question::where("id", $request->id)->first();
        $data = $request->all();
        if ($question) {
            $question->update($data);
            return response()->json([
                'message' => 'Successfully update a question',
            ], 200);
        }
        return response()->json([
            'error' => "The question is not correct",
        ], 400);
    }

    public function getList()
    {
        $questions = Question::orderBy('id', 'DESC')->get();
        if (count($questions) > 0) {
            return response()->json([
                'questions'  => $questions,
            ], 200);
        }
        return response()->json(['error' => 'There are no questions in the system'], 400);
    }
}
