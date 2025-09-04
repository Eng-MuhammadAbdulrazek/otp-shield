<?php

namespace OtpShield\Services;

use OTPHP\TOTP;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OtpService
{
    /**
     * Generate a TOTP code (current valid code)
     */
    public function generate(string $secret, int $period = 30, int $digits = 6): string
    {
        $totp = TOTP::create($secret, $period, 'sha1', $digits);
        return $totp->now();
    }

    /**
     * Verify a TOTP code
     */
    public function verify(string $code, string $secret, int $period = 30, int $digits = 6): bool
    {
        $totp = TOTP::create($secret, $period, 'sha1', $digits);
        return $totp->verify($code);
    }

    /**
     * Generate provisioning URI for a TOTP
     */
    public function provisioningUri(string $secret, string $label, string $issuer): string
    {
        $totp = TOTP::create($secret);
        $totp->setLabel($label);
        $totp->setIssuer($issuer);

        return $totp->getProvisioningUri();
    }

    /**
     * Generate QR code as SVG string for a TOTP secret
     */
    public function provisioningQr(string $secret, string $label, string $issuer): string
    {
        $uri = $this->provisioningUri($secret, $label, $issuer);

        return QrCode::format('svg')->size(300)->generate($uri);
    }
}
