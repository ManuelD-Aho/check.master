<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Import
 * 
 * Lancée lors d'erreurs d'import de données (Excel, CSV, etc.).
 */
class ImportException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'IMPORT_ERROR';

    private int $ligne = 0;
    private string $colonne = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $erreurs = [];

    /**
     * @param string $message Message d'erreur
     * @param array<int, array<string, mixed>> $erreurs Liste des erreurs par ligne
     */
    public function __construct(
        string $message = 'Erreur lors de l\'import',
        array $erreurs = []
    ) {
        $this->erreurs = $erreurs;
        
        $details = [];
        if (!empty($erreurs)) {
            $details['errors'] = $erreurs;
            $details['error_count'] = count($erreurs);
        }

        parent::__construct($message, 422, 'IMPORT_ERROR', $details);
    }

    /**
     * Crée une exception pour erreur de format de fichier
     */
    public static function invalidFormat(string $expectedFormat): self
    {
        return new self("Format de fichier invalide. Format attendu: {$expectedFormat}");
    }

    /**
     * Crée une exception pour fichier trop volumineux
     */
    public static function fileTooLarge(int $maxSizeMb): self
    {
        return new self("Le fichier dépasse la taille maximale autorisée ({$maxSizeMb} Mo)");
    }

    /**
     * Crée une exception pour colonne manquante
     */
    public static function missingColumn(string $columnName): self
    {
        $e = new self("Colonne obligatoire manquante: {$columnName}");
        $e->colonne = $columnName;
        return $e;
    }

    /**
     * Crée une exception pour erreur de validation sur une ligne
     */
    public static function validationError(int $ligne, string $colonne, string $message): self
    {
        $e = new self(
            "Erreur ligne {$ligne}, colonne '{$colonne}': {$message}",
            [$ligne => ['colonne' => $colonne, 'message' => $message]]
        );
        $e->ligne = $ligne;
        $e->colonne = $colonne;
        return $e;
    }

    /**
     * Crée une exception pour plusieurs erreurs
     *
     * @param array<int, array<string, mixed>> $erreurs
     */
    public static function multipleErrors(array $erreurs): self
    {
        $count = count($erreurs);
        return new self(
            "L'import contient {$count} erreur(s). Veuillez corriger le fichier.",
            $erreurs
        );
    }

    /**
     * Crée une exception pour doublon détecté
     */
    public static function duplicateEntry(int $ligne, string $identifiant): self
    {
        return new self(
            "Doublon détecté ligne {$ligne}: {$identifiant} existe déjà",
            [$ligne => ['type' => 'duplicate', 'identifiant' => $identifiant]]
        );
    }

    /**
     * Crée une exception pour fichier vide
     */
    public static function emptyFile(): self
    {
        return new self("Le fichier est vide ou ne contient aucune donnée valide");
    }

    /**
     * Retourne le numéro de ligne en erreur
     */
    public function getLigne(): int
    {
        return $this->ligne;
    }

    /**
     * Retourne la colonne en erreur
     */
    public function getColonne(): string
    {
        return $this->colonne;
    }

    /**
     * Retourne toutes les erreurs
     *
     * @return array<int, array<string, mixed>>
     */
    public function getErreurs(): array
    {
        return $this->erreurs;
    }

    /**
     * Retourne le nombre d'erreurs
     */
    public function getErrorCount(): int
    {
        return count($this->erreurs);
    }
}
