<?php

declare(strict_types=1);

namespace App\Entity\Stage;

use App\Entity\User\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'historique_candidature')]
class HistoriqueCandidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Candidature::class)]
    #[ORM\JoinColumn(name: 'id_candidature', referencedColumnName: 'id_candidature', nullable: false)]
    private Candidature $candidature;

    #[ORM\Column(name: 'action', type: 'string', enumType: ActionHistorique::class, length: 20)]
    private ActionHistorique $action;

    #[ORM\Column(name: 'snapshot_json', type: 'json')]
    private array $snapshotJson = [];

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_auteur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $auteur;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_action', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateAction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCandidature(): Candidature
    {
        return $this->candidature;
    }

    public function setCandidature(Candidature $candidature): self
    {
        $this->candidature = $candidature;

        return $this;
    }

    public function getAction(): ActionHistorique
    {
        return $this->action;
    }

    public function setAction(ActionHistorique $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getSnapshotJson(): array
    {
        return $this->snapshotJson;
    }

    public function setSnapshotJson(array $snapshotJson): self
    {
        $this->snapshotJson = $snapshotJson;

        return $this;
    }

    public function getAuteur(): Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(Utilisateur $auteur): self
    {
        $this->auteur = $auteur;

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

    public function getDateAction(): ?\DateTimeImmutable
    {
        return $this->dateAction;
    }

    public function setDateAction(?\DateTimeImmutable $dateAction): self
    {
        $this->dateAction = $dateAction;

        return $this;
    }
}
