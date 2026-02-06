<?php
declare(strict_types=1);

namespace App\Service\Document;

use DateTimeImmutable;

class AttestationInscriptionGenerator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $inscription = $data['inscription'] ?? null;
        $anneeAcademique = $this->stringValue($data['anneeAcademique'] ?? '');

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);
        $dateNaissance = $this->formatDate($this->pick($etudiant, ['date_naissance', 'dateNaissance']));
        $lieuNaissance = $this->pick($etudiant, ['lieu_naissance', 'lieuNaissance']);
        $filiere = $this->pick($inscription, ['filiere', 'parcours', 'programme']);
        $niveau = $this->pick($inscription, ['niveau', 'cycle']);
        $annee = $anneeAcademique !== '' ? $anneeAcademique : $this->pick($inscription, ['annee', 'annee_academique', 'anneeAcademique']);
        $dateEdition = (new DateTimeImmutable())->format('d/m/Y');

        $html = '<h2 style="text-align:center;">Attestation d\'inscription</h2>';
        $html .= '<p>Nous attestons que <strong>' . htmlspecialchars($etudiantNom !== '' ? $etudiantNom : '-', ENT_QUOTES) . '</strong>';
        $html .= ' ne le ' . htmlspecialchars($dateNaissance !== '' ? $dateNaissance : '-', ENT_QUOTES);
        if ($lieuNaissance !== '') {
            $html .= ' a ' . htmlspecialchars($lieuNaissance, ENT_QUOTES);
        }
        $html .= ', matricule <strong>' . htmlspecialchars($matricule !== '' ? $matricule : '-', ENT_QUOTES) . '</strong>,';
        $html .= ' est inscrit(e) en <strong>' . htmlspecialchars($filiere !== '' ? $filiere : '-', ENT_QUOTES) . '</strong>';
        $html .= ' niveau <strong>' . htmlspecialchars($niveau !== '' ? $niveau : '-', ENT_QUOTES) . '</strong>';
        if ($annee !== '') {
            $html .= ' pour l\'annee academique <strong>' . htmlspecialchars($annee, ENT_QUOTES) . '</strong>';
        }
        $html .= '.</p>';
        $html .= '<p>Fait le ' . htmlspecialchars($dateEdition, ENT_QUOTES) . '</p>';
        $html .= '<p>La direction</p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Attestation d\'inscription');
        $this->setFooter($pdf);

        $suffix = $matricule !== '' ? $matricule : date('YmdHis');
        return $this->savePdf($pdf, 'attestation_inscription_' . $suffix . '.pdf');
    }
}
