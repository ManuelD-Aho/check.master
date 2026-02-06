<?php
declare(strict_types=1);

namespace App\Service\Document;

use DateTimeImmutable;

class RecuPaiementGenerator extends AbstractPdfGenerator
{
    public function generate(array $data): string
    {
        $pdf = $this->createPdf();
        $pdf->AddPage();

        $etudiant = $data['etudiant'] ?? null;
        $inscription = $data['inscription'] ?? null;
        $versement = $data['versement'] ?? null;
        $reference = $this->stringValue($data['reference'] ?? '');

        $etudiantNom = $this->formatPerson($etudiant);
        $matricule = $this->pick($etudiant, ['matricule', 'code', 'numero']);
        $filiere = $this->pick($inscription, ['filiere', 'parcours', 'programme']);
        $niveau = $this->pick($inscription, ['niveau', 'cycle']);
        $annee = $this->pick($inscription, ['annee', 'annee_academique', 'anneeAcademique']);
        $montant = $this->formatAmount($this->pick($versement, ['montant', 'amount', 'valeur']));
        $datePaiement = $this->formatDate($this->pick($versement, ['date', 'date_paiement', 'datePaiement']));
        $mode = $this->pick($versement, ['mode', 'mode_paiement', 'modePaiement']);
        $motif = $this->pick($versement, ['motif', 'libelle', 'description']);
        $dateEdition = (new DateTimeImmutable())->format('d/m/Y');

        $html = '<h2 style="text-align:center;">Recu de paiement</h2>';
        $html .= '<table cellpadding="4" cellspacing="0" border="1" width="100%">';
        $html .= $this->tableRow('Reference', $reference);
        $html .= $this->tableRow('Etudiant', $etudiantNom);
        $html .= $this->tableRow('Matricule', $matricule);
        $html .= $this->tableRow('Filiere', $filiere);
        $html .= $this->tableRow('Niveau', $niveau);
        $html .= $this->tableRow('Annee academique', $annee);
        $html .= $this->tableRow('Montant', $montant);
        $html .= $this->tableRow('Date paiement', $datePaiement);
        $html .= $this->tableRow('Mode paiement', $mode);
        $html .= $this->tableRow('Motif', $motif);
        $html .= $this->tableRow('Date edition', $dateEdition);
        $html .= '</table>';
        $html .= '<p>Le present recu confirme le versement effectue par l\'etudiant.</p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $this->setHeader($pdf, 'Recu de paiement');
        $this->setFooter($pdf);

        $suffix = $reference !== '' ? $reference : date('YmdHis');
        return $this->savePdf($pdf, 'recu_paiement_' . $suffix . '.pdf');
    }
}
