-- =====================================================
-- Seed: 008_entreprises_partenaires.sql
-- Purpose: Entreprises partenaires pour stages
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Entreprises de stage
-- =====================================================

INSERT INTO entreprises (id_entreprise, nom_entreprise, secteur_activite, adresse, telephone, email, site_web, actif) VALUES
-- Grandes entreprises internationales
(1, 'Orange Côte d''Ivoire', 'Télécommunications', 'Plateau, Abidjan, Côte d''Ivoire', '+225 21 23 90 00', 'contact@orange.ci', 'https://www.orange.ci', TRUE),
(2, 'MTN Côte d''Ivoire', 'Télécommunications', 'Cocody, Abidjan, Côte d''Ivoire', '+225 05 70 00 00', 'contact@mtn.ci', 'https://www.mtn.ci', TRUE),
(3, 'Moov Africa', 'Télécommunications', 'Marcory, Abidjan, Côte d''Ivoire', '+225 01 01 00 00', 'contact@moov-africa.ci', 'https://www.moov-africa.ci', TRUE),
(4, 'Société Générale Côte d''Ivoire', 'Banque et Finance', 'Plateau, Abidjan, Côte d''Ivoire', '+225 20 20 12 00', 'contact@sgci.ci', 'https://www.sgci.ci', TRUE),
(5, 'BICICI', 'Banque et Finance', 'Plateau, Abidjan, Côte d''Ivoire', '+225 20 20 16 00', 'contact@bicici.com', 'https://www.bicici.com', TRUE),
(6, 'Ecobank Côte d''Ivoire', 'Banque et Finance', 'Plateau, Abidjan, Côte d''Ivoire', '+225 20 31 92 00', 'contact@ecobank.ci', 'https://www.ecobank.com', TRUE),

-- Entreprises technologiques locales
(7, 'QuanTech Solutions', 'Services Informatiques', 'Cocody Riviera, Abidjan', '+225 07 08 09 10', 'contact@quantech.ci', 'https://www.quantech.ci', TRUE),
(8, 'NSIA Technologies', 'Assurance et Technologies', 'Plateau, Abidjan', '+225 20 31 88 00', 'it@nsia.ci', 'https://www.nsia.ci', TRUE),
(9, 'Deloitte Côte d''Ivoire', 'Conseil et Audit', 'Cocody, Abidjan', '+225 22 40 40 40', 'abidjan@deloitte.ci', 'https://www.deloitte.com', TRUE),
(10, 'PwC Côte d''Ivoire', 'Conseil et Audit', 'Plateau, Abidjan', '+225 20 31 54 00', 'ci_info@pwc.com', 'https://www.pwc.com', TRUE),

-- Entreprises industrielles
(11, 'SODECI', 'Distribution d''eau', 'Treichville, Abidjan', '+225 21 23 30 00', 'contact@sodeci.ci', 'https://www.sodeci.ci', TRUE),
(12, 'CIE', 'Distribution d''électricité', 'Treichville, Abidjan', '+225 21 23 33 00', 'contact@cie.ci', 'https://www.cie.ci', TRUE),
(13, 'Port Autonome d''Abidjan', 'Transport Maritime', 'Vridi, Abidjan', '+225 21 23 80 00', 'paa@paa-ci.org', 'https://www.paa-ci.org', TRUE),
(14, 'Air Côte d''Ivoire', 'Transport Aérien', 'Aéroport FHB, Abidjan', '+225 21 35 71 00', 'info@aircotedivoire.com', 'https://www.aircotedivoire.com', TRUE),

-- Entreprises agroalimentaires
(15, 'SIFCA Group', 'Agroalimentaire', 'Abidjan', '+225 21 75 33 00', 'contact@groupesifca.com', 'https://www.groupesifca.com', TRUE),
(16, 'CFAO Motors', 'Automobile', 'Zone 4, Abidjan', '+225 21 21 93 00', 'contact@cfao.ci', 'https://www.cfao.com', TRUE),

-- Startups et PME innovantes
(17, 'Jumia Côte d''Ivoire', 'E-commerce', 'Cocody, Abidjan', '+225 22 52 00 00', 'ci@jumia.com', 'https://www.jumia.ci', TRUE),
(18, 'Wave Côte d''Ivoire', 'Fintech / Mobile Money', 'Cocody, Abidjan', '+225 01 02 03 04', 'support@wave.com', 'https://www.wave.com', TRUE),
(19, 'Afriland First Bank', 'Banque', 'Plateau, Abidjan', '+225 20 25 60 00', 'afb.ci@afrilandfirstbank.com', 'https://www.afrilandfirstbank.com', TRUE),
(20, 'Koffi & Diabaté', 'Cabinet Juridique', 'Plateau, Abidjan', '+225 20 22 45 67', 'contact@kd-avocats.ci', NULL, TRUE),

-- Administration publique
(21, 'Ministère de l''Économie Numérique', 'Administration Publique', 'Plateau, Abidjan', '+225 20 21 35 00', 'info@men.gouv.ci', 'https://www.men.gouv.ci', TRUE),
(22, 'ARTCI', 'Régulation Télécoms', 'Cocody, Abidjan', '+225 20 34 43 73', 'info@artci.ci', 'https://www.artci.ci', TRUE),
(23, 'INS (Institut National de la Statistique)', 'Statistiques Publiques', 'Plateau, Abidjan', '+225 20 21 05 38', 'contact@ins.ci', 'https://www.ins.ci', TRUE),
(24, 'DGBF (Direction Générale du Budget)', 'Finances Publiques', 'Plateau, Abidjan', '+225 20 20 09 20', 'dgbf@finances.gouv.ci', 'https://www.budget.gouv.ci', TRUE),

-- Organisations internationales
(25, 'Banque Mondiale - Bureau Côte d''Ivoire', 'Organisation Internationale', 'Cocody, Abidjan', '+225 22 40 04 00', 'abidjan@worldbank.org', 'https://www.worldbank.org', TRUE),
(26, 'BAD (Banque Africaine de Développement)', 'Organisation Internationale', 'Plateau, Abidjan', '+225 20 26 10 20', 'afdb@afdb.org', 'https://www.afdb.org', TRUE),
(27, 'PNUD Côte d''Ivoire', 'Organisation Internationale', 'Cocody, Abidjan', '+225 22 51 10 00', 'registry.ci@undp.org', 'https://www.undp.org', TRUE),
(28, 'UNESCO Bureau Abidjan', 'Organisation Internationale', 'Cocody, Abidjan', '+225 22 44 23 70', 'abidjan@unesco.org', 'https://www.unesco.org', TRUE),

-- Entreprises de formation et services
(29, 'PIGIER Côte d''Ivoire', 'Formation Professionnelle', 'Cocody, Abidjan', '+225 22 44 88 88', 'contact@pigier.ci', 'https://www.pigier.ci', TRUE),
(30, 'AGITEL Formation', 'Formation IT', 'Marcory, Abidjan', '+225 21 26 75 00', 'info@agitelformation.ci', 'https://www.agitelformation.ci', TRUE)
ON DUPLICATE KEY UPDATE 
    nom_entreprise = VALUES(nom_entreprise),
    secteur_activite = VALUES(secteur_activite),
    email = VALUES(email),
    actif = VALUES(actif);
