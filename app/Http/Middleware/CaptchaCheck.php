<?php

namespace App\Http\Middleware;

use App\Services\Web\CaptchaService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaptchaCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        # Если тестовый режим, то не обрабатываем каптчу
        if ( ! app()->isProduction()) {
            return $next($request);
        }

        $data = [
            'captcha' => $request->get('captcha'),
            'ip' => $request->ip()
        ];

        if ( ! (new CaptchaService())->check($data)) {
            return response([
                'success' => false,
                'message' => __('errors.system.captcha')
            ]);
        }

        return $next($request);
    }
}
