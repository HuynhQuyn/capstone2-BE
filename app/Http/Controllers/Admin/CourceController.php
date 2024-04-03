<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cource;
use Illuminate\Http\Request;

class CourceController extends Controller
{
    public function getData()
    {
        $cources = Cource::select('cources.*')
            ->orderBy('id', 'DESC')
            ->paginate(5);
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
        if(isset($request['image'])){
            $response = cloudinary()->upload($request['image']->getRealPath())->getSecurePath();
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
        $football_pitchs = Cource::where("id", $request->id)->first();
        $data = $request->all();
        if(isset($request['image'])){
            if(!is_string($request['image'])){
                $response = cloudinary()->upload($request['image']->getRealPath())->getSecurePath();
            }else{
                $response = $request['image'];
            }
        }
        $data["image"] = $response;
        if ($football_pitchs) {
            $football_pitchs->update($data);
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
        $cources = Cource::where("id", $id)->first();
        if ($cources) {
            $cources->is_block = !$cources->is_block;
            $cources->save();
            return response()->json([
                'message' => 'Successfully update status a cource',
            ], 200);
        }
        return response()->json([
            'error' => "The cources is not correct",
        ], 400);
    }
}
