<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Comprobar que el usuario este identificado
        $token=$request->header('Authorization');
        $jwtAuth=new \JwtAuth();
        $checkToken=$jwtAuth->checkToken($token);

        if($checkToken){
            return $next($request);
        }else{
            $data=array(
               'code'   =>400,
               'status' =>'error',
               'message'=>'El usuario NO esta identificado'
           );
            return response()->json($data, $data['code']);
        }
        
    }
}
