<?php

namespace OtpShield\Middleware;

use Closure;

class EnsureOtpVerified
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->otpVerified()) {
            return response()->json(['message' => 'OTP verification required.'], 403);
        }

        return $next($request);
    }
}
