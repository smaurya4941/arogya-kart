<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,string $role): Response
    {   
        //user logged in hai ya nahi check karna hai
        if(!auth()->check()){
            abort(403,'Unauthorized');
        }

        //user role check karna hai
        if(auth()->user()->role->value !== $role){
            abort(403,'Unauthorized');
        }

        return $next($request);
    }
}
