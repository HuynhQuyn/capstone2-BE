<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cource;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function getData($id)
    {
        $chapters = Cource::select('chapter')->where("id", $id)->get();

        if ($chapters) {
            return response()->json([
                'chapters'  => $chapters,
            ], 200);
        }
        return response()->json(['error' => 'There are no chapters in the system'], 400);
    }


    public function store(Request $request)
    {
        $chapters = Cource::where("id", $request->id)->first();
        if($chapters){
            $chapters->chapter = $request->chapter;
            $chapters->save();
            return response()->json([
                'message' => 'Successfully added a new chapter',
            ], 200);
        }
        return response()->json(['error' => 'There are no cources in the system'], 400);
    }

    public function update(Request $request)
    {
        $chapters = Cource::where("id", $request->id)->first();
        if($chapters){
            $chapters->chapter = $request->chapter;
            $chapters->save();
            return response()->json([
                'message' => 'Successfully update a chapter',
            ], 200);
        }
        return response()->json([
            'error' => "The cource is not correct",
        ], 400);
    }
}
