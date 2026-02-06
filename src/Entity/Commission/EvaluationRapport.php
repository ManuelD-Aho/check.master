<?php

declare(strict_types=1);

namespace App\Entity\Commission;

use App\Entity\Report\Rapport;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'evaluation_rapport', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_evaluation_rapport_cycle', columns: ['id_rapport', 'id_evaluateur', 'numero_cycle'])])]
class EvaluationRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_evaluation', type: 'integer')]
    private ?int $idEvaluation = null;

    #[ORM\ManyToOne(targetEntity: Rapport::class)]
    #[ORM\JoinColumn(name: 'id_rapport', referencedColumnName: 'id_rapport', nullable: false)]
    private Rapport $rapport;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_evaluateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $evaluateur;

    #[ORM\Column(name: 'numero_cycle', type: 'integer', options: ['default' => 1])]
    private int $numeroCycle = 1;

    #[ORM\Column(name: 'decision_evaluation', type: 'string', enumType: DecisionEvaluation::class, length: 3, nullable: true)]
    private ?DecisionEvaluation $decisionEvaluation = null;

    #[ORM\Column(name: 'note_qualite', type: 'integer', nullable: true)]
    private ?int $noteQualite = null;

    #[ORM\Column(name: 'points_forts', type: 'text', nullable: true)]
    private ?string $pointsForts = null;

    #[ORM\Column(name: 'points_ameliorer', type: 'text', nullable: true)]
    private ?string $pointsAmeliorer = null;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_evaluation', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateEvaluation = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateModification;

    public function getIdEvaluation(): ?int
    {
        return $this->idEvaluation;
    }

    public function setIdEvaluation(int $idEvaluation): self
    {
        $this->idEvaluation = $idEvaluation;

        return $this;
    }

    public function getRapport(): Rapport
    {
        return $this->rapport;
    }

    public function setRapport(Rapport $rapport): self
    {
        $this->rapport = $rapport;

        return $this;
    }

    public function getEvaluateur(): Utilisateur
    {
        return $this->evaluateur;
    }

    public function setEvaluateur(Utilisateur $evaluateur): self
    {
        $this->evaluateur = $evaluateur;

        return $this;
    }

    public function getNumeroCycle(): int
    {
        return $this->numeroCycle;
    }

    public function setNumeroCycle(int $numeroCycle): self
    {
        $this->numeroCycle = $numeroCycle;

        return $this;
    }

    public function getDecisionEvaluation(): ?DecisionEvaluation
    {
        return $this->decisionEvaluation;
    }

    public function setDecisionEvaluation(?DecisionEvaluation $decisionEvaluation): self
    {
        $this->decisionEvaluation = $decisionEvaluation;

        return $this;
    }

    public function getNoteQualite(): ?int
    {
        return $this->noteQualite;
    }

    public function setNoteQualite(?int $noteQualite): self
    {
        $this->noteQualite = $noteQualite;

        return $this;
    }

    public function getPointsForts(): ?string
    {
        return $this->pointsForts;
    }

    public function setPointsForts(?string $pointsForts): self
    {
        $this->pointsForts = $pointsForts;

        return $this;
    }

    public function getPointsAmeliorer(): ?string
    {
        return $this->pointsAmeliorer;
    }

    public function setPointsAmeliorer(?string $pointsAmeliorer): self
    {
        $this->pointsAmeliorer = $pointsAmeliorer;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDateEvaluation(): ?DateTimeInterface
    {
        return $this->dateEvaluation;
    }

    public function setDateEvaluation(?DateTimeInterface $dateEvaluation): self
    {
        $this->dateEvaluation = $dateEvaluation;

        return $this;
    }

    public function getDateCreation(): DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
