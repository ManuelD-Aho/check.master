-- =====================================================
-- Seed: 004_workflow_etats.sql
-- Purpose: Seed les 14 états workflow + transitions
-- Date: 2025-12-24
-- Ref: PRD 01 - Workflow états du dossier étudiant
-- =====================================================

-- Les 14 états du workflow CheckMaster
INSERT INTO workflow_etats (id_etat, code_etat, nom_etat, phase, delai_max_jours, ordre_affichage, couleur_hex, description) VALUES
(1,  'INSCRIT',               'Inscrit',                    'Inscription',    NULL, 1,  '#6c757d', 'Étudiant inscrit, dossier créé'),
(2,  'CANDIDATURE_SOUMISE',   'Candidature soumise',        'Candidature',    7,    2,  '#007bff', 'Candidature déposée, en attente vérification'),
(3,  'VERIFICATION_SCOLARITE','Vérification scolarité',     'Candidature',    5,    3,  '#17a2b8', 'Vérification paiement et documents par scolarité'),
(4,  'FILTRE_COMMUNICATION',  'Filtre communication',       'Candidature',    3,    4,  '#20c997', 'Vérification format rapport par communication'),
(5,  'EN_ATTENTE_COMMISSION', 'En attente commission',      'Commission',     NULL, 5,  '#ffc107', 'Rapport prêt pour évaluation commission'),
(6,  'EN_EVALUATION_COMMISSION','En évaluation commission', 'Commission',     1,    6,  '#fd7e14', 'Rapport en cours d''évaluation'),
(7,  'RAPPORT_VALIDE',        'Rapport validé',             'Commission',     NULL, 7,  '#28a745', 'Rapport validé par la commission'),
(8,  'ATTENTE_AVIS_ENCADREUR','Attente avis encadreur',     'Encadrement',    7,    8,  '#6610f2', 'En attente avis encadreur pédagogique'),
(9,  'PRET_POUR_JURY',        'Prêt pour jury',             'Soutenance',     NULL, 9,  '#e83e8c', 'Dossier prêt pour constitution jury'),
(10, 'JURY_EN_CONSTITUTION',  'Jury en constitution',       'Soutenance',     14,   10, '#6f42c1', 'Jury en cours de constitution'),
(11, 'SOUTENANCE_PLANIFIEE',  'Soutenance planifiée',       'Soutenance',     NULL, 11, '#17a2b8', 'Date de soutenance fixée'),
(12, 'SOUTENANCE_EN_COURS',   'Soutenance en cours',        'Soutenance',     1,    12, '#fd7e14', 'Soutenance en cours'),
(13, 'SOUTENANCE_TERMINEE',   'Soutenance terminée',        'Finalisation',   NULL, 13, '#28a745', 'Soutenance terminée, notes saisies'),
(14, 'DIPLOME_DELIVRE',       'Diplôme délivré',            'Finalisation',   NULL, 14, '#198754', 'Diplôme délivré, dossier archivé')
ON DUPLICATE KEY UPDATE 
    nom_etat = VALUES(nom_etat),
    phase = VALUES(phase),
    delai_max_jours = VALUES(delai_max_jours),
    couleur_hex = VALUES(couleur_hex),
    description = VALUES(description);

-- Les transitions entre états
INSERT INTO workflow_transitions (id_transition, etat_source_id, etat_cible_id, code_transition, nom_transition, roles_autorises, conditions_json, notifier) VALUES
-- Phase Inscription → Candidature
(1,  1, 2,  'SOUMETTRE_CANDIDATURE',    'Soumettre candidature',          '["etudiant"]',                '{"paiement_effectue": false}', TRUE),

-- Phase Candidature
(2,  2, 3,  'VALIDER_CANDIDATURE',      'Valider candidature',            '["scolarite", "admin"]',      '{}', TRUE),
(3,  2, 1,  'REJETER_CANDIDATURE',      'Rejeter candidature',            '["scolarite", "admin"]',      '{}', TRUE),
(4,  3, 4,  'VALIDER_PAIEMENT',         'Valider paiement',               '["scolarite", "admin"]',      '{"paiement_complet": true}', TRUE),
(5,  3, 2,  'RETOUR_CANDIDATURE',       'Retour candidature',             '["scolarite", "admin"]',      '{}', TRUE),
(6,  4, 5,  'VALIDER_FORMAT',           'Valider format rapport',         '["communication", "admin"]',  '{}', TRUE),
(7,  4, 3,  'REJETER_FORMAT',           'Rejeter format rapport',         '["communication", "admin"]',  '{}', TRUE),

-- Phase Commission
(8,  5, 6,  'DEMARRER_EVALUATION',      'Démarrer évaluation',            '["commission", "president_commission"]', '{}', TRUE),
(9,  6, 7,  'VALIDER_RAPPORT',          'Valider rapport',                '["commission"]',              '{"vote_unanime": true}', TRUE),
(10, 6, 5,  'DEMANDER_REVISION',        'Demander révision',              '["commission"]',              '{}', TRUE),
(11, 6, 4,  'RETOUR_COMMUNICATION',     'Retour communication',           '["commission"]',              '{}', TRUE),

-- Phase Encadrement
(12, 7, 8,  'DEMANDER_AVIS_ENCADREUR',  'Demander avis encadreur',        '["scolarite", "admin"]',      '{}', TRUE),
(13, 8, 9,  'AVIS_FAVORABLE',           'Avis favorable encadreur',       '["encadreur", "admin"]',      '{}', TRUE),
(14, 8, 7,  'AVIS_DEFAVORABLE',         'Avis défavorable',               '["encadreur", "admin"]',      '{}', TRUE),

-- Phase Soutenance
(15, 9,  10, 'CONSTITUER_JURY',         'Constituer jury',                '["president_commission", "admin"]', '{}', TRUE),
(16, 10, 11, 'PLANIFIER_SOUTENANCE',    'Planifier soutenance',           '["scolarite", "admin"]',      '{"jury_complet": true}', TRUE),
(17, 10, 9,  'ANNULER_JURY',            'Annuler jury',                   '["president_commission", "admin"]', '{}', TRUE),
(18, 11, 12, 'DEMARRER_SOUTENANCE',     'Démarrer soutenance',            '["president_jury"]',          '{"code_valide": true}', TRUE),
(19, 11, 10, 'REPORTER_SOUTENANCE',     'Reporter soutenance',            '["scolarite", "admin"]',      '{}', TRUE),
(20, 12, 13, 'TERMINER_SOUTENANCE',     'Terminer soutenance',            '["president_jury"]',          '{"notes_saisies": true}', TRUE),

-- Phase Finalisation
(21, 13, 14, 'DELIVRER_DIPLOME',        'Délivrer diplôme',               '["scolarite", "admin"]',      '{"pv_genere": true}', TRUE)
ON DUPLICATE KEY UPDATE 
    etat_source_id = VALUES(etat_source_id),
    etat_cible_id = VALUES(etat_cible_id),
    nom_transition = VALUES(nom_transition),
    roles_autorises = VALUES(roles_autorises),
    conditions_json = VALUES(conditions_json),
    notifier = VALUES(notifier);

-- Rôles de jury
INSERT INTO roles_jury (id_role, code_role, libelle_role, ordre_affichage) VALUES
(1, 'PRESIDENT',      'Président du jury',        1),
(2, 'DIRECTEUR',      'Directeur de mémoire',     2),
(3, 'RAPPORTEUR',     'Rapporteur',               3),
(4, 'EXAMINATEUR',    'Examinateur',              4),
(5, 'INVITE',         'Membre invité',            5),
(6, 'MAITRE_STAGE',   'Maître de stage',          6)
ON DUPLICATE KEY UPDATE 
    libelle_role = VALUES(libelle_role),
    ordre_affichage = VALUES(ordre_affichage);

-- Niveaux d'escalade
INSERT INTO escalade_niveaux (id_niveau, niveau, nom_niveau, delai_reponse_jours) VALUES
(1, 1, 'Responsable de niveau',        3),
(2, 2, 'Responsable de filière',       5),
(3, 3, 'Directeur adjoint',            7),
(4, 4, 'Doyen',                        10)
ON DUPLICATE KEY UPDATE 
    nom_niveau = VALUES(nom_niveau),
    delai_reponse_jours = VALUES(delai_reponse_jours);

-- Niveaux d'approbation
INSERT INTO niveau_approbation (id_niveau_approbation, lib_niveau, ordre_niveau) VALUES
(1, 'Auto-validation',          1),
(2, 'Validation scolarité',     2),
(3, 'Validation responsable',   3),
(4, 'Validation commission',    4),
(5, 'Validation direction',     5)
ON DUPLICATE KEY UPDATE 
    lib_niveau = VALUES(lib_niveau),
    ordre_niveau = VALUES(ordre_niveau);
