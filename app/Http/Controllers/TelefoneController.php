<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;
use app\Models\Telefone;

class TelefoneController extends Controller
{
    public function store(Request $request) {
        $data = $request->input();

        $telefone = new Telefone();

        $telefone->telefone = $data['telefone'];

        $insertTelefone = $telefone->save();

        if(!isset($insertTelefone)) {
            return response()->json(['message' => 'Dados incompativeis'], 400);
        }

        return response()->json($telefone);


    }
}
