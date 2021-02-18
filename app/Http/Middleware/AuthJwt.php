<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Redis;
use App\Http\Lib\SetDatabase;

class AuthJwt extends BaseMiddleware {

    public function handle( $request, Closure $next ) {
        $token = null;

        if ( strpos( $request->header( 'Authorization' ), 'bearer' ) !== false ) {
            $type = 'bearer';
        } elseif ( strpos( $request->header( 'Authorization' ), 'Bearer' ) !== false ) {
            $type = 'Bearer';
        } else {
            return response()->json( [
                'message' => 'Header invalid'
            ], 401 );
        }

        $token = str_replace( "$type ", '', $request->header( 'Authorization' ) );

        try {
            JWTAuth::setToken( $token );

            if ( ! JWTAuth::getPayload() ) {
                return response()->json( [
                    'message' => 'User not found'
                ], 401 );
            }

        } catch ( TokenExpiredException $e ) {
            return response()->json( [
                'message' => 'Token expired',
                'error' => $e
            ], 401 );

        } catch ( TokenInvalidException $e ) {
            return response()->json( [
                'message' => 'Invalid token',
                'error' => $e
            ], 401 );

        } catch ( JWTException $e ) {
            return response()->json( [
                'message' => 'Token not provided',
                'error' => $e
            ], 401 );

        } catch ( TokenBlacklistedException $e ) {
            return response()->json( [
                'message' => 'Token blacklisted',
                'error' => $e
            ], 401 );

        }

        $sessao = Redis::hgetall( $token );

        if ( $sessao ) {
            new setDatabase( $request );
            Redis::expire($token, 1800);
            return $next( $request );
        }
        $payload = JWTAuth::getPayload()->toArray();
        Redis::hmset( $token, 'dados', json_encode( $payload ) );
        Redis::expire($token, 1800);
        //new setDatabase( $request );

        return $next( $request );
    }

}