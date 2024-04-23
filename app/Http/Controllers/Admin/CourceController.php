<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cource;
use Illuminate\Http\Request;

class CourceController extends Controller
{
    public function getData(Request $request)
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


    public function store(Request $request)
    {
        $data = $request->all();
        if(isset($request['cource_image'])){
            $response = cloudinary()->upload($request['cource_image']->getRealPath())->getSecurePath();
        }

        $data["cource_image"] = $response ?? "";
        Cource::create($data);
        return response()->json([
            'message' => 'Successfully added a new cource',
        ], 200);
    }

    public function destroy($id)
    {
        $cources = Cource::where("id", $id)->first();
        if ($cources) {
            $cources->delete();

            return response()->json([
                'message' => 'Successfully delete a cource',
            ], 200);
        }
        return response()->json([
            'error' => "The cource is not correct",
        ], 400);
    }

    public function getDataById($id)
    {
        $cources = Cource::where("id", $id)->first();
        if ($cources) {
            return response()->json([
                'cources'      => $cources,
            ]);
        }
        return response()->json([
            'error' => "The cources is not correct",
        ], 400);
    }


    public function update(Request $request)
    {
        $cources = Cource::where("id", $request->id)->first();
        $data = $request->all();
        $response = "";
        if(isset($request['cource_image'])){
            if(!is_string($request['cource_image'])){
                $response = cloudinary()->upload($request['cource_image']->getRealPath())->getSecurePath();
            }else{
                $response = $request['cource_image'];
            }
        }
        $data["cource_image"] = $response;
        if ($cources) {
            $cources->update($data);
            return response()->json([
                'message' => 'Successfully update a cource',
            ], 200);
        }
        return response()->json([
            'error' => "The cource is not correct",
        ], 400);
    }

    public function updateStatus($id)
    {
        $cource = Cource::where("id", $id)->first();
        if ($cource) {
            if($cource->is_block == 1 && $cource->cource_type == 1 && !$cource->final_excercise){
                return response()->json([
                    'error' => "The course does not have a final exercise",
                ], 400);
            }
            $cource->is_block = !$cource->is_block;
            $cource->save();
            return response()->json([
                'message' => 'Successfully update status a cource',
            ], 200);
        }
        return response()->json([
            'error' => "The cources is not correct",
        ], 400);
    }
}
