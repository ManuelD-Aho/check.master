<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Soutenance\Mention;
use App\Repository\Soutenance\MentionRepository;
use App\Service\System\SettingsService;
use Throwable;

class MoyenneCalculationService
{
    private MentionRepository $mentionRepository;
    private SettingsService $settingsService;

    public function __construct(
        MentionRepository $mentionRepository,
        SettingsService $settingsService
    ) {
        $this->mentionRepository = $mentionRepository;
        $this->settingsService = $settingsService;
    }

    public function calculateMoyenneFinaleStandard(float $moyenneM1, float $moyenneS1M2, float $noteMemoire): float
    {
        return $this->round(($moyenneM1 + $moyenneS1M2 + $noteMemoire) / 3);
    }

    public function calculateMoyenneFinaleSimplifiee(float $moyenneM1, float $noteMemoire): float
    {
        return $this->round(($moyenneM1 + $noteMemoire) / 2);
    }

    public function determineMention(float $moyenneFinale): ?string
    {
        try {
            $mention = $this->mentionRepository->findByMoyenne($moyenneFinale);

            if ($mention instanceof Mention) {
                return $mention->getLibelleMention();
            }
        } catch (Throwable) {
        }

        if ($moyenneFinale >= 16) {
            return 'Très Bien';
        }
        if ($moyenneFinale >= 14) {
            return 'Bien';
        }
        if ($moyenneFinale >= 12) {
            return 'Assez Bien';
        }
        if ($moyenneFinale >= 10) {
            return 'Passable';
        }

        return null;
    }

    public function determineDecision(float $moyenneFinale): string
    {
        return $moyenneFinale >= 10 ? 'ADMIS' : 'REFUSE';
    }

    public function round(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }
}
