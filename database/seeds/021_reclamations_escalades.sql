-- =====================================================
-- Seed: 021_reclamations_escalades.sql
-- Purpose: Réclamations étudiantes et escalades
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Gestion des réclamations
-- =====================================================

-- Réclamations
INSERT INTO reclamations (id_reclamation, etudiant_id, type_reclamation, priorite, sujet, description, statut, prise_en_charge_par) VALUES
-- Réclamations résolues
(1, 12, 'Financiere', 'Haute', 'Erreur de calcul sur le montant des pénalités', 
'Bonjour,\n\nJe constate que la pénalité de retard qui m''a été appliquée est de 50,000 FCFA alors que mon retard n''était que de 10 jours.\n\nSelon le règlement, le taux est de 0.5% par jour, ce qui devrait donner 27,500 FCFA et non 50,000 FCFA.\n\nMerci de vérifier ce calcul.\n\nCordialement,\nTAPE Didier', 
'Resolue', 30),

(2, 8, 'Academique', 'Normale', 'Demande de révision de la note de soutenance',
'Bonjour,\n\nJe souhaiterais avoir plus de détails sur l''évaluation de ma soutenance, notamment sur les critères utilisés pour attribuer la note de forme.\n\nJe vous remercie.',
'Resolue', 80),

-- Réclamations en cours
(3, 11, 'Administrative', 'Normale', 'Retard dans le traitement de ma candidature',
'Bonjour,\n\nMa candidature a été soumise il y a plus de 15 jours et elle est toujours en attente de validation par le service scolarité.\n\nPourriez-vous me donner une estimation du délai de traitement?\n\nMerci.',
'En_cours', 31),

(4, 23, 'Technique', 'Haute', 'Impossible de télécharger mon reçu de paiement',
'Bonjour,\n\nLorsque je clique sur "Télécharger le reçu" dans mon espace étudiant, j''obtiens une erreur 500.\n\nCela fait 3 jours que le problème persiste.\n\nMerci de résoudre ce problème.',
'En_cours', 1),

-- Réclamation ouverte
(5, 28, 'Financiere', 'Basse', 'Demande d''échelonnement de paiement',
'Bonjour,\n\nSuite à des difficultés financières temporaires, je souhaiterais solliciter un échelonnement pour le solde restant de mes frais de scolarité (150,000 FCFA).\n\nJe m''engage à régler ce montant en 3 versements mensuels.\n\nMerci de considérer ma demande.',
'En_attente', NULL)
ON DUPLICATE KEY UPDATE 
    statut = VALUES(statut),
    prise_en_charge_par = VALUES(prise_en_charge_par);

-- Réponses aux réclamations
INSERT INTO reclamation_reponses (id_reponse, reclamation_id, auteur_id, contenu) VALUES
-- Réponses à la réclamation 1
(1, 1, 30, 'Bonjour Monsieur TAPE,\n\nAprès vérification, nous confirmons effectivement une erreur de calcul. La pénalité correcte est de 27,500 FCFA.\n\nNous avons procédé à la correction. Votre nouveau solde sera mis à jour dans les 24h.\n\nNous vous prions de nous excuser pour ce désagrément.\n\nService Scolarité'),
(2, 1, 12, 'Merci pour votre réactivité. Je confirme que le montant a bien été corrigé.'),

-- Réponses à la réclamation 2
(3, 2, 80, 'Bonjour,\n\nSuite à votre demande, voici le détail de l''évaluation:\n\n- Note de fond: 15/20 (analyse méthodologique perfectible)\n- Note de forme: 14/20 (mise en page à améliorer)\n- Note de soutenance: 15/20 (bonne présentation orale)\n\nCes notes ont été attribuées selon la grille de critères en vigueur.\n\nCordialement,\nPrésident de la Commission'),

-- Réponses à la réclamation 3
(4, 3, 31, 'Bonjour,\n\nNous avons bien reçu votre réclamation. Le retard est dû à un afflux important de candidatures ce mois-ci.\n\nVotre dossier sera traité en priorité dans les 48h.\n\nService Scolarité')
ON DUPLICATE KEY UPDATE 
    contenu = VALUES(contenu);

-- Escalades
INSERT INTO escalades (id_escalade, dossier_id, type_escalade, niveau_escalade, description, statut, cree_par, assignee_a) VALUES
-- Escalade résolue (Commission bloquée)
(1, 5, 'commission_blocage', 2, 'Blocage au tour 2 - pas d''unanimité après discussion', 
'Resolue', 1, 80),

-- Escalade en cours (Délai SLA dépassé)
(2, 6, 'delai_depasse', 1, 'Délai de 7 jours dépassé pour l''avis encadreur',
'En_cours', 1, 50),

-- Escalade ouverte (Absence jury)
(3, 4, 'jury_incomplet', 1, 'Un membre du jury a décliné sa participation à J-5',
'Ouverte', 1, 80)
ON DUPLICATE KEY UPDATE 
    statut = VALUES(statut),
    assignee_a = VALUES(assignee_a);

-- Actions sur escalades
INSERT INTO escalades_actions (id_action, escalade_id, type_action, utilisateur_id, description) VALUES
-- Actions sur escalade 1
(1, 1, 'Prise_en_charge', 80, 'Escalade prise en charge pour médiation'),
(2, 1, 'Communication', 80, 'Réunion organisée avec les membres divergents'),
(3, 1, 'Resolution', 80, 'Après discussion, consensus trouvé. Rapport validé.'),

-- Actions sur escalade 2
(4, 2, 'Prise_en_charge', 50, 'Escalade prise en charge - contact de l''encadreur'),
(5, 2, 'Communication', 50, 'Message envoyé à Dr. SANOGO Mariam'),
(6, 2, 'Relance', 50, 'Relance téléphonique effectuée'),

-- Actions sur escalade 3
(7, 3, 'Prise_en_charge', 80, 'Recherche d''un remplaçant en cours')
ON DUPLICATE KEY UPDATE 
    description = VALUES(description);
