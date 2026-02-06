<?php

declare(strict_types=1);

namespace App\Entity\Student;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'versement')]
class Versement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_versement', type: 'integer')]
    private ?int $idVersement = null;

    #[ORM\ManyToOne(targetEntity: Inscription::class, inversedBy: 'versements')]
    #[ORM\JoinColumn(name: 'id_inscription', referencedColumnName: 'id_inscription', nullable: false)]
    private ?Inscription $inscription = null;

    #[ORM\Column(name: 'montant_versement', type: 'decimal', precision: 10, scale: 2)]
    private string $montantVersement;

    #[ORM\Column(name: 'date_versement', type: 'date')]
    private \DateTimeInterface $dateVersement;

    #[ORM\Column(name: 'type_versement', enumType: TypeVersement::class)]
    private TypeVersement $typeVersement;

    #[ORM\Column(name: 'methode_paiement', enumType: MethodePaiement::class)]
    private MethodePaiement $methodePaiement;

    #[ORM\Column(name: 'reference_paiement', type: 'string', length: 100, nullable: true)]
    private ?string $referencePaiement = null;

    #[ORM\Column(name: 'recu_genere', type: 'boolean', options: ['default' => false])]
    private bool $recuGenere = false;

    #[ORM\Column(name: 'chemin_recu', type: 'string', length: 255, nullable: true)]
    private ?string $cheminRecu = null;

    #[ORM\Column(name: 'reference_recu', type: 'string', length: 50, nullable: true)]
    private ?string $referenceRecu = null;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\User\\Utilisateur')]
    #[ORM\JoinColumn(name: 'id_utilisateur_saisie', referencedColumnName: 'id_utilisateur', nullable: false)]
    private $utilisateurSaisie;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $dateCreation;

    public function getIdVersement(): ?int
    {
        return $this->idVersement;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getMontantVersement(): string
    {
        return $this->montantVersement;
    }

    public function setMontantVersement(string $montantVersement): self
    {
        $this->montantVersement = $montantVersement;

        return $this;
    }

    public function getDateVersement(): \DateTimeInterface
    {
        return $this->dateVersement;
    }

    public function setDateVersement(\DateTimeInterface $dateVersement): self
    {
        $this->dateVersement = $dateVersement;

        return $this;
    }

    public function getTypeVersement(): TypeVersement
    {
        return $this->typeVersement;
    }

    public function setTypeVersement(TypeVersement $typeVersement): self
    {
        $this->typeVersement = $typeVersement;

        return $this;
    }

    public function getMethodePaiement(): MethodePaiement
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(MethodePaiement $methodePaiement): self
    {
        $this->methodePaiement = $methodePaiement;

        return $this;
    }

    public function getReferencePaiement(): ?string
    {
        return $this->referencePaiement;
    }

    public function setReferencePaiement(?string $referencePaiement): self
    {
        $this->referencePaiement = $referencePaiement;

        return $this;
    }

    public function isRecuGenere(): bool
    {
        return $this->recuGenere;
    }

    public function setRecuGenere(bool $recuGenere): self
    {
        $this->recuGenere = $recuGenere;

        return $this;
    }

    public function getCheminRecu(): ?string
    {
        return $this->cheminRecu;
    }

    public function setCheminRecu(?string $cheminRecu): self
    {
        $this->cheminRecu = $cheminRecu;

        return $this;
    }

    public function getReferenceRecu(): ?string
    {
        return $this->referenceRecu;
    }

    public function setReferenceRecu(?string $referenceRecu): self
    {
        $this->referenceRecu = $referenceRecu;

        return $this;
    }

    public function getUtilisateurSaisie()
    {
        return $this->utilisateurSaisie;
    }

    public function setUtilisateurSaisie($utilisateurSaisie): self
    {
        $this->utilisateurSaisie = $utilisateurSaisie;

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

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
