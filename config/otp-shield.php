<?php

return [
    /*
     * |--------------------------------------------------------------------------
     * | OTP Digits
     * |--------------------------------------------------------------------------
     * | Number of digits in the OTP code.
     * | Default is 6 (e.g., 123456).
     */
    'digits' => 6,

    /*
     * |--------------------------------------------------------------------------
     * | OTP Period (seconds)
     * |--------------------------------------------------------------------------
     * | The validity period of a single TOTP code in seconds.
     * | Default is 30 seconds, which is standard for most authenticator apps.
     */
    'period' => 30,

    /*
     * |--------------------------------------------------------------------------
     * | OTP Issuer Name
     * |--------------------------------------------------------------------------
     * | This will appear in the authenticator app (e.g., Google Authenticator)
     * | to identify your application. Defaults to your APP_NAME from .env
     */
    'issuer' => env('APP_NAME', 'Laravel App'),

    /*
     * |--------------------------------------------------------------------------
     * | Maximum Attempts
     * |--------------------------------------------------------------------------
     * | Number of failed OTP attempts allowed before the account/device is
     * | temporarily locked. Helps prevent brute-force attacks.
     */
    'max_attempts' => 3,

    /*
     * |--------------------------------------------------------------------------
     * | Lockout Time (seconds)
     * |--------------------------------------------------------------------------
     * | Duration to lock the OTP after exceeding max_attempts.
     * | Default is 300 seconds (5 minutes).
     */
    'lockout_time' => 300,

    /*
     * |--------------------------------------------------------------------------
     * | Default OTP Type
     * |--------------------------------------------------------------------------
     * | Currently only 'totp' (time-based) is supported.
     * | HOTP (counter-based) support will be added in the future.
     */
    'default_otp_type' => 'totp',

    /*
     * |--------------------------------------------------------------------------
     * | User Model
     * |--------------------------------------------------------------------------
     * | Define the User model that OTPSHIELD should use.
     * | Can be customized if your user model is not App\Models\User.
     */
    'user_model' => App\Models\User::class,
];
