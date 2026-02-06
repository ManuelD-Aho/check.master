<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'resultat_final')]
#[ORM\UniqueConstraint(name: 'uk_resultat_etudiant_annee', columns: ['matricule_etudiant', 'id_annee_academique'])]
class ResultatFinal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_resultat', type: 'integer')]
    private ?int $idResultat = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\ManyToOne(targetEntity: Soutenance::class)]
    #[ORM\JoinColumn(name: 'id_soutenance', referencedColumnName: 'id_soutenance', nullable: false)]
    private ?Soutenance $soutenance = null;

    #[ORM\Column(name: 'note_memoire', type: 'decimal', precision: 4, scale: 2)]
    private string $noteMemoire;

    #[ORM\Column(name: 'moyenne_m1', type: 'decimal', precision: 4, scale: 2)]
    private string $moyenneM1;

    #[ORM\Column(name: 'moyenne_s1_m2', type: 'decimal', precision: 4, scale: 2, nullable: true)]
    private ?string $moyenneS1M2 = null;

    #[ORM\Column(name: 'moyenne_finale', type: 'decimal', precision: 4, scale: 2)]
    private string $moyenneFinale;

    #[ORM\ManyToOne(targetEntity: Mention::class, inversedBy: 'resultats')]
    #[ORM\JoinColumn(name: 'id_mention', referencedColumnName: 'id_mention', nullable: true)]
    private ?Mention $mention = null;

    #[ORM\Column(name: 'type_pv', enumType: TypePv::class)]
    private TypePv $typePv;

    #[ORM\Column(name: 'decision_jury', enumType: DecisionJury::class)]
    private DecisionJury $decisionJury;

    #[ORM\Column(name: 'date_deliberation', type: 'datetime')]
    private DateTimeInterface $dateDeliberation;

    #[ORM\Column(name: 'valide', type: 'boolean')]
    private bool $valide = false;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_validateur', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $validateur = null;

    #[ORM\Column(name: 'reference_annexe1', type: 'string', length: 50, nullable: true)]
    private ?string $referenceAnnexe1 = null;

    #[ORM\Column(name: 'reference_annexe2', type: 'string', length: 50, nullable: true)]
    private ?string $referenceAnnexe2 = null;

    #[ORM\Column(name: 'reference_annexe3', type: 'string', length: 50, nullable: true)]
    private ?string $referenceAnnexe3 = null;

    #[ORM\Column(name: 'reference_pv_final', type: 'string', length: 50, nullable: true)]
    private ?string $referencePvFinal = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    public function getIdResultat(): ?int
    {
        return $this->idResultat;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getAnneeAcademique(): ?AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(?AnneeAcademique $anneeAcademique): self
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
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

    public function getNoteMemoire(): string
    {
        return $this->noteMemoire;
    }

    public function setNoteMemoire(string $noteMemoire): self
    {
        $this->noteMemoire = $noteMemoire;

        return $this;
    }

    public function getMoyenneM1(): string
    {
        return $this->moyenneM1;
    }

    public function setMoyenneM1(string $moyenneM1): self
    {
        $this->moyenneM1 = $moyenneM1;

        return $this;
    }

    public function getMoyenneS1M2(): ?string
    {
        return $this->moyenneS1M2;
    }

    public function setMoyenneS1M2(?string $moyenneS1M2): self
    {
        $this->moyenneS1M2 = $moyenneS1M2;

        return $this;
    }

    public function getMoyenneFinale(): string
    {
        return $this->moyenneFinale;
    }

    public function setMoyenneFinale(string $moyenneFinale): self
    {
        $this->moyenneFinale = $moyenneFinale;

        return $this;
    }

    public function getMention(): ?Mention
    {
        return $this->mention;
    }

    public function setMention(?Mention $mention): self
    {
        $this->mention = $mention;

        return $this;
    }

    public function getTypePv(): TypePv
    {
        return $this->typePv;
    }

    public function setTypePv(TypePv $typePv): self
    {
        $this->typePv = $typePv;

        return $this;
    }

    public function getDecisionJury(): DecisionJury
    {
        return $this->decisionJury;
    }

    public function setDecisionJury(DecisionJury $decisionJury): self
    {
        $this->decisionJury = $decisionJury;

        return $this;
    }

    public function getDateDeliberation(): DateTimeInterface
    {
        return $this->dateDeliberation;
    }

    public function setDateDeliberation(DateTimeInterface $dateDeliberation): self
    {
        $this->dateDeliberation = $dateDeliberation;

        return $this;
    }

    public function isValide(): bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getValidateur(): ?Utilisateur
    {
        return $this->validateur;
    }

    public function setValidateur(?Utilisateur $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getReferenceAnnexe1(): ?string
    {
        return $this->referenceAnnexe1;
    }

    public function setReferenceAnnexe1(?string $referenceAnnexe1): self
    {
        $this->referenceAnnexe1 = $referenceAnnexe1;

        return $this;
    }

    public function getReferenceAnnexe2(): ?string
    {
        return $this->referenceAnnexe2;
    }

    public function setReferenceAnnexe2(?string $referenceAnnexe2): self
    {
        $this->referenceAnnexe2 = $referenceAnnexe2;

        return $this;
    }

    public function getReferenceAnnexe3(): ?string
    {
        return $this->referenceAnnexe3;
    }

    public function setReferenceAnnexe3(?string $referenceAnnexe3): self
    {
        $this->referenceAnnexe3 = $referenceAnnexe3;

        return $this;
    }

    public function getReferencePvFinal(): ?string
    {
        return $this->referencePvFinal;
    }

    public function setReferencePvFinal(?string $referencePvFinal): self
    {
        $this->referencePvFinal = $referencePvFinal;

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
}
