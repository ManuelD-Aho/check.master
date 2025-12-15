<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Rapport
 * 
 * Valide les données de soumission de rapport.
 */
class RapportValidator
{
    private array $errors = [];

    /**
     * Extensions autorisées
     */
    private const EXTENSIONS_AUTORISEES = ['pdf', 'doc', 'docx'];

    /**
     * Taille max (10 MB)
     */
    private const TAILLE_MAX = 10485760;

    /**
     * Valide les données de rapport
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        // Titre obligatoire
        if (empty($data['titre'])) {
            $this->errors['titre'] = 'Le titre est obligatoire';
        } elseif (strlen($data['titre']) < 10) {
            $this->errors['titre'] = 'Le titre doit contenir au moins 10 caractères';
        }

        return empty($this->errors);
    }

    /**
     * Valide le fichier uploadé
     */
    public function validateFile(array $file): bool
    {
        $this->errors = [];

        // Vérifier l'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors['fichier'] = $this->getUploadErrorMessage($file['error']);
            return false;
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::EXTENSIONS_AUTORISEES)) {
            $this->errors['fichier'] = 'Format de fichier non autorisé. Autorisés: ' . implode(', ', self::EXTENSIONS_AUTORISEES);
            return false;
        }

        // Vérifier la taille
        if ($file['size'] > self::TAILLE_MAX) {
            $this->errors['fichier'] = 'Le fichier ne doit pas dépasser 10 MB';
            return false;
        }

        // Vérifier le type MIME
        $mimeType = mime_content_type($file['tmp_name']);
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            $this->errors['fichier'] = 'Type de fichier non autorisé';
            return false;
        }

        return true;
    }

    /**
     * Retourne le message d'erreur d'upload
     */
    private function getUploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Le fichier est trop volumineux',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement uploadé',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été uploadé',
            UPLOAD_ERR_NO_TMP_DIR => 'Erreur serveur: répertoire temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Erreur serveur: impossible d\'écrire le fichier',
            default => 'Erreur lors de l\'upload',
        };
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }
}
