<?php

declare(strict_types=1);

namespace App\Services\Signature;

use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\SignatureException;

/**
 * Service Signature
 * 
 * Gestion des signatures électroniques optionnelles basées sur OTP.
 */
class ServiceSignature
{
    private const OTP_LONGUEUR = 6;
    private const OTP_VALIDITE_MINUTES = 10;

    /**
     * Génère un code OTP pour signature
     */
    public static function genererOtp(int $utilisateurId, string $documentRef): array
    {
        $code = self::genererCode();
        $hash = password_hash($code, PASSWORD_ARGON2ID);
        $expireA = date('Y-m-d H:i:s', time() + (self::OTP_VALIDITE_MINUTES * 60));

        // Stocker le hash en session ou cache
        $cle = "otp_signature_{$utilisateurId}_{$documentRef}";
        $_SESSION[$cle] = [
            'hash' => $hash,
            'expire_a' => $expireA,
            'document_ref' => $documentRef,
        ];

        // Envoyer le code par email/SMS
        ServiceNotification::envoyerParCode(
            'otp_signature',
            [$utilisateurId],
            ['code' => $code, 'validite' => self::OTP_VALIDITE_MINUTES]
        );

        ServiceAudit::log('generation_otp_signature', 'signature', null, [
            'utilisateur_id' => $utilisateurId,
            'document_ref' => $documentRef,
        ]);

        return [
            'genere' => true,
            'expire_a' => $expireA,
            'validite_minutes' => self::OTP_VALIDITE_MINUTES,
        ];
    }

    /**
     * Vérifie un code OTP
     */
    public static function verifierOtp(int $utilisateurId, string $documentRef, string $code): bool
    {
        $cle = "otp_signature_{$utilisateurId}_{$documentRef}";

        if (!isset($_SESSION[$cle])) {
            throw new SignatureException('Aucun code OTP en attente');
        }

        $donnees = $_SESSION[$cle];

        // Vérifier l'expiration
        if (strtotime($donnees['expire_a']) < time()) {
            unset($_SESSION[$cle]);
            throw new SignatureException('Le code OTP a expiré');
        }

        // Vérifier le code
        if (!password_verify($code, $donnees['hash'])) {
            throw new SignatureException('Code OTP invalide');
        }

        // Supprimer l'OTP utilisé
        unset($_SESSION[$cle]);

        ServiceAudit::log('verification_otp_signature', 'signature', null, [
            'utilisateur_id' => $utilisateurId,
            'document_ref' => $documentRef,
            'succes' => true,
        ]);

        return true;
    }

    /**
     * Signe un document avec OTP
     */
    public static function signerDocument(
        int $utilisateurId,
        string $documentRef,
        string $code
    ): array {
        // Vérifier l'OTP
        self::verifierOtp($utilisateurId, $documentRef, $code);

        // Générer la signature
        $signature = self::genererSignature($utilisateurId, $documentRef);

        ServiceAudit::log('signature_document', 'signature', null, [
            'utilisateur_id' => $utilisateurId,
            'document_ref' => $documentRef,
            'signature' => $signature,
        ]);

        return [
            'signe' => true,
            'signature' => $signature,
            'date' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Génère un code OTP numérique
     */
    private static function genererCode(): string
    {
        $code = '';
        for ($i = 0; $i < self::OTP_LONGUEUR; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }

    /**
     * Génère une signature unique
     */
    private static function genererSignature(int $utilisateurId, string $documentRef): string
    {
        $data = implode('|', [
            $utilisateurId,
            $documentRef,
            time(),
            bin2hex(random_bytes(16)),
        ]);

        return hash('sha256', $data);
    }

    /**
     * Vérifie une signature existante
     */
    public static function verifierSignature(string $signature, array $metadonnees): bool
    {
        // La vérification dépend de comment les signatures sont stockées
        // Pour l'instant, retourne true si la signature existe
        return !empty($signature) && strlen($signature) === 64;
    }
}
