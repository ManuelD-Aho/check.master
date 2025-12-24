<?php

declare(strict_types=1);

namespace App\Services\Documents;

use App\Models\DocumentGenere;
use App\Services\Security\ServiceAudit;
use App\Services\Core\ServiceParametres;
use TCPDF;
use Mpdf\Mpdf;

/**
 * Service PDF
 * 
 * Génération de PDFs pour les 13 types de documents.
 * Utilise TCPDF pour documents simples et mPDF pour documents complexes.
 * Calcul SHA256 pour l'intégrité.
 * 
 * @see PRD Section Documents
 */
class ServicePdf
{
    private const STORAGE_DIR = 'storage/documents';

    /**
     * Types de documents supportés
     */
    public const TYPE_RECU_PAIEMENT = 'recu_paiement';
    public const TYPE_RECU_PENALITE = 'recu_penalite';
    public const TYPE_BULLETIN_NOTES = 'bulletin_notes';
    public const TYPE_PV_COMMISSION = 'pv_commission';
    public const TYPE_PV_SOUTENANCE = 'pv_soutenance';
    public const TYPE_CONVOCATION = 'convocation';
    public const TYPE_ATTESTATION_DIPLOME = 'attestation_diplome';
    public const TYPE_RAPPORT_EVALUATION = 'rapport_evaluation';
    public const TYPE_BULLETIN_PROVISOIRE = 'bulletin_provisoire';
    public const TYPE_CERTIFICAT_SCOLARITE = 'certificat_scolarite';
    public const TYPE_LETTRE_JURY = 'lettre_jury';
    public const TYPE_ATTESTATION_STAGE = 'attestation_stage';
    public const TYPE_BORDEREAU_TRANSMISSION = 'bordereau_transmission';

    /**
     * Génère un PDF simple avec TCPDF
     */
    public static function generer(
        string $typeDocument,
        array $donnees,
        ?int $generePar = null,
        ?string $entiteType = null,
        ?int $entiteId = null
    ): array {
        $pdf = self::creerTcpdf();

        // Appliquer le template selon le type
        $contenu = self::appliquerTemplate($typeDocument, $donnees);

        $pdf->AddPage();
        $pdf->writeHTML($contenu, true, false, true, false, '');

        // Générer le fichier
        $nomFichier = self::genererNomFichier($typeDocument);
        $chemin = self::getStoragePath() . '/' . $nomFichier;

        $pdf->Output($chemin, 'F');

        // Calculer le hash
        $hash = hash_file('sha256', $chemin);

        // Enregistrer le document
        $document = DocumentGenere::enregistrer(
            $typeDocument,
            $chemin,
            $nomFichier,
            $generePar,
            $entiteType,
            $entiteId
        );

        ServiceAudit::log('generation_pdf', 'document', $document->getId(), [
            'type' => $typeDocument,
            'hash' => $hash,
        ]);

        return [
            'id' => $document->getId(),
            'path' => $chemin,
            'name' => $nomFichier,
            'hash' => $hash,
            'size' => filesize($chemin),
        ];
    }

    /**
     * Génère un PDF complexe avec mPDF
     */
    public static function genererAvance(
        string $typeDocument,
        array $donnees,
        ?int $generePar = null,
        ?string $entiteType = null,
        ?int $entiteId = null
    ): array {
        $mpdf = self::creerMpdf();

        // Appliquer le template selon le type
        $contenu = self::appliquerTemplate($typeDocument, $donnees);

        $mpdf->WriteHTML($contenu);

        // Générer le fichier
        $nomFichier = self::genererNomFichier($typeDocument);
        $chemin = self::getStoragePath() . '/' . $nomFichier;

        $mpdf->Output($chemin, 'F');

        // Calculer le hash
        $hash = hash_file('sha256', $chemin);

        // Enregistrer le document
        $document = DocumentGenere::enregistrer(
            $typeDocument,
            $chemin,
            $nomFichier,
            $generePar,
            $entiteType,
            $entiteId
        );

        ServiceAudit::log('generation_pdf_avance', 'document', $document->getId(), [
            'type' => $typeDocument,
            'hash' => $hash,
        ]);

        return [
            'id' => $document->getId(),
            'path' => $chemin,
            'name' => $nomFichier,
            'hash' => $hash,
            'size' => filesize($chemin),
        ];
    }

    /**
     * Crée une instance TCPDF configurée
     */
    private static function creerTcpdf(): TCPDF
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Métadonnées
        $pdf->SetCreator('CheckMaster');
        $pdf->SetAuthor('CheckMaster');
        $pdf->SetTitle('Document CheckMaster');

        // Marges
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Pagination
        $pdf->SetAutoPageBreak(true, 25);

        // Police par défaut
        $pdf->SetFont('helvetica', '', 10);

        return $pdf;
    }

    /**
     * Crée une instance mPDF configurée
     */
    private static function creerMpdf(): Mpdf
    {
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 25,
            'margin_header' => 5,
            'margin_footer' => 10,
        ];

        $mpdf = new Mpdf($config);

        $mpdf->SetCreator('CheckMaster');
        $mpdf->SetAuthor('CheckMaster');
        $mpdf->SetTitle('Document CheckMaster');

        return $mpdf;
    }

    /**
     * Applique le template selon le type de document
     */
    private static function appliquerTemplate(string $type, array $donnees): string
    {
        $template = self::getTemplate($type);
        return self::interpolerVariables($template, $donnees);
    }

    /**
     * Retourne le template HTML pour un type de document
     */
    private static function getTemplate(string $type): string
    {
        $templates = [
            self::TYPE_RECU_PAIEMENT => self::templateRecuPaiement(),
            self::TYPE_RECU_PENALITE => self::templateRecuPenalite(),
            self::TYPE_BULLETIN_NOTES => self::templateBulletinNotes(),
            self::TYPE_PV_COMMISSION => self::templatePvCommission(),
            self::TYPE_PV_SOUTENANCE => self::templatePvSoutenance(),
            self::TYPE_CONVOCATION => self::templateConvocation(),
            self::TYPE_ATTESTATION_DIPLOME => self::templateAttestationDiplome(),
            self::TYPE_CERTIFICAT_SCOLARITE => self::templateCertificatScolarite(),
            self::TYPE_LETTRE_JURY => self::templateLettreJury(),
        ];

        return $templates[$type] ?? self::templateGenerique();
    }

    /**
     * Interpole les variables dans le template
     */
    private static function interpolerVariables(string $template, array $donnees): string
    {
        foreach ($donnees as $key => $value) {
            if (is_scalar($value)) {
                $template = str_replace("{{$key}}", htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'), $template);
            }
        }
        return $template;
    }

    /**
     * Génère un nom de fichier unique
     */
    private static function genererNomFichier(string $type): string
    {
        return $type . '_' . date('Y-m-d_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
    }

    /**
     * Retourne le chemin de stockage
     */
    private static function getStoragePath(): string
    {
        $path = dirname(__DIR__, 3) . '/' . self::STORAGE_DIR;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }

    // ===== TEMPLATES =====

    private static function templateRecuPaiement(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
    .info { margin: 10px 0; }
    .amount { font-size: 24px; font-weight: bold; color: #006600; }
    .footer { margin-top: 30px; font-size: 9px; color: #666; }
</style>
<div class="header">
    <div class="title">REÇU DE PAIEMENT</div>
    <div>CheckMaster - UFHB</div>
</div>
<div class="info"><strong>N° Reçu:</strong> {numero_recu}</div>
<div class="info"><strong>Date:</strong> {date}</div>
<div class="info"><strong>Étudiant:</strong> {etudiant_nom}</div>
<div class="info"><strong>Matricule:</strong> {matricule}</div>
<div class="info"><strong>Année académique:</strong> {annee_academique}</div>
<div class="info"><strong>Motif:</strong> {motif}</div>
<div class="info"><strong>Mode de paiement:</strong> {mode_paiement}</div>
<div class="amount">Montant: {montant} FCFA</div>
<div class="footer">
    Ce reçu est généré automatiquement par le système CheckMaster.<br>
    Hash: {hash}
</div>
HTML;
    }

    private static function templateRecuPenalite(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; color: #cc0000; }
</style>
<div class="header">
    <div class="title">REÇU DE PÉNALITÉ</div>
</div>
<div><strong>N° Reçu:</strong> {numero_recu}</div>
<div><strong>Date:</strong> {date}</div>
<div><strong>Étudiant:</strong> {etudiant_nom}</div>
<div><strong>Motif de pénalité:</strong> {motif}</div>
<div><strong>Montant:</strong> {montant} FCFA</div>
HTML;
    }

    private static function templateBulletinNotes(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 8px; }
    th { background-color: #f0f0f0; }
</style>
<div class="header">
    <div class="title">BULLETIN DE NOTES</div>
    <div>Année académique {annee_academique}</div>
</div>
<div><strong>Étudiant:</strong> {etudiant_nom}</div>
<div><strong>Matricule:</strong> {matricule}</div>
<div><strong>Spécialité:</strong> {specialite}</div>
<table>
    <tr><th>Matière</th><th>Note</th><th>Coefficient</th><th>Mention</th></tr>
    {lignes_notes}
</table>
<div><strong>Moyenne générale:</strong> {moyenne}</div>
<div><strong>Mention:</strong> {mention_finale}</div>
HTML;
    }

    private static function templatePvCommission(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
</style>
<div class="header">
    <div class="title">PROCÈS-VERBAL DE COMMISSION</div>
    <div>Session du {date_session}</div>
</div>
<div><strong>Lieu:</strong> {lieu}</div>
<div><strong>Membres présents:</strong></div>
<div>{membres}</div>
<div><strong>Rapports évalués:</strong></div>
<div>{rapports}</div>
<div><strong>Décisions:</strong></div>
<div>{decisions}</div>
HTML;
    }

    private static function templatePvSoutenance(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
</style>
<div class="header">
    <div class="title">PROCÈS-VERBAL DE SOUTENANCE</div>
</div>
<div><strong>Date:</strong> {date}</div>
<div><strong>Étudiant:</strong> {etudiant_nom}</div>
<div><strong>Sujet:</strong> {sujet}</div>
<div><strong>Jury:</strong></div>
<div>{jury}</div>
<div><strong>Note finale:</strong> {note}</div>
<div><strong>Mention:</strong> {mention}</div>
<div><strong>Décision:</strong> {decision}</div>
HTML;
    }

    private static function templateConvocation(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
</style>
<div class="header">
    <div class="title">CONVOCATION</div>
</div>
<div>Abidjan, le {date}</div>
<div><strong>Destinataire:</strong> {destinataire}</div>
<div>{corps}</div>
<div>Le Responsable</div>
HTML;
    }

    private static function templateAttestationDiplome(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 30px; }
    .title { font-size: 24px; font-weight: bold; }
</style>
<div class="header">
    <div class="title">ATTESTATION DE DIPLÔME</div>
    <div>Université Félix Houphouët-Boigny</div>
</div>
<div>Je soussigné, certifie que:</div>
<div><strong>{etudiant_nom}</strong></div>
<div>Né(e) le {date_naissance} à {lieu_naissance}</div>
<div>a obtenu le diplôme de <strong>{diplome}</strong></div>
<div>avec la mention <strong>{mention}</strong></div>
<div>Fait à Abidjan, le {date}</div>
HTML;
    }

    private static function templateCertificatScolarite(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
</style>
<div class="header">
    <div class="title">CERTIFICAT DE SCOLARITÉ</div>
</div>
<div>Je soussigné certifie que <strong>{etudiant_nom}</strong></div>
<div>est régulièrement inscrit(e) en {niveau} {specialite}</div>
<div>pour l'année académique {annee_academique}.</div>
<div>Fait à Abidjan, le {date}</div>
HTML;
    }

    private static function templateLettreJury(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
</style>
<div>Abidjan, le {date}</div>
<div><strong>Objet:</strong> Invitation à participer au jury de soutenance</div>
<div>Cher(e) {destinataire},</div>
<div>{corps}</div>
<div>Cordialement,</div>
<div>Le Président de la Commission</div>
HTML;
    }

    private static function templateGenerique(): string
    {
        return <<<HTML
<style>
    body { font-family: helvetica; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; }
</style>
<div class="header">
    <div class="title">{titre}</div>
</div>
<div>{contenu}</div>
<div>Généré le {date}</div>
HTML;
    }
}
