<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $appointments = Cita::where('docUsuario', $user->docUsuario)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json([
            'appointments' => $appointments
        ]);
    }
}
