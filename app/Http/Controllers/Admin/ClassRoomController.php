<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassDetail;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $class = ClassRoom::create($data);
        $schedules = json_decode($data['schedules']);
        foreach($schedules as $value){
            ClassDetail::create([
                'title' => $value->title,
                'date' => $value->date,
                'id_class' => $class->id
            ]);
        }
        return response()->json([
            'message' => 'Successfully added a new class',
        ], 200);
    }

    public function getData(Request $request)
    {
        $classes = ClassRoom::select('class_rooms.*')
            ->where('id_cource', $request->id_cource)
            ->orderBy('id', 'DESC');
        if($request->class_name != ""){
            $classes = $classes->where('class_name', 'like' , '%' . $request->class_name  . '%');
        }
        $classes = $classes->paginate(5);
        if (count($classes) > 0) {
            return response()->json([
                'classes'  => $classes,
            ], 200);
        }
        return response()->json(['error' => 'There are no class in the system'], 400);
    }

    public function destroy($id)
    {
        $class = ClassRoom::where("id", $id)->first();
        if ($class) {
            $class->delete();
            ClassDetail::where("id_class", $id)->delete();
            return response()->json([
                'message' => 'Successfully delete a class',
            ], 200);
        }
        return response()->json([
            'error' => "The class is not correct",
        ], 400);
    }

    public function getDataById($id)
    {
        $class = ClassRoom::where("id", $id)->first();
        if ($class) {
            return response()->json([
                'class'      => $class,
            ]);
        }
        return response()->json([
            'error' => "The class is not correct",
        ], 400);
    }

    public function update(Request $request)
    {
        $class = ClassRoom::where("id", $request->id)->first();
        $data = $request->all();
        if ($class) {
            if ($data['updated_duration']) {
                ClassDetail::where("id_class", $request->id)->delete();

                $schedules = json_decode($data['schedules']);
                foreach($schedules as $value){
                    ClassDetail::create([
                        'title' => $value->title,
                        'date' => $value->date,
                        'id_class' => $class->id
                    ]);
                }
            }
            $class->update($data);
            return response()->json([
                'message' => 'Successfully update a class',
            ], 200);
        }
        return response()->json([
            'error' => "The class is not correct",
        ], 400);
    }
}
