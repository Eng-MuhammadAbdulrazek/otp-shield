<?php

namespace OtpShield\Contracts;

interface OtpAuthenticatable
{
    public function otps();
    public function enableOtp(array $config = []);
    public function disableOtp(): bool;
    public function verifyOtp(string $code): bool;
    public function otpVerified(): bool;
    public function getOtpQrCode(): ?string;
}
