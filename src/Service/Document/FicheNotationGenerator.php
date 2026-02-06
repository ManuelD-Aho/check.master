<?php
declare(strict_types=1);

namespace App\Service\Document;

class FicheNotationGenerator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $soutenance = $data['soutenance'] ?? null;
        $criteres = is_array($data['criteres'] ?? null) ? $data['criteres'] : [];
        $baremes = is_array($data['baremes'] ?? null) ? $data['baremes'] : [];

        $etudiant = $this->pick($soutenance, ['etudiant', 'candidat']);
        $etudiantNom = $this->formatPerson($etudiant);
        $sujet = $this->pick($soutenance, ['sujet', 'theme', 'intitule']);
        $dateSoutenance = $this->formatDate($this->pick($soutenance, ['date', 'date_soutenance', 'dateSoutenance']));

        $rows = '';

        if ($criteres !== []) {
            foreach ($criteres as $index => $critere) {
                $critereLabel = $this->stringValue($critere);
                $baremeValue = '';
                $noteValue = '';

                if (array_key_exists($index, $baremes)) {
                    $entry = $baremes[$index];
                    if (is_array($entry)) {
                        $baremeValue = $this->stringValue($entry['bareme'] ?? $entry['valeur'] ?? $entry['note_max'] ?? '');
                        $noteValue = $this->stringValue($entry['note'] ?? '');
                    } else {
                        $baremeValue = $this->stringValue($entry);
                    }
                }

                $rows .= '<tr><td width="55%">' . htmlspecialchars($critereLabel !== '' ? $critereLabel : '-', ENT_QUOTES) . '</td>';
                $rows .= '<td width="20%" align="center">' . htmlspecialchars($baremeValue !== '' ? $baremeValue : '-', ENT_QUOTES) . '</td>';
                $rows .= '<td width="25%" align="center">' . htmlspecialchars($noteValue !== '' ? $noteValue : '-', ENT_QUOTES) . '</td></tr>';
            }
        } elseif ($baremes !== []) {
            foreach ($baremes as $entry) {
                $critereLabel = '';
                $baremeValue = '';
                $noteValue = '';

                if (is_array($entry)) {
                    $critereLabel = $this->stringValue($entry['critere'] ?? $entry['libelle'] ?? '');
                    $baremeValue = $this->stringValue($entry['bareme'] ?? $entry['valeur'] ?? $entry['note_max'] ?? '');
                    $noteValue = $this->stringValue($entry['note'] ?? '');
                } else {
                    $critereLabel = $this->stringValue($entry);
                }

                $rows .= '<tr><td width="55%">' . htmlspecialchars($critereLabel !== '' ? $critereLabel : '-', ENT_QUOTES) . '</td>';
                $rows .= '<td width="20%" align="center">' . htmlspecialchars($baremeValue !== '' ? $baremeValue : '-', ENT_QUOTES) . '</td>';
                $rows .= '<td width="25%" align="center">' . htmlspecialchars($noteValue !== '' ? $noteValue : '-', ENT_QUOTES) . '</td></tr>';
            }
        } else {
            $rows .= '<tr><td width="55%">-</td><td width="20%" align="center">-</td><td width="25%" align="center">-</td></tr>';
        }

        $html = '<h2 style="text-align:center;">Fiche de notation</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Sujet', $sujet);
        $html .= $this->tableRow('Date soutenance', $dateSoutenance);
        $html .= '</table>';
        $html .= '<br />';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= '<tr><th width="55%" align="left">Critere</th><th width="20%" align="center">Bareme</th><th width="25%" align="center">Note</th></tr>';
        $html .= $rows;
        $html .= '</table>';
        $html .= '<p>Signature du jury</p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Fiche de notation');
        $this->setFooter($pdf);

        $suffix = $dateSoutenance !== '' ? str_replace('/', '', $dateSoutenance) : date('YmdHis');
        return $this->savePdf($pdf, 'fiche_notation_' . $suffix . '.pdf');
    }
}
