<?php

declare(strict_types=1);

namespace App\Entity\Report;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Etudiant;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rapport')]
#[ORM\UniqueConstraint(name: 'uk_rapport_etudiant_annee', columns: ['matricule_etudiant', 'id_annee_academique'])]
class Rapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_rapport', type: 'integer')]
    private ?int $idRapport = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\ManyToOne(targetEntity: ModeleRapport::class, inversedBy: 'rapports')]
    #[ORM\JoinColumn(name: 'id_modele', referencedColumnName: 'id_modele', nullable: true)]
    private ?ModeleRapport $modeleRapport = null;

    #[ORM\Column(name: 'titre_rapport', type: 'string', length: 255)]
    private string $titreRapport;

    #[ORM\Column(name: 'theme_rapport', type: 'string', length: 255)]
    private string $themeRapport;

    #[ORM\Column(name: 'contenu_html', type: 'text', columnDefinition: 'LONGTEXT')]
    private string $contenuHtml;

    #[ORM\Column(name: 'contenu_texte', type: 'text', columnDefinition: 'LONGTEXT', nullable: true)]
    private ?string $contenuTexte = null;

    #[ORM\Column(name: 'statut_rapport', enumType: StatutRapport::class)]
    private StatutRapport $statutRapport = StatutRapport::BROUILLON;

    #[ORM\Column(name: 'nombre_mots', type: 'integer')]
    private int $nombreMots = 0;

    #[ORM\Column(name: 'nombre_pages_estime', type: 'integer')]
    private int $nombrePagesEstime = 0;

    #[ORM\Column(name: 'version_courante', type: 'integer')]
    private int $versionCourante = 1;

    #[ORM\Column(name: 'chemin_fichier_pdf', type: 'string', length: 255, nullable: true)]
    private ?string $cheminFichierPdf = null;

    #[ORM\Column(name: 'reference_document', type: 'string', length: 50, nullable: true)]
    private ?string $referenceDocument = null;

    #[ORM\Column(name: 'taille_fichier', type: 'integer', nullable: true)]
    private ?int $tailleFichier = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime')]
    private DateTimeInterface $dateModification;

    #[ORM\Column(name: 'date_soumission', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateSoumission = null;

    #[ORM\Column(name: 'date_approbation', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateApprobation = null;

    #[ORM\OneToMany(mappedBy: 'rapport', targetEntity: VersionRapport::class)]
    private Collection $versionsRapport;

    #[ORM\OneToMany(mappedBy: 'rapport', targetEntity: CommentaireRapport::class)]
    private Collection $commentairesRapport;

    #[ORM\OneToMany(mappedBy: 'rapport', targetEntity: ValidationRapport::class)]
    private Collection $validationsRapport;

    public function __construct()
    {
        $this->versionsRapport = new ArrayCollection();
        $this->commentairesRapport = new ArrayCollection();
        $this->validationsRapport = new ArrayCollection();
    }

    public function getIdRapport(): ?int
    {
        return $this->idRapport;
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

    public function getModeleRapport(): ?ModeleRapport
    {
        return $this->modeleRapport;
    }

    public function setModeleRapport(?ModeleRapport $modeleRapport): self
    {
        $this->modeleRapport = $modeleRapport;

        return $this;
    }

    public function getTitreRapport(): string
    {
        return $this->titreRapport;
    }

    public function setTitreRapport(string $titreRapport): self
    {
        $this->titreRapport = $titreRapport;

        return $this;
    }

    public function getThemeRapport(): string
    {
        return $this->themeRapport;
    }

    public function setThemeRapport(string $themeRapport): self
    {
        $this->themeRapport = $themeRapport;

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

    public function getContenuTexte(): ?string
    {
        return $this->contenuTexte;
    }

    public function setContenuTexte(?string $contenuTexte): self
    {
        $this->contenuTexte = $contenuTexte;

        return $this;
    }

    public function getStatutRapport(): StatutRapport
    {
        return $this->statutRapport;
    }

    public function setStatutRapport(StatutRapport $statutRapport): self
    {
        $this->statutRapport = $statutRapport;

        return $this;
    }

    public function getNombreMots(): int
    {
        return $this->nombreMots;
    }

    public function setNombreMots(int $nombreMots): self
    {
        $this->nombreMots = $nombreMots;

        return $this;
    }

    public function getNombrePagesEstime(): int
    {
        return $this->nombrePagesEstime;
    }

    public function setNombrePagesEstime(int $nombrePagesEstime): self
    {
        $this->nombrePagesEstime = $nombrePagesEstime;

        return $this;
    }

    public function getVersionCourante(): int
    {
        return $this->versionCourante;
    }

    public function setVersionCourante(int $versionCourante): self
    {
        $this->versionCourante = $versionCourante;

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

    public function getReferenceDocument(): ?string
    {
        return $this->referenceDocument;
    }

    public function setReferenceDocument(?string $referenceDocument): self
    {
        $this->referenceDocument = $referenceDocument;

        return $this;
    }

    public function getTailleFichier(): ?int
    {
        return $this->tailleFichier;
    }

    public function setTailleFichier(?int $tailleFichier): self
    {
        $this->tailleFichier = $tailleFichier;

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

    public function getDateSoumission(): ?DateTimeInterface
    {
        return $this->dateSoumission;
    }

    public function setDateSoumission(?DateTimeInterface $dateSoumission): self
    {
        $this->dateSoumission = $dateSoumission;

        return $this;
    }

    public function getDateApprobation(): ?DateTimeInterface
    {
        return $this->dateApprobation;
    }

    public function setDateApprobation(?DateTimeInterface $dateApprobation): self
    {
        $this->dateApprobation = $dateApprobation;

        return $this;
    }

    public function getVersionsRapport(): Collection
    {
        return $this->versionsRapport;
    }

    public function addVersionRapport(VersionRapport $versionRapport): self
    {
        if (!$this->versionsRapport->contains($versionRapport)) {
            $this->versionsRapport->add($versionRapport);
            $versionRapport->setRapport($this);
        }

        return $this;
    }

    public function removeVersionRapport(VersionRapport $versionRapport): self
    {
        if ($this->versionsRapport->removeElement($versionRapport)) {
            if ($versionRapport->getRapport() === $this) {
                $versionRapport->setRapport(null);
            }
        }

        return $this;
    }

    public function getCommentairesRapport(): Collection
    {
        return $this->commentairesRapport;
    }

    public function addCommentaireRapport(CommentaireRapport $commentaireRapport): self
    {
        if (!$this->commentairesRapport->contains($commentaireRapport)) {
            $this->commentairesRapport->add($commentaireRapport);
            $commentaireRapport->setRapport($this);
        }

        return $this;
    }

    public function removeCommentaireRapport(CommentaireRapport $commentaireRapport): self
    {
        if ($this->commentairesRapport->removeElement($commentaireRapport)) {
            if ($commentaireRapport->getRapport() === $this) {
                $commentaireRapport->setRapport(null);
            }
        }

        return $this;
    }

    public function getValidationsRapport(): Collection
    {
        return $this->validationsRapport;
    }

    public function addValidationRapport(ValidationRapport $validationRapport): self
    {
        if (!$this->validationsRapport->contains($validationRapport)) {
            $this->validationsRapport->add($validationRapport);
            $validationRapport->setRapport($this);
        }

        return $this;
    }

    public function removeValidationRapport(ValidationRapport $validationRapport): self
    {
        if ($this->validationsRapport->removeElement($validationRapport)) {
            if ($validationRapport->getRapport() === $this) {
                $validationRapport->setRapport(null);
            }
        }

        return $this;
    }
}
