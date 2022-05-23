<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InbentaAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->header('sessionID')) {
            Session::setId($request->header('sessionID'));
            Session::start();
            if (Session::has('accessToken') && 
            Carbon::now()->timestamp < Session('expiration') ) {
                return $next($request);
            } else {
                try {
                    $this->auth($request);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
                return $next($request);
            }
        } else {
            try {
                $this->auth($request);
                return $next($request);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    private function auth($request)
    {
        $data = ['secret' => env('API_SECRET')];
        $headers = ['x-inbenta-key' => $request->header('api-key')];
        $response = Http::withHeaders($headers)->post(env('API_AUTH_URL'), $data);
        $body = json_decode($response->getBody());
        Session::put('accessToken', $body->accessToken);
        Session::put('expiration', $body->expiration);
        Session::put('chatbotApi', $body->apis->chatbot);
    }
}
