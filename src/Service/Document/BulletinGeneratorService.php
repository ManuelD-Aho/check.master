<?php
declare(strict_types=1);

namespace App\Service\Document;

use DateTimeImmutable;

class BulletinGeneratorService extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $inscription = $data['inscription'] ?? null;
        $notes = is_array($data['notes'] ?? null) ? $data['notes'] : [];
        $semestre = $data['semestre'] ?? null;

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);
        $filiere = $this->pick($inscription, ['filiere', 'parcours', 'programme']);
        $niveau = $this->pick($inscription, ['niveau', 'cycle']);
        $annee = $this->pick($inscription, ['annee', 'annee_academique', 'anneeAcademique']);
        $semestreLabel = $this->pick($semestre, ['libelle', 'label', 'nom']);
        $dateEdition = (new DateTimeImmutable())->format('d/m/Y');

        $html = '<h2 style="text-align:center;">Bulletin de notes</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Matricule', $matricule);
        $html .= $this->tableRow('Filiere', $filiere);
        $html .= $this->tableRow('Niveau', $niveau);
        $html .= $this->tableRow('Annee academique', $annee);
        $html .= $this->tableRow('Semestre', $semestreLabel);
        $html .= $this->tableRow('Date edition', $dateEdition);
        $html .= '</table>';

        $html .= '<br/><h3>Detail des notes</h3>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= '<tr><th width="50%"><strong>Matiere</strong></th><th width="25%"><strong>Note</strong></th><th width="25%"><strong>Coefficient</strong></th></tr>';

        $totalPondere = 0.0;
        $totalCoeff = 0.0;

        foreach ($notes as $note) {
            if (!is_array($note)) {
                continue;
            }

            $matiere = $this->stringValue($note['matiere'] ?? $note['ue'] ?? $note['element'] ?? '');
            $valeur = $note['note'] ?? $note['valeur'] ?? '';
            $coefficient = $note['coefficient'] ?? $note['coeff'] ?? '';

            $valeurNum = is_numeric($valeur) ? (float) $valeur : 0.0;
            $coeffNum = is_numeric($coefficient) ? (float) $coefficient : 0.0;

            $totalPondere += $valeurNum * $coeffNum;
            $totalCoeff += $coeffNum;

            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($matiere !== '' ? $matiere : '-', ENT_QUOTES) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($this->stringValue($valeur), ENT_QUOTES) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($this->stringValue($coefficient), ENT_QUOTES) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $moyenne = $totalCoeff > 0 ? round($totalPondere / $totalCoeff, 2) : 0;
        $html .= '<br/><table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Moyenne generale', (string) $moyenne . ' / 20');
        $html .= '</table>';

        $decision = $this->stringValue($data['decision'] ?? '');
        if ($decision !== '') {
            $html .= '<br/>';
            $html .= $this->tableRow('Decision', $decision);
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Bulletin de notes');
        $this->setFooter($pdf);

        $suffix = $matricule !== '' ? $matricule . '_' . date('YmdHis') : date('YmdHis');
        return $this->savePdf($pdf, 'bulletin_' . $suffix . '.pdf');
    }
}
