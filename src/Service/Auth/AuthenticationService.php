<?php
declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User\Utilisateur;
use App\Entity\User\UtilisateurStatut;
use App\Repository\User\UtilisateurRepository;
use DateInterval;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class AuthenticationService
{
    private UtilisateurRepository $utilisateurRepository;
    private PasswordService $passwordService;
    private JwtService $jwtService;
    private TwoFactorService $twoFactorService;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UtilisateurRepository $utilisateurRepository,
        PasswordService $passwordService,
        JwtService $jwtService,
        TwoFactorService $twoFactorService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->passwordService = $passwordService;
        $this->jwtService = $jwtService;
        $this->twoFactorService = $twoFactorService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function authenticate(string $login, string $password): ?array
    {
        try {
            $user = $this->findUserByLogin($login);

            if ($user === null) {
                return null;
            }

            if (!$this->isUserActive($user)) {
                return null;
            }

            if (!$this->passwordService->verify($password, $user->getMotDePasseHash())) {
                $this->registerFailedLogin($user);
                return null;
            }

            $this->registerSuccessfulLogin($user);

            $payload = [
                'sub' => $user->getIdUtilisateur(),
                'login' => $user->getLoginUtilisateur()
            ];

            $token = $this->jwtService->encode($payload);
            $payload = $this->jwtService->getPayload($token) ?? $payload;

            return [
                'user' => $user,
                'token' => $token,
                'expires_at' => $payload['exp'] ?? null,
                'requires_2fa' => $user->is2faEnabled()
            ];
        } catch (Throwable) {
            return null;
        }
    }

    public function validateSession(string $token): ?Utilisateur
    {
        $payload = $this->jwtService->getPayload($token);

        if ($payload === null || !isset($payload['sub'])) {
            return null;
        }

        $userId = (int)$payload['sub'];

        return $this->utilisateurRepository->find($userId);
    }

    public function logout(int $userId): void
    {
        try {
            $this->eventDispatcher->dispatch((object)['event' => 'auth.logout', 'user_id' => $userId]);
        } catch (Throwable) {
        }
    }

    public function initiatePasswordReset(string $email): ?string
    {
        try {
            $user = $this->utilisateurRepository->findOneBy(['emailUtilisateur' => $email]);

            if (!$user instanceof Utilisateur) {
                return null;
            }

            $token = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
            $expiresAt = (new DateTimeImmutable())->add(new DateInterval('PT1H'));

            $user->setTokenReinitialisation($token)
                ->setExpirationToken($expiresAt)
                ->setDateModification(new DateTimeImmutable());

            $this->utilisateurRepository->save($user);

            return $token;
        } catch (Throwable) {
            return null;
        }
    }

    public function completePasswordReset(string $token, string $newPassword): bool
    {
        try {
            $user = $this->utilisateurRepository->findOneBy(['tokenReinitialisation' => $token]);

            if (!$user instanceof Utilisateur) {
                return false;
            }

            $expiresAt = $user->getExpirationToken();
            if ($expiresAt === null || $expiresAt < new DateTimeImmutable()) {
                return false;
            }

            $user->setMotDePasseHash($this->passwordService->hash($newPassword))
                ->setTokenReinitialisation(null)
                ->setExpirationToken(null)
                ->setTentativesConnexion(0)
                ->setDateBlocage(null)
                ->setDateModification(new DateTimeImmutable());

            $this->utilisateurRepository->save($user);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        try {
            $user = $this->utilisateurRepository->find($userId);

            if (!$user instanceof Utilisateur) {
                return false;
            }

            if (!$this->passwordService->verify($currentPassword, $user->getMotDePasseHash())) {
                return false;
            }

            $strength = $this->passwordService->validateStrength($newPassword);
            if ($strength['valid'] !== true) {
                return false;
            }

            $user->setMotDePasseHash($this->passwordService->hash($newPassword))
                ->setDateModification(new DateTimeImmutable());

            $this->utilisateurRepository->save($user);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function verify2FA(int $userId, string $code): bool
    {
        try {
            $user = $this->utilisateurRepository->find($userId);

            if (!$user instanceof Utilisateur) {
                return false;
            }

            if (!$user->is2faEnabled() || $user->getSecret2fa() === null) {
                return false;
            }

            $recoveryCodes = $this->decodeRecoveryCodes($user->getCodesRecuperation2fa());

            if ($this->twoFactorService->verifyRecoveryCode($code, $recoveryCodes)) {
                $remaining = array_values(array_filter(
                    $recoveryCodes,
                    static fn (string $stored) => !hash_equals($stored, $code)
                ));

                $user->setCodesRecuperation2fa($this->encodeRecoveryCodes($remaining))
                    ->setDateModification(new DateTimeImmutable());

                $this->utilisateurRepository->save($user);

                return true;
            }

            return $this->twoFactorService->verifyCode($user->getSecret2fa(), $code);
        } catch (Throwable) {
            return false;
        }
    }

    public function enable2FA(int $userId): array
    {
        try {
            $user = $this->utilisateurRepository->find($userId);

            if (!$user instanceof Utilisateur) {
                return [];
            }

            $secret = $this->twoFactorService->generateSecret();
            $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

            $user->setSecret2fa($secret)
                ->setIs2faEnabled(true)
                ->setCodesRecuperation2fa($this->encodeRecoveryCodes($recoveryCodes))
                ->setDateModification(new DateTimeImmutable());

            $this->utilisateurRepository->save($user);

            return [
                'qr_uri' => $this->twoFactorService->generateQrCodeUri($secret, $user->getEmailUtilisateur()),
                'recovery_codes' => $recoveryCodes
            ];
        } catch (Throwable) {
            return [];
        }
    }

    public function disable2FA(int $userId, string $code): bool
    {
        try {
            $user = $this->utilisateurRepository->find($userId);

            if (!$user instanceof Utilisateur) {
                return false;
            }

            if (!$this->verify2FA($userId, $code)) {
                return false;
            }

            $user->setIs2faEnabled(false)
                ->setSecret2fa(null)
                ->setCodesRecuperation2fa(null)
                ->setDateModification(new DateTimeImmutable());

            $this->utilisateurRepository->save($user);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function getUserById(int $userId): ?Utilisateur
    {
        $user = $this->utilisateurRepository->find($userId);
        return $user instanceof Utilisateur ? $user : null;
    }

    private function findUserByLogin(string $login): ?Utilisateur
    {
        $user = $this->utilisateurRepository->findOneBy(['loginUtilisateur' => $login]);

        if ($user instanceof Utilisateur) {
            return $user;
        }

        $user = $this->utilisateurRepository->findOneBy(['emailUtilisateur' => $login]);

        return $user instanceof Utilisateur ? $user : null;
    }

    private function isUserActive(Utilisateur $user): bool
    {
        if ($user->getStatutUtilisateur() !== UtilisateurStatut::Actif) {
            return false;
        }

        $blockedAt = $user->getDateBlocage();
        if ($blockedAt !== null && $blockedAt > new DateTimeImmutable()) {
            return false;
        }

        return true;
    }

    private function registerFailedLogin(Utilisateur $user): void
    {
        $attempts = $user->getTentativesConnexion() + 1;
        $user->setTentativesConnexion($attempts)
            ->setDateModification(new DateTimeImmutable());

        if ($attempts >= 5) {
            $user->setDateBlocage((new DateTimeImmutable())->add(new DateInterval('PT15M')));
        }

        $this->utilisateurRepository->save($user);
    }

    private function registerSuccessfulLogin(Utilisateur $user): void
    {
        $user->setTentativesConnexion(0)
            ->setDateBlocage(null)
            ->setDerniereConnexion(new DateTimeImmutable())
            ->setPremiereConnexion(false)
            ->setDateModification(new DateTimeImmutable());

        $this->utilisateurRepository->save($user);
    }

    private function decodeRecoveryCodes(?string $stored): array
    {
        if ($stored === null || $stored === '') {
            return [];
        }

        $decoded = json_decode($stored, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, 'is_string'));
    }

    private function encodeRecoveryCodes(array $codes): string
    {
        return json_encode(array_values($codes), JSON_THROW_ON_ERROR);
    }
}
