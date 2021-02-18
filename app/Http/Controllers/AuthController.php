<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\Redis;
use Concerns\InteractsWithInput;

use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request) {

        $data = $request->input();

        if(!isset($data['email']) || !isset($data['password'])) {

            return response()->json(['message' => 'Usuário ou senha não informado'], 400);
        }


        $user = DB::table('users')
            ->where('email', $data['email'])
            ->first();
        
             
        if(!isset($user)) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $verificaPassword = Hash::check($data['password'], $user->password);

        if($verificaPassword) {
            $keysToken = [
                'id' => $user->id,
                'nome' => $user->nome,
                'cpf' => $user->cpf,
                'datanascimento' => $user->datanascimento,
                'email' => $user->email,
                'cep' => $user->cep,
                'rua' => $user->rua,
                'numero' => $user->numero,
                'bairro' => $user->bairro,
                'cidade' => $user->cidade,
                'estado' => $user->estado


            ];
           
            
            $chaves = JWTFactory::customClaims( $keysToken);
            $payload = JWTFactory::make($chaves);
            $token = JWTAuth::encode($payload);

            $token = $token->get();

            $dataUser = collect([
                'id' => $user->id,
                'nome' => $user->nome,
                'cpf' => $user->cpf,
                'datanascimento' => $user->datanascimento,
                'email' => $user->email,
                'cep' => $user->cep,
                'rua' => $user->rua,
                'numero' => $user->numero,
                'bairro' => $user->bairro,
                'cidade' => $user->cidade,
                'estado' => $user->estado,
                'token' => $token

            ]);


            Redis::hmset($token, 'dados', json_encode($dataUser));

            return response()->json($dataUser);
        }
            return response()->json(['message' => 'Senha incorreta'], 400);
    }
}
