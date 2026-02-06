<?php
declare(strict_types=1);

namespace App\Service\Document;

use DateTimeImmutable;

class PvFinalGeneratorService extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $session = $data['session'] ?? null;
        $resultats = is_array($data['resultats'] ?? null) ? $data['resultats'] : [];
        $jury = is_array($data['jury'] ?? null) ? $data['jury'] : [];
        $annee = $this->stringValue($data['annee'] ?? $data['annee_academique'] ?? '');
        $filiere = $this->stringValue($data['filiere'] ?? '');
        $niveau = $this->stringValue($data['niveau'] ?? '');

        $dateSession = $this->formatDate($this->pick($session, ['date', 'date_session', 'dateSession']));
        $lieu = $this->pick($session, ['lieu', 'salle', 'location']);
        $dateEdition = (new DateTimeImmutable())->format('d/m/Y');

        $html = '<h2 style="text-align:center;">Proces-verbal final de deliberation</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Annee academique', $annee);
        $html .= $this->tableRow('Filiere', $filiere);
        $html .= $this->tableRow('Niveau', $niveau);
        $html .= $this->tableRow('Date de session', $dateSession);
        $html .= $this->tableRow('Lieu', $lieu);
        $html .= $this->tableRow('Date edition', $dateEdition);
        $html .= '</table>';

        if ($jury !== []) {
            $html .= '<br/><h3>Composition du jury</h3>';
            $juryItems = [];
            foreach ($jury as $membre) {
                $juryItems[] = $this->formatPerson($membre);
            }
            $html .= $this->listItems($juryItems);
        }

        $html .= '<br/><h3>Resultats</h3>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= '<tr><th width="10%"><strong>N°</strong></th><th width="30%"><strong>Etudiant</strong></th><th width="20%"><strong>Matricule</strong></th><th width="15%"><strong>Moyenne</strong></th><th width="25%"><strong>Decision</strong></th></tr>';

        $num = 0;
        foreach ($resultats as $resultat) {
            if (!is_array($resultat)) {
                continue;
            }

            $num++;
            $nom = $this->formatPerson($resultat['etudiant'] ?? $resultat);
            $mat = $this->stringValue($resultat['matricule'] ?? '');
            $moy = $this->stringValue($resultat['moyenne'] ?? '');
            $decision = $this->stringValue($resultat['decision'] ?? $resultat['mention'] ?? '');

            $html .= '<tr>';
            $html .= '<td style="text-align:center;">' . $num . '</td>';
            $html .= '<td>' . htmlspecialchars($nom !== '' ? $nom : '-', ENT_QUOTES) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($mat !== '' ? $mat : '-', ENT_QUOTES) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($moy !== '' ? $moy : '-', ENT_QUOTES) . '</td>';
            $html .= '<td>' . htmlspecialchars($decision !== '' ? $decision : '-', ENT_QUOTES) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $observations = $this->stringValue($data['observations'] ?? '');
        if ($observations !== '') {
            $html .= '<br/><h3>Observations</h3>';
            $html .= '<p>' . htmlspecialchars($observations, ENT_QUOTES) . '</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Proces-verbal final');
        $this->setFooter($pdf);

        $suffix = $dateSession !== '' ? str_replace('/', '', $dateSession) : date('YmdHis');
        return $this->savePdf($pdf, 'pv_final_' . $suffix . '.pdf');
    }
}
