<?php

namespace OtpShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use OtpShield\Facades\OtpShield;

class EnableOtp extends Command
{
    protected $signature = 'otp-shield:enable {user_id}';
    protected $description = 'Enable OTP for a user';

    public function handle()
    {
        $userModel = config('otp-shield.user_model');
        $userId = $this->argument('user_id');

        $user = $userModel::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $otp = $user->enableOtp();

        $uri = OtpShield::provisioningUri(
            Crypt::decryptString($otp->secret),
            $user->email,
            config('otp-shield.issuer')
        );

        $this->info("OTP enabled for user {$user->email}.");
        $this->line("Provisioning URI: {$uri}");

        return 0;
    }
}
