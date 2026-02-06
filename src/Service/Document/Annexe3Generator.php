<?php
declare(strict_types=1);

namespace App\Service\Document;

class Annexe3Generator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $moyennes = is_array($data['moyennes'] ?? null) ? $data['moyennes'] : [];
        $mention = $this->stringValue($data['mention'] ?? '');

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);

        $rows = '';
        if ($moyennes !== []) {
            foreach ($moyennes as $libelle => $valeur) {
                if (is_array($valeur)) {
                    $libelleText = $this->stringValue($valeur['libelle'] ?? $libelle);
                    $valeurText = $this->stringValue($valeur['moyenne'] ?? $valeur['valeur'] ?? '');
                } else {
                    $libelleText = is_int($libelle) ? 'Moyenne' : $this->stringValue($libelle);
                    $valeurText = $this->stringValue($valeur);
                }
                $rows .= '<tr><td width="70%">' . htmlspecialchars($libelleText !== '' ? $libelleText : '-', ENT_QUOTES) . '</td>';
                $rows .= '<td width="30%" align="center">' . htmlspecialchars($valeurText !== '' ? $valeurText : '-', ENT_QUOTES) . '</td></tr>';
            }
        } else {
            $rows .= '<tr><td width="70%">-</td><td width="30%" align="center">-</td></tr>';
        }

        $html = '<h2 style="text-align:center;">Annexe 3</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Matricule', $matricule);
        $html .= $this->tableRow('Mention', $mention);
        $html .= '</table>';
        $html .= '<br />';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= '<tr><th width="70%" align="left">Evaluation</th><th width="30%" align="center">Moyenne</th></tr>';
        $html .= $rows;
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Annexe 3');
        $this->setFooter($pdf);

        $suffix = $matricule !== '' ? $matricule : date('YmdHis');
        return $this->savePdf($pdf, 'annexe_3_' . $suffix . '.pdf');
    }
}
