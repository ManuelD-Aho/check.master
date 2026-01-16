<?php

/**
 * Script de secours pour réinitialiser le mot de passe Admin
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\Security\ServiceAuthentification;
use App\Models\Utilisateur;

// Initialisation basique
require_once __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Réinitialisation du mot de passe Admin</h1>";

// 1. Chercher l'admin
$admin = Utilisateur::findByLogin('admin@checkmaster.ufhb.ci');

if (!$admin) {
    die("<p style='color:red'>Erreur : L'utilisateur admin@checkmaster.ufhb.ci n'existe pas en base de données.</p>");
}

// 2. Générer le nouveau hash
$authService = new ServiceAuthentification();
$nouveauMotDePasse = 'password'; // Mot de passe simple pour tester
$hash = $authService->hasherMotDePasse($nouveauMotDePasse);

// 3. Mettre à jour
$admin->mdp_utilisateur = $hash;
$admin->statut_utilisateur = 'Actif'; // On s'assure qu'il est actif
$admin->tentatives_echec = 0; // On remet à zéro les échecs
$admin->verrouille_jusqu_a = null; // On déverrouille
$admin->save();

echo "<p style='color:green'>✅ Succès ! Le mot de passe a été réinitialisé.</p>";
echo "<ul>";
echo "<li><strong>Login :</strong> admin@checkmaster.ufhb.ci</li>";
echo "<li><strong>Nouveau mot de passe :</strong> password</li>";
echo "</ul>";
echo "<p><a href='/connexion'>Se connecter maintenant</a></p>";