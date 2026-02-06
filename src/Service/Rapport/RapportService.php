<?php
declare(strict_types=1);

namespace App\Service\Rapport;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Report\ActionValidation;
use App\Entity\Report\CommentaireRapport;
use App\Entity\Report\ModeleRapport;
use App\Entity\Report\Rapport;
use App\Entity\Report\StatutRapport;
use App\Entity\Report\TypeCommentaire;
use App\Entity\Report\TypeVersion;
use App\Entity\Report\ValidationRapport;
use App\Entity\Report\VersionRapport;
use App\Repository\Report\CommentaireRapportRepository;
use App\Repository\Report\RapportRepository;
use App\Repository\Report\ValidationRapportRepository;
use App\Repository\Report\VersionRapportRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class RapportService
{
    private EntityManagerInterface $entityManager;
    private RapportRepository $rapportRepository;
    private VersionRapportRepository $versionRepository;
    private CommentaireRapportRepository $commentaireRepository;
    private ValidationRapportRepository $validationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        RapportRepository $rapportRepository,
        VersionRapportRepository $versionRepository,
        CommentaireRapportRepository $commentaireRepository,
        ValidationRapportRepository $validationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->rapportRepository = $rapportRepository;
        $this->versionRepository = $versionRepository;
        $this->commentaireRepository = $commentaireRepository;
        $this->validationRepository = $validationRepository;
    }

    public function createRapport(
        $etudiant,
        AnneeAcademique $anneeAcademique,
        string $titre,
        string $theme,
        string $contenuHtml,
        $auteur,
        ?ModeleRapport $modeleRapport = null
    ): Rapport {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $rapport = new Rapport();
            $rapport->setEtudiant($etudiant)
                ->setAnneeAcademique($anneeAcademique)
                ->setModeleRapport($modeleRapport)
                ->setTitreRapport($titre)
                ->setThemeRapport($theme)
                ->setContenuHtml($contenuHtml)
                ->setContenuTexte($this->extractText($contenuHtml))
                ->setStatutRapport(StatutRapport::BROUILLON)
                ->setNombreMots($this->countWords($contenuHtml))
                ->setNombrePagesEstime($this->estimatePages($contenuHtml))
                ->setVersionCourante(1)
                ->setDateCreation($now)
                ->setDateModification($now);

            $version = $this->createVersion($rapport, 1, $contenuHtml, TypeVersion::MODIFICATION, $auteur, null, $now);

            $this->entityManager->persist($rapport);
            $this->entityManager->persist($version);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function saveContent(Rapport $rapport, string $contenuHtml, $auteur, ?string $commentaire = null): Rapport
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $nextVersion = $rapport->getVersionCourante() + 1;

            $rapport->setContenuHtml($contenuHtml)
                ->setContenuTexte($this->extractText($contenuHtml))
                ->setNombreMots($this->countWords($contenuHtml))
                ->setNombrePagesEstime($this->estimatePages($contenuHtml))
                ->setVersionCourante($nextVersion)
                ->setDateModification($now);

            $version = $this->createVersion($rapport, $nextVersion, $contenuHtml, TypeVersion::AUTO_SAVE, $auteur, $commentaire, $now);

            $this->entityManager->persist($rapport);
            $this->entityManager->persist($version);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function submit(Rapport $rapport, $auteur): Rapport
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $nextVersion = $rapport->getVersionCourante() + 1;

            $rapport->setStatutRapport(StatutRapport::SOUMIS)
                ->setDateSoumission($now)
                ->setVersionCourante($nextVersion)
                ->setDateModification($now);

            $version = $this->createVersion($rapport, $nextVersion, $rapport->getContenuHtml(), TypeVersion::SOUMISSION, $auteur, null, $now);

            $this->entityManager->persist($rapport);
            $this->entityManager->persist($version);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function approve(Rapport $rapport, $validateur, ?string $commentaire = null): Rapport
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $rapport->setStatutRapport(StatutRapport::APPROUVE)
                ->setDateApprobation($now)
                ->setDateModification($now);

            $validation = new ValidationRapport();
            $validation->setRapport($rapport)
                ->setValidateur($validateur)
                ->setActionValidation(ActionValidation::APPROUVE)
                ->setCommentaireValidation($commentaire)
                ->setDateValidation($now);

            $this->entityManager->persist($rapport);
            $this->entityManager->persist($validation);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function returnForRevision(
        Rapport $rapport,
        $validateur,
        string $motif,
        ?string $commentaire = null
    ): Rapport {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $rapport->setStatutRapport(StatutRapport::RETOURNE)
                ->setDateModification($now);

            $validation = new ValidationRapport();
            $validation->setRapport($rapport)
                ->setValidateur($validateur)
                ->setActionValidation(ActionValidation::RETOURNE)
                ->setMotifRetour($motif)
                ->setCommentaireValidation($commentaire)
                ->setDateValidation($now);

            $this->entityManager->persist($rapport);
            $this->entityManager->persist($validation);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function addComment(
        Rapport $rapport,
        $auteur,
        string $contenu,
        TypeCommentaire $typeCommentaire,
        bool $estPublic = true
    ): CommentaireRapport {
        $this->entityManager->beginTransaction();

        try {
            $commentaire = new CommentaireRapport();
            $commentaire->setRapport($rapport)
                ->setAuteur($auteur)
                ->setContenuCommentaire($contenu)
                ->setTypeCommentaire($typeCommentaire)
                ->setEstPublic($estPublic)
                ->setDateCreation(new DateTimeImmutable());

            $this->entityManager->persist($commentaire);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $commentaire;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function getVersionHistory(Rapport $rapport): array
    {
        if ($rapport->getIdRapport() === null) {
            return [];
        }

        return $this->versionRepository->findByRapport($rapport->getIdRapport());
    }

    public function generatePdf(
        Rapport $rapport,
        string $cheminFichier,
        ?string $referenceDocument = null,
        ?int $tailleFichier = null
    ): Rapport {
        $this->entityManager->beginTransaction();

        try {
            $rapport->setCheminFichierPdf($cheminFichier)
                ->setReferenceDocument($referenceDocument)
                ->setTailleFichier($tailleFichier)
                ->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($rapport);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    private function createVersion(
        Rapport $rapport,
        int $numeroVersion,
        string $contenuHtml,
        TypeVersion $typeVersion,
        $auteur,
        ?string $commentaire,
        DateTimeImmutable $dateCreation
    ): VersionRapport {
        $version = new VersionRapport();
        $version->setRapport($rapport)
            ->setNumeroVersion($numeroVersion)
            ->setContenuHtml($contenuHtml)
            ->setTypeVersion($typeVersion)
            ->setAuteur($auteur)
            ->setCommentaire($commentaire)
            ->setDateCreation($dateCreation);

        return $version;
    }

    private function extractText(string $html): ?string
    {
        $text = trim(strip_tags($html));
        return $text === '' ? null : $text;
    }

    private function countWords(string $html): int
    {
        $text = $this->extractText($html);
        if ($text === null) {
            return 0;
        }

        return str_word_count($text);
    }

    private function estimatePages(string $html): int
    {
        $words = $this->countWords($html);
        if ($words === 0) {
            return 0;
        }

        return (int)ceil($words / 300);
    }
}
