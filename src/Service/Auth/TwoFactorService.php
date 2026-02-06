<?php
declare(strict_types=1);

namespace App\Service\Auth;

use App\Service\System\EncryptionService;
use InvalidArgumentException;
use Throwable;

class TwoFactorService
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function generateSecret(): string
    {
        $totpClass = $this->getTotpClass();
        $totp = $totpClass::create();
        $secret = $totp->getSecret();

        return $this->encryptSecret($secret);
    }

    public function generateQrCodeUri(string $secret, string $email, string $issuer = 'MIAGE-GI'): string
    {
        $plainSecret = $this->decryptSecret($secret);
        $totpClass = $this->getTotpClass();
        $totp = $totpClass::create($plainSecret);
        $totp->setLabel($email);
        $totp->setIssuer($issuer);

        return $totp->getProvisioningUri();
    }

    public function verifyCode(string $secret, string $code): bool
    {
        $plainSecret = $this->decryptSecret($secret);
        $totpClass = $this->getTotpClass();
        $totp = $totpClass::create($plainSecret);

        return $totp->verify($code);
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        if ($count < 1) {
            throw new InvalidArgumentException('Recovery code count must be positive.');
        }

        $codes = [];

        while (count($codes) < $count) {
            $code = $this->generateRecoveryCode();
            if (!in_array($code, $codes, true)) {
                $codes[] = $code;
            }
        }

        return $codes;
    }

    public function verifyRecoveryCode(string $code, array $validCodes): bool
    {
        $code = trim($code);

        foreach ($validCodes as $validCode) {
            if (is_string($validCode) && hash_equals($validCode, $code)) {
                return true;
            }
        }

        return false;
    }

    private function generateRecoveryCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $length = 10;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $code;
    }

    private function encryptSecret(string $secret): string
    {
        try {
            if (method_exists($this->encryptionService, 'encrypt')) {
                return (string)$this->encryptionService->encrypt($secret);
            }
        } catch (Throwable) {
        }

        return $secret;
    }

    private function decryptSecret(string $secret): string
    {
        try {
            if (method_exists($this->encryptionService, 'decrypt')) {
                return (string)$this->encryptionService->decrypt($secret);
            }
        } catch (Throwable) {
        }

        return $secret;
    }

    private function getTotpClass(): string
    {
        $class = 'OTPHP\\TOTP';
        if (!class_exists($class)) {
            throw new InvalidArgumentException('TOTP library is not available.');
        }

        return $class;
    }
}
