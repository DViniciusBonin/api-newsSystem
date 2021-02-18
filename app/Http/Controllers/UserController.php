<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Telefone;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request) {
        $data = $request->input();
        $cpf =  preg_replace( '/[^0-9]/', '', $data['cpf'] );
        $data['cep'] =  preg_replace( '/[^0-9]/', '', $data['cep'] );
        $password = Hash::make($data['password']);


        $user = new User();
        $user->nome = $data['nome'];
        $user->cpf = $cpf;
        $user->datanascimento = $data['datanascimento'];
        $user->password = $password;
        $user->email = $data['email'];
        $user->cep = $data['cep'];
        $user->rua = $data['rua'];
        $user->numero = $data['numero'];
        $user->bairro = $data['bairro'];
        $user->cidade = $data['cidade'];
        $user->estado = $data['estado'];
        

        $insertUser = $user->save();

        if(!isset($insertUser)) {
            return response()->json(['message' => 'Dados incompativeis'], 400);
        }

        return response()->json(['message' => true], 201);


    }
}
