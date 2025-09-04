Absolutely. Here’s a **professional, user-friendly README** for **OTPSHIELD**, designed to give a smooth developer experience:

---

# OTPSHIELD

**OTPSHIELD** is a professional, plug-n-play **OTP/TOTP package for Laravel**.
It provides secure, time-based OTPs with:

- Polymorphic OTP storage (supports users, admins, devices, etc.)
- Encrypted secrets
- Middleware for route protection
- SVG QR code generation for Google Authenticator, Authy, etc.
- Artisan commands for management
- Configurable period, digits, and lockout policies

---

## 📦 Installation

Require the package and dependencies via Composer:

```bash
composer require spomky-labs/otphp
composer require paragonie/constant_time_encoding
composer require simplesoftwareio/simple-qrcode
```

Add OTPSHIELD to your Laravel project (if not using auto-discovery):

```php
// config/app.php
'providers' => [
    ...
    OtpShield\OtpShieldServiceProvider::class,
],
'aliases' => [
    ...
    'OtpShield' => OtpShield\Facades\OtpShield::class,
],
```

Publish the configuration and migrations:

```bash
php artisan vendor:publish --provider="OtpShield\OtpShieldServiceProvider" --tag="config"
php artisan migrate
```

---

## ⚙️ Configuration

`config/otp-shield.php` contains:

```php
return [
    'digits' => 6,            // Number of OTP digits
    'period' => 30,           // Validity period in seconds
    'algorithm' => 'sha1',    // Hash algorithm
    'issuer' => env('APP_NAME', 'Laravel App'),
    'max_attempts' => 5,      // Max failed attempts before lockout
    'lockout_time' => 300,    // Lockout duration in seconds
    'default_otp_type' => 'totp',  // allowed totp & hotp - Default : totp
];
```

---

## 🧩 Usage in Models

Add the trait and contract to your User model:

```php
use OtpShield\Traits\HasOtp;
use OtpShield\Contracts\OtpAuthenticatable;

class User extends Authenticatable implements OtpAuthenticatable
{
    use HasOtp;
}
```

---

## 🔑 Enable OTP

```php
$otp = $user->enableOtp();
```

---

## 🖼 Generate QR Code (SVG)

```php
$qrSvg = $user->getOtpQrCode(); // returns SVG string

// Embed in Blade
echo '<div class="otp-qr">'.$qrSvg.'</div>';
```

Or via the facade directly:

```php
use OtpShield\Facades\OtpShield;
$qrSvg = OtpShield::provisioningQr($secret, $user->email, config('otp-shield.issuer'));
```

---

## ✅ Verify OTP

```php
$isValid = $user->verifyOtp('123456'); // true/false
```

---

## 🛡 Middleware Protection

```php
Route::middleware(['auth', \OtpShield\Middleware\EnsureOtpVerified::class])
    ->group(function () {
        Route::get('/secure-data', [SecureDataController::class, 'index']);
    });
```

---

## 🛠 Artisan Commands

- **Enable OTP:**

```bash
php artisan otp-shield:enable {user_id}
```

- **Disable OTP:**

```bash
php artisan otp-shield:disable {user_id}
```

- **Verify OTP manually:**

```bash
php artisan otp-shield:verify {user_id} {code}
```

- **Generate QR code for API / frontend (SVG):**

```bash
php artisan otp-shield:generate-qr {user_id} --file=optional.png
```

---

## 💡 Best Practices

1. **Always encrypt secrets** — OTPSHIELD handles this automatically.
2. **Use middleware** to protect sensitive routes.
3. **Return QR as SVG** in APIs for dynamic frontend rendering.
4. **Monitor failed attempts** to prevent brute-force attacks.

---

## 🧪 Example Workflow

```php
// 1. Enable OTP
$otp = $user->enableOtp();

// 2. Generate QR code for frontend
$qrSvg = $user->getOtpQrCode();

// 3. Display QR code for scanning in app
echo $qrSvg;

// 4. User scans QR in Google Authenticator

// 5. Verify OTP code during login
$isValid = $user->verifyOtp($inputOtp);

if ($isValid) {
    // Grant access
}
```

---

## 🌐 Supported Apps

- Google Authenticator
- Authy
- Microsoft Authenticator
- Any TOTP-compatible app

---

## ⚡ Summary

**OTPSHIELD** makes adding **secure, TOTP-based authentication** to Laravel **fast and reliable**, with minimal setup, modern SVG QR codes, and robust security features.

---
