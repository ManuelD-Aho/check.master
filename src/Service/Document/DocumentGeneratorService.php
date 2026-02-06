<?php
declare(strict_types=1);

namespace App\Service\Document;

use App\Service\System\SettingsService;

class DocumentGeneratorService
{
    private SettingsService $settings;
    private string $storagePath;

    /** @var array<string, AbstractPdfGenerator> */
    private array $generators;

    public function __construct(
        SettingsService $settings,
        string $storagePath,
        RecuPaiementGenerator $recuGenerator,
        AttestationInscriptionGenerator $attestationInscriptionGenerator,
        AttestationStageGenerator $attestationStageGenerator,
        Annexe1Generator $annexe1Generator,
        Annexe2Generator $annexe2Generator,
        Annexe3Generator $annexe3Generator,
        CompteRenduCommissionGenerator $compteRenduGenerator,
        FicheNotationGenerator $ficheNotationGenerator,
        PvSoutenanceGenerator $pvSoutenanceGenerator,
        BulletinGeneratorService $bulletinGenerator,
        PvFinalGeneratorService $pvFinalGenerator
    ) {
        $this->settings = $settings;
        $this->storagePath = $storagePath;
        $this->generators = [
            'recu_paiement' => $recuGenerator,
            'attestation_inscription' => $attestationInscriptionGenerator,
            'attestation_stage' => $attestationStageGenerator,
            'annexe1' => $annexe1Generator,
            'annexe2' => $annexe2Generator,
            'annexe3' => $annexe3Generator,
            'compte_rendu_commission' => $compteRenduGenerator,
            'fiche_notation' => $ficheNotationGenerator,
            'pv_soutenance' => $pvSoutenanceGenerator,
            'bulletin' => $bulletinGenerator,
            'pv_final' => $pvFinalGenerator,
        ];
    }

    public function generate(string $type, array $data): string
    {
        if (!isset($this->generators[$type])) {
            throw new \InvalidArgumentException(sprintf('Unknown document type "%s".', $type));
        }

        return $this->generators[$type]->generate($data);
    }

    /**
     * @return list<string>
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->generators);
    }

    public function hasGenerator(string $type): bool
    {
        return isset($this->generators[$type]);
    }
}
