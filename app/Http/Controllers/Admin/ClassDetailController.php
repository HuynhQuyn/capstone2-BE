<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassDetail;
use Illuminate\Http\Request;

class ClassDetailController extends Controller
{
    public function getData($id_class)
    {
        $schedules = ClassDetail::orderBy('id', 'DESC')->where('id_class', $id_class)->get();
        if (count($schedules) > 0) {
            return response()->json([
                'schedules'  => $schedules,
            ], 200);
        }
        return response()->json(['error' => 'There are no schedule in the system'], 400);
    }

    public function getDataById($id)
    {
        $schedule = ClassDetail::where("id", $id)->first();
        if ($schedule) {
            return response()->json([
                'schedule'      => $schedule,
            ]);
        }
        return response()->json([
            'error' => "The schedule is not correct",
        ], 400);
    }

    public function update(Request $request)
    {
        $schedule = ClassDetail::where("id", $request->id)->first();
        $data = $request->all();
        if ($schedule) {
            $schedule->update($data);
            return response()->json([
                'message' => 'Successfully update a schedule',
            ], 200);
        }
        return response()->json([
            'error' => "The schedule is not correct",
        ], 400);
    }
}
