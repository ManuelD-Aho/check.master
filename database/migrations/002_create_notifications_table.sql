-- Migration: 002_create_notifications_table.sql
-- Purpose: Add table for in-app notifications (missing in 001)

CREATE TABLE IF NOT EXISTS notifications (
    id_notification INT PRIMARY KEY AUTO_INCREMENT,
    destinataire_id INT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lue BOOLEAN DEFAULT FALSE,
    lue_le DATETIME,
    lien VARCHAR(500),
    donnees_json JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_destinataire (destinataire_id),
    INDEX idx_lue (lue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
