<?php
declare(strict_types=1);

namespace App\Service\Document;

class CompteRenduCommissionGenerator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $session = $data['session'] ?? null;
        $rapports = is_array($data['rapports'] ?? null) ? $data['rapports'] : [];
        $evaluations = is_array($data['evaluations'] ?? null) ? $data['evaluations'] : [];
        $membres = is_array($data['membres'] ?? null) ? $data['membres'] : [];

        $sessionLibelle = $this->pick($session, ['libelle', 'titre', 'nom']);
        $sessionDate = $this->formatDate($this->pick($session, ['date', 'date_session', 'dateSession']));

        $rapportItems = [];
        foreach ($rapports as $rapport) {
            $rapportItems[] = $this->stringValue($rapport);
        }

        $evaluationItems = [];
        foreach ($evaluations as $evaluation) {
            $evaluationItems[] = $this->stringValue($evaluation);
        }

        $membreItems = [];
        foreach ($membres as $membre) {
            $membreItems[] = $this->formatPerson($membre);
        }

        $html = '<h2 style="text-align:center;">Compte rendu de commission</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Session', $sessionLibelle);
        $html .= $this->tableRow('Date', $sessionDate);
        $html .= '</table>';
        $html .= '<h3>Membres</h3>';
        $html .= $this->listItems($membreItems);
        $html .= '<h3>Rapports</h3>';
        $html .= $this->listItems($rapportItems);
        $html .= '<h3>Evaluations</h3>';
        $html .= $this->listItems($evaluationItems);

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Compte rendu de commission');
        $this->setFooter($pdf);

        $suffix = $sessionDate !== '' ? str_replace('/', '', $sessionDate) : date('YmdHis');
        return $this->savePdf($pdf, 'compte_rendu_commission_' . $suffix . '.pdf');
    }
}
