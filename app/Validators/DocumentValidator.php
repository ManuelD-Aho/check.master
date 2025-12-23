<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Document
 * 
 * Valide les données des documents et fichiers uploadés.
 */
class DocumentValidator extends BaseValidator
{
    /**
     * Types de documents valides
     */
    private const TYPES_DOCUMENTS = [
        'rapport',
        'pv_commission',
        'pv_soutenance',
        'convocation',
        'attestation',
        'recu_paiement',
        'bulletin_notes',
        'piece_identite',
        'photo',
        'certificat',
        'lettre',
        'autre',
    ];

    /**
     * Extensions autorisées par type
     */
    private const EXTENSIONS_PAR_TYPE = [
        'rapport' => ['pdf'],
        'pv_commission' => ['pdf'],
        'pv_soutenance' => ['pdf'],
        'convocation' => ['pdf'],
        'attestation' => ['pdf'],
        'recu_paiement' => ['pdf'],
        'bulletin_notes' => ['pdf'],
        'piece_identite' => ['pdf', 'jpg', 'jpeg', 'png'],
        'photo' => ['jpg', 'jpeg', 'png'],
        'certificat' => ['pdf'],
        'lettre' => ['pdf', 'doc', 'docx'],
        'autre' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'],
    ];

    /**
     * Types MIME autorisés
     */
    private const MIME_TYPES = [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    /**
     * Taille maximale par défaut (10 Mo)
     */
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Taille maximale pour les rapports (50 Mo)
     */
    private const MAX_RAPPORT_SIZE = 50 * 1024 * 1024;

    /**
     * Taille maximale pour les images (5 Mo)
     */
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024;

    /**
     * Valide les données du document
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Nom obligatoire
        $this->validateRequired('nom', 'Le nom du document est obligatoire');
        $this->validateMaxLength('nom', 255, 'Le nom ne doit pas dépasser 255 caractères');

        // Type de document obligatoire
        $this->validateRequired('type_document', 'Le type de document est obligatoire');
        $this->validateInArray('type_document', self::TYPES_DOCUMENTS, 'Type de document invalide');

        // Description optionnelle
        if (!$this->isEmpty('description')) {
            $this->validateMaxLength('description', 1000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide un fichier uploadé
     *
     * @param array<string, mixed> $file Données du fichier ($_FILES)
     * @param string $typeDocument Type de document attendu
     */
    public function validateUpload(array $file, string $typeDocument = 'autre'): bool
    {
        $this->resetErrors();

        // Vérifier que le fichier est présent
        if (empty($file) || !isset($file['tmp_name']) || !is_string($file['tmp_name'])) {
            $this->addError('file', 'Le fichier est obligatoire');
            return false;
        }

        // Vérifier les erreurs d'upload
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $this->addError('file', $this->getUploadErrorMessage((int) $file['error']));
            return false;
        }

        // Vérifier la taille
        $maxSize = $this->getMaxSizeForType($typeDocument);
        if (isset($file['size']) && $file['size'] > $maxSize) {
            $maxSizeMb = round($maxSize / 1024 / 1024, 1);
            $this->addError('file', "Le fichier ne doit pas dépasser {$maxSizeMb} Mo");
            return false;
        }

        // Vérifier le type MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if ($mimeType === false || !isset(self::MIME_TYPES[$mimeType])) {
            $this->addError('file', 'Type de fichier non autorisé');
            return false;
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $allowedExtensions = self::EXTENSIONS_PAR_TYPE[$typeDocument] ?? self::EXTENSIONS_PAR_TYPE['autre'];
        if (!in_array($extension, $allowedExtensions, true)) {
            $this->addError('file', 'Extension de fichier non autorisée pour ce type de document. Extensions autorisées: ' . implode(', ', $allowedExtensions));
            return false;
        }

        // Vérifier la cohérence MIME/extension
        $expectedExtension = self::MIME_TYPES[$mimeType];
        $normalizedExtension = $extension === 'jpeg' ? 'jpg' : $extension;
        if ($normalizedExtension !== $expectedExtension && !($normalizedExtension === 'jpg' && $expectedExtension === 'jpg')) {
            $this->addError('file', 'Le type de fichier ne correspond pas à son extension');
            return false;
        }

        // Validations spécifiques au type
        return $this->validateSpecificType($file, $typeDocument);
    }

    /**
     * Validations spécifiques selon le type de document
     *
     * @param array<string, mixed> $file
     */
    private function validateSpecificType(array $file, string $typeDocument): bool
    {
        switch ($typeDocument) {
            case 'rapport':
                return $this->validateRapport($file);
            case 'photo':
                return $this->validatePhoto($file);
            case 'piece_identite':
                return $this->validatePieceIdentite($file);
            default:
                return true;
        }
    }

    /**
     * Valide un rapport de mémoire
     *
     * @param array<string, mixed> $file
     */
    private function validateRapport(array $file): bool
    {
        // Le rapport doit être en PDF
        if (!isset($file['tmp_name']) || !is_string($file['tmp_name'])) {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if ($mimeType !== 'application/pdf') {
            $this->addError('file', 'Le rapport doit être au format PDF');
            return false;
        }

        // Vérifier le nombre de pages minimum (optionnel, si possible)
        // Cette vérification nécessiterait une bibliothèque PDF

        return true;
    }

    /**
     * Valide une photo
     *
     * @param array<string, mixed> $file
     */
    private function validatePhoto(array $file): bool
    {
        if (!isset($file['tmp_name']) || !is_string($file['tmp_name'])) {
            return false;
        }

        // Vérifier que c'est bien une image
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $this->addError('file', 'Le fichier n\'est pas une image valide');
            return false;
        }

        // Vérifier les dimensions minimales
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width < 200 || $height < 200) {
            $this->addError('file', 'L\'image doit faire au moins 200x200 pixels');
            return false;
        }

        // Vérifier le ratio pour une photo d'identité (environ 3:4)
        $ratio = $width / $height;
        if ($ratio < 0.6 || $ratio > 0.9) {
            $this->addError('file', 'Le format de la photo doit être proche de 3:4 (photo d\'identité)');
            return false;
        }

        return true;
    }

    /**
     * Valide une pièce d'identité
     *
     * @param array<string, mixed> $file
     */
    private function validatePieceIdentite(array $file): bool
    {
        if (!isset($file['tmp_name']) || !is_string($file['tmp_name'])) {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        // Si c'est une image, vérifier la lisibilité
        if (str_starts_with((string) $mimeType, 'image/')) {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                $this->addError('file', 'L\'image n\'est pas lisible');
                return false;
            }

            // Dimensions minimales pour être lisible
            if ($imageInfo[0] < 600 || $imageInfo[1] < 400) {
                $this->addError('file', 'L\'image doit avoir une résolution suffisante (minimum 600x400)');
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne la taille maximale pour un type de document
     */
    private function getMaxSizeForType(string $typeDocument): int
    {
        return match ($typeDocument) {
            'rapport' => self::MAX_RAPPORT_SIZE,
            'photo', 'piece_identite' => self::MAX_IMAGE_SIZE,
            default => self::MAX_FILE_SIZE,
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

    /**
     * Valide les métadonnées d'un document archivé
     *
     * @param array<string, mixed> $data
     */
    public function validateArchive(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Hash SHA256 obligatoire
        $this->validateRequired('hash_sha256', 'Le hash SHA256 est obligatoire');
        $this->validateRegex('hash_sha256', '/^[a-f0-9]{64}$/i', 'Format de hash SHA256 invalide');

        // Type entité
        if (!$this->isEmpty('entite_type')) {
            $this->validateMaxLength('entite_type', 50);
        }

        // ID entité
        if (!$this->isEmpty('entite_id')) {
            $this->validatePositiveInteger('entite_id');
        }

        return !$this->hasErrors();
    }
}
