<?php

namespace OtpShield\Traits;

use Illuminate\Support\Facades\Crypt;
use OtpShield\Facades\OtpShield;
use OtpShield\Models\Otp;
use ParagonIE\ConstantTime\Base32;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait HasOtp
{
    /**
     * Polymorphic relationship to OTP records.
     *
     * Allows multiple OTPs to be associated with the model (future-proof for HOTP or multi-device).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function otps()
    {
        return $this->morphMany(Otp::class, 'otpable');
    }

    /**
     * Check if OTP is enabled for this model.
     *
     * Returns true if at least one active OTP exists.
     *
     * @return bool
     */
    public function isOtpEnabled(): bool
    {
        return $this->otps()->where('active', true)->exists();
    }

    /**
     * Enable OTP for the model.
     *
     * Generates a new secret and creates an OTP record.
     *
     * @param array $config Optional configuration (type, digits, period)
     * @return Otp
     */
    public function enableOtp(array $config = [])
    {
        $secret = Base32::encodeUpper(random_bytes(20));

        return $this->otps()->create([
            'secret' => Crypt::encryptString($secret),
            'type' => $config['type'] ?? config('otp-shield.default_otp_type'),
            'digits' => $config['digits'] ?? config('otp-shield.digits'),
            'period' => $config['period'] ?? config('otp-shield.period'),
            'active' => true,
            'attempts' => 0,
        ]);
    }

    /**
     * Disable OTP for the model.
     *
     * Marks all OTPs as inactive.
     *
     * @return bool
     */
    public function disableOtp(): bool
    {
        return $this->otps()->update(['active' => false]);
    }

    /**
     * Verify a given OTP code.
     *
     * Checks active OTPs, enforces max attempts, and updates last_used_at on success.
     *
     * @param string $code
     * @return bool
     */
    public function verifyOtp(string $code): bool
    {
        $otp = $this->otps()->where('active', true)->first();
        if (!$otp)
            return false;

        if ($otp->attempts >= config('otp-shield.max_attempts')) {
            return false;  // account/device locked
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

    /**
     * Check if OTP has been verified in the past.
     *
     * Useful for middleware to allow access to protected routes.
     *
     * @return bool
     */
    public function otpVerified(): bool
    {
        return $this
            ->otps()
            ->where('active', true)
            ->whereNotNull('last_used_at')
            ->exists();
    }

    /**
     * Generate OTP QR code as SVG string.
     *
     * Can be rendered in views for Google Authenticator, Authy, etc.
     *
     * @return string|null SVG string or null if no active OTP exists
     */
    public function getOtpQrCode($size = 300): ?string
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

        return QrCode::format('svg')->size($size)->generate($uri);
    }
}
