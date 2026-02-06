<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'note_soutenance')]
#[ORM\UniqueConstraint(name: 'uk_note_soutenance_critere', columns: ['id_soutenance', 'id_critere'])]
class NoteSoutenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_note', type: 'integer')]
    private ?int $idNote = null;

    #[ORM\ManyToOne(targetEntity: Soutenance::class, inversedBy: 'notesSoutenance')]
    #[ORM\JoinColumn(name: 'id_soutenance', referencedColumnName: 'id_soutenance', nullable: false)]
    private ?Soutenance $soutenance = null;

    #[ORM\ManyToOne(targetEntity: CritereEvaluation::class, inversedBy: 'notesSoutenance')]
    #[ORM\JoinColumn(name: 'id_critere', referencedColumnName: 'id_critere', nullable: false)]
    private ?CritereEvaluation $critere = null;

    #[ORM\Column(name: 'note', type: 'decimal', precision: 4, scale: 2)]
    private string $note;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur_saisie', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $utilisateurSaisie = null;

    #[ORM\Column(name: 'date_saisie', type: 'datetime')]
    private DateTimeInterface $dateSaisie;

    public function getIdNote(): ?int
    {
        return $this->idNote;
    }

    public function getSoutenance(): ?Soutenance
    {
        return $this->soutenance;
    }

    public function setSoutenance(?Soutenance $soutenance): self
    {
        $this->soutenance = $soutenance;

        return $this;
    }

    public function getCritere(): ?CritereEvaluation
    {
        return $this->critere;
    }

    public function setCritere(?CritereEvaluation $critere): self
    {
        $this->critere = $critere;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

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

    public function getUtilisateurSaisie(): ?Utilisateur
    {
        return $this->utilisateurSaisie;
    }

    public function setUtilisateurSaisie(?Utilisateur $utilisateurSaisie): self
    {
        $this->utilisateurSaisie = $utilisateurSaisie;

        return $this;
    }

    public function getDateSaisie(): DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(DateTimeInterface $dateSaisie): self
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }
}
