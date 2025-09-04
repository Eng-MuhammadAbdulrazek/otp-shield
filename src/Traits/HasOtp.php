<?php

namespace OtpShield\Traits;

use Illuminate\Support\Facades\Crypt;
use OtpShield\Facades\OtpShield;
use OtpShield\Models\Otp;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait HasOtp
{
    public function otps()
    {
        return $this->morphMany(Otp::class, 'otpable');
    }

    public function enableOtp(array $config = [])
    {
        $secret = \ParagonIE\ConstantTimeEncoding\Base32::encodeUpper(random_bytes(20));

        return $this->otps()->create([
            'secret' => Crypt::encryptString($secret),
            'type' => $config['type'] ?? config('otp-shield.default_otp_type'),
            'digits' => $config['digits'] ?? config('otp-shield.digits'),
            'period' => $config['period'] ?? config('otp-shield.period'),
        ]);
    }

    public function disableOtp(): bool
    {
        return $this->otps()->update(['active' => false]);
    }

    public function verifyOtp(string $code): bool
    {
        $otp = $this->otps()->where('active', true)->first();
        if (!$otp)
            return false;

        if ($otp->attempts >= config('otp-shield.max_attempts')) {
            return false;
        }

        $secret = Crypt::decryptString($otp->secret);

        $valid = app('otpshield')->verify($code, $secret, $otp->period, $otp->digits);

        if ($valid) {
            $otp->update(['attempts' => 0, 'last_used_at' => now()]);
        } else {
            $otp->increment('attempts');
        }

        return $valid;
    }

    public function otpVerified(): bool
    {
        return $this->otps()->where('active', true)->whereNotNull('last_used_at')->exists();
    }

    /**
     * Generate OTP QR code as SVG string (for Google Authenticator / Authy)
     */
    public function getOtpQrCode(): ?string
    {
        $otp = $this->otps()->where('active', true)->first();
        if (!$otp)
            return null;

        $secret = Crypt::decryptString($otp->secret);

        $uri = OtpShield::provisioningUri(
            $secret,
            $this->email ?? $this->name ?? 'user',
            config('otp-shield.issuer')
        );

        // Generate QR code as SVG string
        return QrCode::format('svg')->size(300)->generate($uri);
    }
}
