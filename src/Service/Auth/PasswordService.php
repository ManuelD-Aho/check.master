<?php
declare(strict_types=1);

namespace App\Service\Auth;

use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordService
{
    private PasswordHasherFactory $hasherFactory;

    public function __construct(PasswordHasherFactory $hasherFactory)
    {
        $this->hasherFactory = $hasherFactory;
    }

    public function hash(string $password): string
    {
        return $this->getHasher()->hash($password);
    }

    public function verify(string $password, string $hash): bool
    {
        return $this->getHasher()->verify($hash, $password);
    }

    public function needsRehash(string $hash): bool
    {
        return $this->getHasher()->needsRehash($hash);
    }

    public function generateSecurePassword(int $length = 16): string
    {
        if ($length <= 0) {
            throw new InvalidArgumentException('Password length must be positive.');
        }

        $length = max(8, $length);

        $sets = [
            'lower' => 'abcdefghijklmnopqrstuvwxyz',
            'upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'digit' => '0123456789',
            'symbol' => '!@#$%^&*()-_=+[]{}<>?'
        ];

        $all = implode('', $sets);
        $chars = [];

        foreach ($sets as $set) {
            $chars[] = $set[random_int(0, strlen($set) - 1)];
        }

        for ($i = count($chars); $i < $length; $i++) {
            $chars[] = $all[random_int(0, strlen($all) - 1)];
        }

        $this->secureShuffle($chars);

        return implode('', $chars);
    }

    public function validateStrength(string $password): array
    {
        $errors = [];
        $score = 0;

        if (strlen($password) < 8) {
            $errors[] = 'min_length';
        } else {
            $score++;
        }

        if (preg_match('/[a-z]/', $password)) {
            $score++;
        } else {
            $errors[] = 'lowercase';
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        } else {
            $errors[] = 'uppercase';
        }

        if (preg_match('/\d/', $password)) {
            $score++;
        } else {
            $errors[] = 'digit';
        }

        if (preg_match('/[^a-zA-Z\d]/', $password)) {
            $score++;
        } else {
            $errors[] = 'symbol';
        }

        return [
            'valid' => $errors === [],
            'score' => $score,
            'errors' => $errors
        ];
    }

    private function getHasher(): PasswordHasherInterface
    {
        return $this->hasherFactory->getPasswordHasher('default');
    }

    private function secureShuffle(array &$items): void
    {
        $count = count($items);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $temp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $temp;
        }
    }
}
