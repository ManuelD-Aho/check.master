<?php
declare(strict_types=1);

namespace App\Service\Document;

class PvSoutenanceGenerator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $soutenance = $data['soutenance'] ?? null;
        $jury = is_array($data['jury'] ?? null) ? $data['jury'] : [];
        $notes = is_array($data['notes'] ?? null) ? $data['notes'] : [];
        $resultat = $this->stringValue($data['resultat'] ?? '');
        $type = $this->stringValue($data['type'] ?? 'standard');

        $etudiant = $this->pick($soutenance, ['etudiant', 'candidat']);
        $etudiantNom = $this->formatPerson($etudiant);
        $sujet = $this->pick($soutenance, ['sujet', 'theme', 'intitule']);
        $dateSoutenance = $this->formatDate($this->pick($soutenance, ['date', 'date_soutenance', 'dateSoutenance']));
        $typeLabel = strtolower($type) === 'simplifie' ? 'Simplifie' : 'Standard';

        $juryItems = [];
        foreach ($jury as $membre) {
            $juryItems[] = $this->formatPerson($membre);
        }

        $notesItems = [];
        foreach ($notes as $note) {
            if (is_array($note)) {
                $membre = $this->stringValue($note['membre'] ?? $note['nom'] ?? '');
                $valeur = $this->stringValue($note['note'] ?? $note['valeur'] ?? '');
                $notesItems[] = trim($membre . ' : ' . ($valeur !== '' ? $valeur : '-'));
                continue;
            }
            $notesItems[] = $this->stringValue($note);
        }

        $html = '<h2 style="text-align:center;">Proces-verbal de soutenance</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Sujet', $sujet);
        $html .= $this->tableRow('Date soutenance', $dateSoutenance);
        $html .= $this->tableRow('Type', $typeLabel);
        $html .= $this->tableRow('Resultat', $resultat);
        $html .= '</table>';
        $html .= '<h3>Jury</h3>';
        $html .= $this->listItems($juryItems);
        $html .= '<h3>Notes</h3>';
        $html .= $this->listItems($notesItems);

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Proces-verbal de soutenance');
        $this->setFooter($pdf);

        $suffix = $dateSoutenance !== '' ? str_replace('/', '', $dateSoutenance) : date('YmdHis');
        return $this->savePdf($pdf, 'pv_soutenance_' . $suffix . '.pdf');
    }
}
