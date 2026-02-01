<?php
// COMANDO PARA CREAR ESTE ARCHIVO DE MIDDLEWARE: php artisan make:middleware NombreDelMiddleware
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Autenticacion extends Middleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    public function handle($request, Closure $next, ...$guards)
    {
        if ($token = $request->cookie('cookie_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }
        $this->authenticate($request, $guards);
        return $next($request);
    }

    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
}
