<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Import
 * 
 * Valide les fichiers et données pour les imports en masse.
 */
class ImportValidator extends BaseValidator
{
    /**
     * Types de fichiers autorisés pour l'import
     */
    private const ALLOWED_MIME_TYPES = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
        'application/vnd.ms-excel', // xls
        'text/csv',
        'text/plain',
    ];

    /**
     * Extensions autorisées
     */
    private const ALLOWED_EXTENSIONS = ['xlsx', 'xls', 'csv'];

    /**
     * Taille maximale du fichier (10 Mo)
     */
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Colonnes requises pour import étudiants
     */
    private const REQUIRED_COLUMNS_ETUDIANTS = [
        'num_carte',
        'nom',
        'prenom',
        'email',
    ];

    /**
     * Colonnes requises pour import enseignants
     */
    private const REQUIRED_COLUMNS_ENSEIGNANTS = [
        'nom',
        'prenom',
        'email',
        'specialite',
    ];

    /**
     * Valide les données d'import
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Type d'import obligatoire
        $this->validateRequired('type_import', 'Le type d\'import est obligatoire');
        $this->validateInArray('type_import', ['etudiants', 'enseignants', 'entreprises', 'notes'], 'Type d\'import invalide');

        return !$this->hasErrors();
    }

    /**
     * Valide un fichier d'import
     *
     * @param array<string, mixed> $file Données du fichier ($_FILES)
     */
    public function validateFile(array $file): bool
    {
        $this->resetErrors();

        // Vérifier que le fichier est présent
        if (empty($file) || !isset($file['tmp_name'])) {
            $this->addError('file', 'Le fichier est obligatoire');
            return false;
        }

        // Vérifier les erreurs d'upload
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $this->addError('file', $this->getUploadErrorMessage($file['error']));
            return false;
        }

        // Vérifier la taille
        if (isset($file['size']) && $file['size'] > self::MAX_FILE_SIZE) {
            $maxSizeMb = self::MAX_FILE_SIZE / 1024 / 1024;
            $this->addError('file', "Le fichier ne doit pas dépasser {$maxSizeMb} Mo");
            return false;
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            $this->addError('file', 'Type de fichier non autorisé. Formats acceptés: ' . implode(', ', self::ALLOWED_EXTENSIONS));
            return false;
        }

        // Vérifier le type MIME
        if (isset($file['tmp_name']) && is_string($file['tmp_name'])) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
                $this->addError('file', 'Type de fichier non autorisé');
                return false;
            }
        }

        return true;
    }

    /**
     * Valide les colonnes du fichier d'import
     *
     * @param array<string> $columns Colonnes du fichier
     * @param string $typeImport Type d'import
     */
    public function validateColumns(array $columns, string $typeImport): bool
    {
        $this->resetErrors();

        $requiredColumns = $this->getRequiredColumns($typeImport);
        $normalizedColumns = array_map('strtolower', array_map('trim', $columns));
        $normalizedRequired = array_map('strtolower', $requiredColumns);

        $missingColumns = [];
        foreach ($normalizedRequired as $required) {
            if (!in_array($required, $normalizedColumns, true)) {
                $missingColumns[] = $required;
            }
        }

        if (!empty($missingColumns)) {
            $this->addError('columns', 'Colonnes manquantes: ' . implode(', ', $missingColumns));
            return false;
        }

        return true;
    }

    /**
     * Valide une ligne de données
     *
     * @param array<string, mixed> $row Données de la ligne
     * @param string $typeImport Type d'import
     * @param int $lineNumber Numéro de ligne
     */
    public function validateRow(array $row, string $typeImport, int $lineNumber): bool
    {
        $this->resetErrors();
        $this->data = $row;

        switch ($typeImport) {
            case 'etudiants':
                return $this->validateEtudiantRow($lineNumber);
            case 'enseignants':
                return $this->validateEnseignantRow($lineNumber);
            case 'entreprises':
                return $this->validateEntrepriseRow($lineNumber);
            case 'notes':
                return $this->validateNoteRow($lineNumber);
            default:
                $this->addError('type', "Type d'import non supporté");
                return false;
        }
    }

    /**
     * Valide une ligne d'import étudiant
     */
    private function validateEtudiantRow(int $lineNumber): bool
    {
        $prefix = "Ligne {$lineNumber}: ";

        if ($this->isEmpty('num_carte')) {
            $this->addError("ligne_{$lineNumber}_num_carte", $prefix . 'Numéro de carte obligatoire');
        } elseif (!preg_match('/^[A-Z]{2}\d{8}$/', (string) $this->data['num_carte'])) {
            $this->addError("ligne_{$lineNumber}_num_carte", $prefix . 'Format numéro de carte invalide (ex: CI01552852)');
        }

        if ($this->isEmpty('nom')) {
            $this->addError("ligne_{$lineNumber}_nom", $prefix . 'Nom obligatoire');
        }

        if ($this->isEmpty('prenom')) {
            $this->addError("ligne_{$lineNumber}_prenom", $prefix . 'Prénom obligatoire');
        }

        if ($this->isEmpty('email')) {
            $this->addError("ligne_{$lineNumber}_email", $prefix . 'Email obligatoire');
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError("ligne_{$lineNumber}_email", $prefix . 'Format email invalide');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une ligne d'import enseignant
     */
    private function validateEnseignantRow(int $lineNumber): bool
    {
        $prefix = "Ligne {$lineNumber}: ";

        if ($this->isEmpty('nom')) {
            $this->addError("ligne_{$lineNumber}_nom", $prefix . 'Nom obligatoire');
        }

        if ($this->isEmpty('prenom')) {
            $this->addError("ligne_{$lineNumber}_prenom", $prefix . 'Prénom obligatoire');
        }

        if ($this->isEmpty('email')) {
            $this->addError("ligne_{$lineNumber}_email", $prefix . 'Email obligatoire');
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError("ligne_{$lineNumber}_email", $prefix . 'Format email invalide');
        }

        if ($this->isEmpty('specialite')) {
            $this->addError("ligne_{$lineNumber}_specialite", $prefix . 'Spécialité obligatoire');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une ligne d'import entreprise
     */
    private function validateEntrepriseRow(int $lineNumber): bool
    {
        $prefix = "Ligne {$lineNumber}: ";

        if ($this->isEmpty('nom')) {
            $this->addError("ligne_{$lineNumber}_nom", $prefix . 'Nom entreprise obligatoire');
        }

        if (!$this->isEmpty('email') && !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError("ligne_{$lineNumber}_email", $prefix . 'Format email invalide');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une ligne d'import de note
     */
    private function validateNoteRow(int $lineNumber): bool
    {
        $prefix = "Ligne {$lineNumber}: ";

        if ($this->isEmpty('num_carte')) {
            $this->addError("ligne_{$lineNumber}_num_carte", $prefix . 'Numéro de carte obligatoire');
        }

        if ($this->isEmpty('note')) {
            $this->addError("ligne_{$lineNumber}_note", $prefix . 'Note obligatoire');
        } elseif (!is_numeric($this->data['note'])) {
            $this->addError("ligne_{$lineNumber}_note", $prefix . 'Note doit être un nombre');
        } else {
            $note = (float) $this->data['note'];
            if ($note < 0 || $note > 20) {
                $this->addError("ligne_{$lineNumber}_note", $prefix . 'Note doit être entre 0 et 20');
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Retourne les colonnes requises selon le type d'import
     *
     * @return array<string>
     */
    private function getRequiredColumns(string $typeImport): array
    {
        return match ($typeImport) {
            'etudiants' => self::REQUIRED_COLUMNS_ETUDIANTS,
            'enseignants' => self::REQUIRED_COLUMNS_ENSEIGNANTS,
            'entreprises' => ['nom'],
            'notes' => ['num_carte', 'note'],
            default => [],
        };
    }

    /**
     * Retourne un message d'erreur pour les erreurs d'upload
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement uploadé',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été uploadé',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier',
            UPLOAD_ERR_EXTENSION => 'Upload bloqué par une extension PHP',
            default => 'Erreur inconnue lors de l\'upload',
        };
    }
}
