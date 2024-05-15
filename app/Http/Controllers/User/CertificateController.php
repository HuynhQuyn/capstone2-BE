<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function getListCertificate()
    {
        $user = auth()->user();
        $certificates = Participant::where('participants.user_id', $user->id)
                                ->where('participants.is_certificate', 1)
                                ->join('cources', 'cources.id', 'participants.cource_id')
                                ->select('cources.cource_name', 'participants.*')
                                ->get();

        if (count($certificates) > 0) {
            return response()->json([
                'certificates'  => $certificates,
            ], 200);
        }
        return response()->json(['error' => 'There are no certificate in the system'], 400);
    }
}
