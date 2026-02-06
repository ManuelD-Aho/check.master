<?php

declare(strict_types=1);

namespace App\Entity\Commission;

use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compte_rendu_commission')]
class CompteRenduCommission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_compte_rendu', type: 'integer')]
    private ?int $idCompteRendu = null;

    #[ORM\ManyToOne(targetEntity: SessionCommission::class)]
    #[ORM\JoinColumn(name: 'id_session', referencedColumnName: 'id_session', nullable: false)]
    private SessionCommission $session;

    #[ORM\Column(name: 'numero_pv', type: 'string', length: 50, unique: true)]
    private string $numeroPv;

    #[ORM\Column(name: 'titre_pv', type: 'string', length: 255)]
    private string $titrePv;

    #[ORM\Column(name: 'contenu_html', type: 'text', columnDefinition: 'LONGTEXT')]
    private string $contenuHtml;

    #[ORM\Column(name: 'chemin_fichier_pdf', type: 'string', length: 255, nullable: true)]
    private ?string $cheminFichierPdf = null;

    #[ORM\Column(name: 'statut_pv', type: 'string', enumType: StatutPv::class, length: 20, options: ['default' => 'brouillon'])]
    private StatutPv $statutPv = StatutPv::Brouillon;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_createur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $createur;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_finalisation', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateFinalisation = null;

    public function getIdCompteRendu(): ?int
    {
        return $this->idCompteRendu;
    }

    public function setIdCompteRendu(int $idCompteRendu): self
    {
        $this->idCompteRendu = $idCompteRendu;

        return $this;
    }

    public function getSession(): SessionCommission
    {
        return $this->session;
    }

    public function setSession(SessionCommission $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getNumeroPv(): string
    {
        return $this->numeroPv;
    }

    public function setNumeroPv(string $numeroPv): self
    {
        $this->numeroPv = $numeroPv;

        return $this;
    }

    public function getTitrePv(): string
    {
        return $this->titrePv;
    }

    public function setTitrePv(string $titrePv): self
    {
        $this->titrePv = $titrePv;

        return $this;
    }

    public function getContenuHtml(): string
    {
        return $this->contenuHtml;
    }

    public function setContenuHtml(string $contenuHtml): self
    {
        $this->contenuHtml = $contenuHtml;

        return $this;
    }

    public function getCheminFichierPdf(): ?string
    {
        return $this->cheminFichierPdf;
    }

    public function setCheminFichierPdf(?string $cheminFichierPdf): self
    {
        $this->cheminFichierPdf = $cheminFichierPdf;

        return $this;
    }

    public function getStatutPv(): StatutPv
    {
        return $this->statutPv;
    }

    public function setStatutPv(StatutPv $statutPv): self
    {
        $this->statutPv = $statutPv;

        return $this;
    }

    public function getCreateur(): Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(Utilisateur $createur): self
    {
        $this->createur = $createur;

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

    public function getDateFinalisation(): ?DateTimeInterface
    {
        return $this->dateFinalisation;
    }

    public function setDateFinalisation(?DateTimeInterface $dateFinalisation): self
    {
        $this->dateFinalisation = $dateFinalisation;

        return $this;
    }
}
