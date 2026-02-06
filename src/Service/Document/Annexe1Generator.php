<?php
declare(strict_types=1);

namespace App\Service\Document;

class Annexe1Generator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $soutenance = $data['soutenance'] ?? null;
        $resultat = $this->stringValue($data['resultat'] ?? '');

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);
        $sujet = $this->pick($soutenance, ['sujet', 'theme', 'intitule']);
        $dateSoutenance = $this->formatDate($this->pick($soutenance, ['date', 'date_soutenance', 'dateSoutenance']));

        $html = '<h2 style="text-align:center;">Annexe 1</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Matricule', $matricule);
        $html .= $this->tableRow('Sujet', $sujet);
        $html .= $this->tableRow('Date soutenance', $dateSoutenance);
        $html .= $this->tableRow('Resultat', $resultat);
        $html .= '</table>';
        $html .= '<p>La presente annexe atteste du resultat de la soutenance.</p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Annexe 1');
        $this->setFooter($pdf);

        $suffix = $matricule !== '' ? $matricule : date('YmdHis');
        return $this->savePdf($pdf, 'annexe_1_' . $suffix . '.pdf');
    }
}
