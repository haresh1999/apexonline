<?php

namespace App\Http\Middleware;

use App\Models\Transaction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TnxLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = config('services.user');

        if ($user['is_limit'] == 1) {

            $alreadyPaid = Transaction::whereDate('created_at', today())
                ->where('status', 'completed')
                ->where('env', 'production')
                ->where(function ($query) {
                    $query->where('payer_email', request('payer_email'))
                        ->orWhere('payer_mobile', request('payer_mobile'));
                })
                ->count();

            if ($alreadyPaid >= $user['limit_count']) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached your daily payment limit.'
                ], 422);
            }
        }

        return $next($request);
    }
}
