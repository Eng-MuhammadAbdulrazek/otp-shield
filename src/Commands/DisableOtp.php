<?php

namespace OtpShield\Commands;

use Illuminate\Console\Command;

class DisableOtp extends Command
{
    protected $signature = 'otp-shield:disable {user_id}';
    protected $description = 'Disable OTP for a user';

    public function handle()
    {
        // Get the user model from OTPSHIELD config
        $userModel = config('otp-shield.user_model');
        $userId = $this->argument('user_id');

        $user = $userModel::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $user->disableOtp();

        $this->info("OTP disabled for user {$user->email}.");
        return 0;
    }
}
