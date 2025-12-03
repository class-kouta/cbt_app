<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 環境変数 USE_BASIC_AUTH が true でない場合は認証をスキップ
        if (! filter_var(env('USE_BASIC_AUTH', false), FILTER_VALIDATE_BOOLEAN)) {
            return $next($request);
        }

        $username = env('BASIC_AUTH_USERNAME');
        $password = env('BASIC_AUTH_PASSWORD');

        // 環境変数が設定されていない場合はエラーを返す
        if (empty($username) || empty($password)) {
            // 本番環境など認証有効下での設定漏れを防ぐためエラーとする
            return response('Basic authentication is enabled but credentials are not configured.', 500);
        }

        // HTTP Basic認証の検証
        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Restricted Area"',
            ]);
        }

        return $next($request);
    }
}
