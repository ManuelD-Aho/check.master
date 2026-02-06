<?php
declare(strict_types=1);

namespace App\Service\Document;

use DateTimeImmutable;

class AttestationStageGenerator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $candidature = $data['candidature'] ?? null;
        $informationStage = $data['informationStage'] ?? null;

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);
        $filiere = $this->pick($candidature, ['filiere', 'parcours', 'programme']);
        $entreprise = $this->pick($informationStage, ['entreprise', 'structure', 'organisme']);
        $lieu = $this->pick($informationStage, ['lieu', 'ville']);
        $dateDebut = $this->formatDate($this->pick($informationStage, ['date_debut', 'dateDebut']));
        $dateFin = $this->formatDate($this->pick($informationStage, ['date_fin', 'dateFin']));
        $sujet = $this->pick($informationStage, ['sujet', 'theme', 'intitule']);
        $dateEdition = (new DateTimeImmutable())->format('d/m/Y');

        $html = '<h2 style="text-align:center;">Attestation de stage</h2>';
        $html .= '<p>Nous attestons que <strong>' . htmlspecialchars($etudiantNom !== '' ? $etudiantNom : '-', ENT_QUOTES) . '</strong>,';
        $html .= ' matricule <strong>' . htmlspecialchars($matricule !== '' ? $matricule : '-', ENT_QUOTES) . '</strong>,';
        if ($filiere !== '') {
            $html .= ' etudiant(e) en <strong>' . htmlspecialchars($filiere, ENT_QUOTES) . '</strong>, ';
        }
        $html .= 'a effectue un stage';
        if ($entreprise !== '') {
            $html .= ' au sein de <strong>' . htmlspecialchars($entreprise, ENT_QUOTES) . '</strong>';
        }
        if ($lieu !== '') {
            $html .= ' a ' . htmlspecialchars($lieu, ENT_QUOTES);
        }
        if ($dateDebut !== '' || $dateFin !== '') {
            $html .= ' du <strong>' . htmlspecialchars($dateDebut !== '' ? $dateDebut : '-', ENT_QUOTES) . '</strong>';
            $html .= ' au <strong>' . htmlspecialchars($dateFin !== '' ? $dateFin : '-', ENT_QUOTES) . '</strong>';
        }
        $html .= '.';
        if ($sujet !== '') {
            $html .= ' Sujet: <strong>' . htmlspecialchars($sujet, ENT_QUOTES) . '</strong>.';
        }
        $html .= '</p>';
        $html .= '<p>Fait le ' . htmlspecialchars($dateEdition, ENT_QUOTES) . '</p>';
        $html .= '<p>La direction</p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Attestation de stage');
        $this->setFooter($pdf);

        $suffix = $matricule !== '' ? $matricule : date('YmdHis');
        return $this->savePdf($pdf, 'attestation_stage_' . $suffix . '.pdf');
    }
}
