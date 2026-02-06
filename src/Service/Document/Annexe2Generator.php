<?php
declare(strict_types=1);

namespace App\Service\Document;

class Annexe2Generator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $soutenance = $data['soutenance'] ?? null;
        $resultat = $this->stringValue($data['resultat'] ?? '');
        $jury = is_array($data['jury'] ?? null) ? $data['jury'] : [];

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);
        $sujet = $this->pick($soutenance, ['sujet', 'theme', 'intitule']);
        $dateSoutenance = $this->formatDate($this->pick($soutenance, ['date', 'date_soutenance', 'dateSoutenance']));

        $juryItems = [];
        foreach ($jury as $membre) {
            $juryItems[] = $this->formatPerson($membre);
        }

        $html = '<h2 style="text-align:center;">Annexe 2</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Matricule', $matricule);
        $html .= $this->tableRow('Sujet', $sujet);
        $html .= $this->tableRow('Date soutenance', $dateSoutenance);
        $html .= $this->tableRow('Resultat', $resultat);
        $html .= '</table>';
        $html .= '<h3>Jury</h3>';
        $html .= $this->listItems($juryItems);

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Annexe 2');
        $this->setFooter($pdf);

        $suffix = $matricule !== '' ? $matricule : date('YmdHis');
        return $this->savePdf($pdf, 'annexe_2_' . $suffix . '.pdf');
    }
}
