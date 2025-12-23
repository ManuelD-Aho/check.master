<?php

declare(strict_types=1);

/**
 * Configuration PDF CheckMaster
 * 
 * Configuration des générateurs PDF (TCPDF/mPDF) et templates
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Générateur par défaut
    |--------------------------------------------------------------------------
    */
    'default_generator' => 'tcpdf',

    /*
    |--------------------------------------------------------------------------
    | Configuration TCPDF (Documents simples)
    |--------------------------------------------------------------------------
    */
    'tcpdf' => [
        'creator' => 'CheckMaster',
        'author' => 'UFR Mathématiques et Informatique - Université FHB',
        'header_logo' => 'ressources/images/logo_ufr.png',
        'header_logo_width' => 30,
        'header_title' => 'CheckMaster - Gestion des Mémoires',
        'header_string' => 'Université Félix Houphouët-Boigny',
        'page_orientation' => 'P',
        'unit' => 'mm',
        'page_format' => 'A4',
        'margins' => [
            'left' => 15,
            'top' => 27,
            'right' => 15,
        ],
        'font_family' => 'dejavusans',
        'font_size' => 10,
        'encoding' => 'UTF-8',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration mPDF (Documents avancés)
    |--------------------------------------------------------------------------
    */
    'mpdf' => [
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font_size' => 10,
        'default_font' => 'dejavusans',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 25,
        'margin_bottom' => 20,
        'margin_header' => 10,
        'margin_footer' => 10,
        'orientation' => 'P',
        'tempDir' => 'storage/temp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Types de documents PDF (13 types)
    |--------------------------------------------------------------------------
    */
    'types' => [
        // Documents TCPDF (simples)
        'recu_paiement' => [
            'generator' => 'tcpdf',
            'template' => 'recu_paiement.php',
            'orientation' => 'P',
            'format' => 'A5',
            'archiver' => true,
        ],
        'recu_penalite' => [
            'generator' => 'tcpdf',
            'template' => 'recu_penalite.php',
            'orientation' => 'P',
            'format' => 'A5',
            'archiver' => true,
        ],
        'bulletin_notes' => [
            'generator' => 'tcpdf',
            'template' => 'bulletin_notes.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
        ],
        'convocation_soutenance' => [
            'generator' => 'tcpdf',
            'template' => 'convocation_soutenance.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
        ],
        'convocation_jury' => [
            'generator' => 'tcpdf',
            'template' => 'convocation_jury.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
        ],
        'lettre_affectation' => [
            'generator' => 'tcpdf',
            'template' => 'lettre_affectation.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
        ],
        'fiche_notation' => [
            'generator' => 'tcpdf',
            'template' => 'fiche_notation.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => false,
        ],

        // Documents mPDF (avancés)
        'pv_commission' => [
            'generator' => 'mpdf',
            'template' => 'pv_commission.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
            'signature_requise' => true,
        ],
        'pv_soutenance' => [
            'generator' => 'mpdf',
            'template' => 'pv_soutenance.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
            'signature_requise' => true,
        ],
        'attestation_reussite' => [
            'generator' => 'mpdf',
            'template' => 'attestation_reussite.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
            'signature_requise' => true,
        ],
        'attestation_diplome' => [
            'generator' => 'mpdf',
            'template' => 'attestation_diplome.php',
            'orientation' => 'L',
            'format' => 'A4',
            'archiver' => true,
            'signature_requise' => true,
        ],
        'releve_notes' => [
            'generator' => 'mpdf',
            'template' => 'releve_notes.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => true,
        ],
        'rapport_activite' => [
            'generator' => 'mpdf',
            'template' => 'rapport_activite.php',
            'orientation' => 'P',
            'format' => 'A4',
            'archiver' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Répertoire des templates
    |--------------------------------------------------------------------------
    */
    'templates_path' => 'ressources/templates/pdf/',

    /*
    |--------------------------------------------------------------------------
    | Répertoire de stockage
    |--------------------------------------------------------------------------
    */
    'storage_path' => 'storage/documents/',

    /*
    |--------------------------------------------------------------------------
    | Configuration archive
    |--------------------------------------------------------------------------
    */
    'archive' => [
        'enabled' => true,
        'hash_algorithm' => 'sha256',
        'path' => 'storage/archives/',
        'verification_periodique' => true,
        'retention_years' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page de garde
    |--------------------------------------------------------------------------
    */
    'page_garde' => [
        'logo' => 'ressources/images/logo_ufr.png',
        'universite' => 'UNIVERSITÉ FÉLIX HOUPHOUËT-BOIGNY',
        'ufr' => 'UFR MATHÉMATIQUES ET INFORMATIQUE',
        'departement' => 'Département MIAGE',
        'annee_format' => 'Année académique {year}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mentions légales pied de page
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'text' => 'Document généré par CheckMaster - {date}',
        'page_format' => 'Page {page}/{total}',
        'confidentialite' => 'Document confidentiel - Reproduction interdite',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filigranes
    |--------------------------------------------------------------------------
    */
    'watermark' => [
        'enabled' => true,
        'text' => 'CheckMaster',
        'opacity' => 0.1,
        'angle' => 45,
    ],
];
