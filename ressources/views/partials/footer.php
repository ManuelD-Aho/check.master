<?php

declare(strict_types=1);
/**
 * CheckMaster - Footer Partial
 * ==============================
 * Pied de page de l'application
 */

$currentYear = date('Y');
?>

<footer class="footer">
    <div>
        &copy; <?= $currentYear ?> CheckMaster UFHB 2.0 | UFR Mathématiques et Informatique
    </div>
    <div>
        <a href="/aide">Aide</a>
        <span style="margin: 0 8px;">•</span>
        <a href="/contact">Contact</a>
        <span style="margin: 0 8px;">•</span>
        <span>v2.0.0</span>
    </div>
</footer>