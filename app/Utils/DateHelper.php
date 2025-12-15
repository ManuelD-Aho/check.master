<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour les dates
 * 
 * Utilitaires pour la manipulation de dates.
 */
class DateHelper
{
    /**
     * Format de date par défaut
     */
    public const FORMAT_DATE = 'd/m/Y';
    public const FORMAT_DATETIME = 'd/m/Y H:i';
    public const FORMAT_TIME = 'H:i';
    public const FORMAT_SQL = 'Y-m-d H:i:s';

    /**
     * Formate une date pour affichage
     */
    public static function format(?string $date, string $format = self::FORMAT_DATE): string
    {
        if (empty($date)) {
            return '';
        }

        $dt = new \DateTime($date);
        return $dt->format($format);
    }

    /**
     * Formate une date en français (Mardi 15 décembre 2025)
     */
    public static function formatFr(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $dt = new \DateTime($date);

        $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $mois = [
            '',
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
        ];

        $jour = $jours[(int) $dt->format('w')];
        $numJour = $dt->format('j');
        $nomMois = $mois[(int) $dt->format('n')];
        $annee = $dt->format('Y');

        return "{$jour} {$numJour} {$nomMois} {$annee}";
    }

    /**
     * Retourne une date relative (il y a 5 minutes, etc.)
     */
    public static function relative(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $dt = new \DateTime($date);
        $now = new \DateTime();
        $diff = $now->diff($dt);

        if ($diff->invert === 0) {
            return 'dans le futur';
        }

        if ($diff->y > 0) {
            return $diff->y === 1 ? 'il y a 1 an' : "il y a {$diff->y} ans";
        }
        if ($diff->m > 0) {
            return $diff->m === 1 ? 'il y a 1 mois' : "il y a {$diff->m} mois";
        }
        if ($diff->d > 0) {
            return $diff->d === 1 ? 'hier' : "il y a {$diff->d} jours";
        }
        if ($diff->h > 0) {
            return $diff->h === 1 ? 'il y a 1 heure' : "il y a {$diff->h} heures";
        }
        if ($diff->i > 0) {
            return $diff->i === 1 ? 'il y a 1 minute' : "il y a {$diff->i} minutes";
        }

        return 'à l\'instant';
    }

    /**
     * Calcule la différence en jours entre deux dates
     */
    public static function diffJours(string $date1, string $date2): int
    {
        $dt1 = new \DateTime($date1);
        $dt2 = new \DateTime($date2);

        return (int) $dt1->diff($dt2)->days;
    }

    /**
     * Vérifie si une date est dans le passé
     */
    public static function estPasse(string $date): bool
    {
        return strtotime($date) < time();
    }

    /**
     * Vérifie si une date est dans le futur
     */
    public static function estFutur(string $date): bool
    {
        return strtotime($date) > time();
    }

    /**
     * Vérifie si une date est aujourd'hui
     */
    public static function estAujourdhui(string $date): bool
    {
        return date('Y-m-d', strtotime($date)) === date('Y-m-d');
    }

    /**
     * Retourne le début de la journée
     */
    public static function debutJournee(?string $date = null): \DateTime
    {
        $dt = $date ? new \DateTime($date) : new \DateTime();
        return $dt->setTime(0, 0, 0);
    }

    /**
     * Retourne la fin de la journée
     */
    public static function finJournee(?string $date = null): \DateTime
    {
        $dt = $date ? new \DateTime($date) : new \DateTime();
        return $dt->setTime(23, 59, 59);
    }

    /**
     * Ajoute des jours à une date
     */
    public static function ajouterJours(string $date, int $jours): string
    {
        $dt = new \DateTime($date);
        $dt->modify("+{$jours} days");
        return $dt->format(self::FORMAT_SQL);
    }

    /**
     * Retourne l'année académique pour une date (ex: 2024-2025)
     */
    public static function anneeAcademique(?string $date = null): string
    {
        $dt = $date ? new \DateTime($date) : new \DateTime();
        $annee = (int) $dt->format('Y');
        $mois = (int) $dt->format('n');

        // Si avant septembre, c'est l'année précédente
        if ($mois < 9) {
            return ($annee - 1) . '-' . $annee;
        }

        return $annee . '-' . ($annee + 1);
    }
}
