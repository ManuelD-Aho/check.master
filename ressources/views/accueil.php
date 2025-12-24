<?php

/**
 * Page d'accueil publique CheckMaster
 * ====================================
 * Page de présentation du système de gestion des mémoires
 */

declare(strict_types=1);

// Stats pour affichage (en production, depuis la BDD)
$stats = [
    'etudiants' => 40,
    'soutenances' => 125,
    'taux_reussite' => 98,
    'entreprises' => 30
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CheckMaster - Système de gestion des mémoires de fin d'études de l'UFR Mathématiques et Informatique de l'Université Félix Houphouët-Boigny">
    <title>CheckMaster - Gestion des Mémoires UFHB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-900: #0d1f3c;
            --primary-800: #1a365d;
            --primary-700: #2b4c7e;
            --primary-600: #3d5a80;
            --primary-100: #e8eef6;
            --primary-50: #f5f7fa;
            --accent-600: #2c9a94;
            --accent-500: #38b2ac;
            --accent-400: #4fd1c5;
            --success-500: #22c55e;
            --warning-500: #f59e0b;
            --gray-900: #111827;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-400: #9ca3af;
            --gray-200: #e5e7eb;
            --gray-100: #f3f4f6;
            --gray-50: #f9fafb;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gray-700);
            line-height: 1.6;
            background: var(--white);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           HEADER / NAVIGATION
           ═══════════════════════════════════════════════════════════════════════════ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gray-200);
            padding: 0 2rem;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--primary-800);
        }

        .navbar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-800) 0%, var(--accent-500) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 800;
            font-size: 1.1rem;
        }

        .navbar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .navbar-link {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .navbar-link:hover {
            color: var(--primary-700);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-800) 0%, var(--primary-700) 100%);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(26, 54, 93, 0.25);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(26, 54, 93, 0.35);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--gray-300);
            color: var(--gray-700);
        }

        .btn-outline:hover {
            border-color: var(--primary-700);
            color: var(--primary-700);
        }

        .btn-lg {
            padding: 0.875rem 1.75rem;
            font-size: 1rem;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           HERO SECTION
           ═══════════════════════════════════════════════════════════════════════════ */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(180deg, var(--primary-50) 0%, var(--white) 100%);
            padding: 8rem 2rem 4rem;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content {
            max-width: 540px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            background: var(--accent-500);
            color: var(--white);
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 9999px;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: 3.25rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1.15;
            margin-bottom: 1.5rem;
        }

        .hero-title span {
            color: var(--primary-700);
        }

        .hero-description {
            font-size: 1.125rem;
            color: var(--gray-600);
            margin-bottom: 2rem;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .hero-stats {
            display: flex;
            gap: 2.5rem;
        }

        .hero-stat {
            text-align: left;
        }

        .hero-stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-800);
        }

        .hero-stat-label {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .hero-visual {
            position: relative;
        }

        .hero-card {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .hero-card-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-400) 0%, var(--accent-600) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 700;
        }

        .hero-card-info h4 {
            font-size: 1rem;
            color: var(--gray-900);
            margin-bottom: 0.125rem;
        }

        .hero-card-info p {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .hero-card-progress {
            margin-bottom: 1rem;
        }

        .hero-card-progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .hero-card-progress-bar {
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
        }

        .hero-card-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-500) 0%, var(--success-500) 100%);
            border-radius: 4px;
            width: 75%;
        }

        .hero-card-steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .hero-card-step {
            text-align: center;
            padding: 0.75rem 0.5rem;
            background: var(--gray-50);
            border-radius: 0.75rem;
        }

        .hero-card-step-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .hero-card-step-icon.done {
            background: var(--success-500);
            color: var(--white);
        }

        .hero-card-step-icon.active {
            background: var(--accent-500);
            color: var(--white);
        }

        .hero-card-step-icon.pending {
            background: var(--gray-200);
            color: var(--gray-500);
        }

        .hero-card-step-label {
            font-size: 0.7rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .hero-decoration {
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--accent-400) 0%, var(--primary-600) 100%);
            border-radius: 50%;
            opacity: 0.1;
            top: -50px;
            right: -50px;
            z-index: 1;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           FEATURES SECTION
           ═══════════════════════════════════════════════════════════════════════════ */
        .features {
            padding: 6rem 2rem;
            background: var(--white);
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-subtitle {
            display: inline-block;
            padding: 0.375rem 1rem;
            background: var(--primary-100);
            color: var(--primary-700);
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 9999px;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .section-description {
            font-size: 1.125rem;
            color: var(--gray-600);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: var(--gray-50);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid var(--gray-200);
        }

        .feature-card:hover {
            background: var(--white);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon svg {
            width: 28px;
            height: 28px;
        }

        .feature-icon.blue {
            background: var(--primary-100);
            color: var(--primary-700);
        }

        .feature-icon.teal {
            background: #e6fffa;
            color: var(--accent-600);
        }

        .feature-icon.amber {
            background: #fef3c7;
            color: #b45309;
        }

        .feature-icon.green {
            background: #dcfce7;
            color: #15803d;
        }

        .feature-icon.purple {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .feature-icon.pink {
            background: #fce7f3;
            color: #be185d;
        }

        .feature-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }

        .feature-description {
            font-size: 0.95rem;
            color: var(--gray-600);
            line-height: 1.6;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           WORKFLOW SECTION
           ═══════════════════════════════════════════════════════════════════════════ */
        .workflow {
            padding: 6rem 2rem;
            background: linear-gradient(180deg, var(--primary-900) 0%, var(--primary-800) 100%);
            color: var(--white);
        }

        .workflow .section-subtitle {
            background: rgba(255, 255, 255, 0.15);
            color: var(--accent-400);
        }

        .workflow .section-title {
            color: var(--white);
        }

        .workflow .section-description {
            color: rgba(255, 255, 255, 0.7);
        }

        .workflow-steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .workflow-step {
            text-align: center;
            position: relative;
        }

        .workflow-step-number {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent-400);
        }

        .workflow-step-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .workflow-step-desc {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .workflow-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 32px;
            right: -0.75rem;
            width: calc(100% - 64px);
            height: 2px;
            background: linear-gradient(90deg, var(--accent-500) 0%, transparent 100%);
            transform: translateX(50%);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           CTA SECTION
           ═══════════════════════════════════════════════════════════════════════════ */
        .cta {
            padding: 6rem 2rem;
            background: var(--gray-50);
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .cta-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.125rem;
            color: var(--gray-600);
            margin-bottom: 2rem;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           FOOTER
           ═══════════════════════════════════════════════════════════════════════════ */
        .footer {
            padding: 3rem 2rem;
            background: var(--gray-900);
            color: var(--gray-400);
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-logo {
            width: 32px;
            height: 32px;
            background: var(--primary-700);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .footer-text {
            font-size: 0.875rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-link {
            color: var(--gray-400);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .footer-link:hover {
            color: var(--white);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-content {
                max-width: 100%;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero-visual {
                display: none;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .workflow-steps {
                grid-template-columns: repeat(2, 1fr);
            }

            .workflow-step:not(:last-child)::after {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
            }

            .hero-title {
                font-size: 2.25rem;
            }

            .hero-stats {
                flex-wrap: wrap;
                gap: 1.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .workflow-steps {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2rem;
            }

            .footer-container {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="/" class="navbar-brand">
            <div class="navbar-logo">CM</div>
            <span class="navbar-title">CheckMaster</span>
        </a>
        <div class="navbar-nav">
            <a href="#features" class="navbar-link">Fonctionnalités</a>
            <a href="#workflow" class="navbar-link">Processus</a>
            <a href="/connexion" class="btn btn-primary">Se connecter</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    UFR Mathématiques et Informatique
                </div>
                <h1 class="hero-title">
                    Gérez vos <span>mémoires</span> de fin d'études simplement
                </h1>
                <p class="hero-description">
                    CheckMaster digitalise l'ensemble du processus de validation des mémoires, 
                    de la candidature à la délivrance du diplôme, pour l'Université Félix Houphouët-Boigny.
                </p>
                <div class="hero-actions">
                    <a href="/connexion" class="btn btn-primary btn-lg">
                        Accéder à mon espace
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="#workflow" class="btn btn-outline btn-lg">
                        Découvrir le processus
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?= $stats['etudiants'] ?>+</div>
                        <div class="hero-stat-label">Étudiants M2</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?= $stats['soutenances'] ?>+</div>
                        <div class="hero-stat-label">Soutenances</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?= $stats['taux_reussite'] ?>%</div>
                        <div class="hero-stat-label">Taux de réussite</div>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-header">
                        <div class="hero-card-avatar">KA</div>
                        <div class="hero-card-info">
                            <h4>KONE Adama</h4>
                            <p>Master 2 MIAGE - 2024-2025</p>
                        </div>
                    </div>
                    <div class="hero-card-progress">
                        <div class="hero-card-progress-label">
                            <span>Progression du dossier</span>
                            <span style="color: var(--accent-600); font-weight: 600;">75%</span>
                        </div>
                        <div class="hero-card-progress-bar">
                            <div class="hero-card-progress-fill"></div>
                        </div>
                    </div>
                    <div class="hero-card-steps">
                        <div class="hero-card-step">
                            <div class="hero-card-step-icon done">✓</div>
                            <div class="hero-card-step-label">Candidature</div>
                        </div>
                        <div class="hero-card-step">
                            <div class="hero-card-step-icon done">✓</div>
                            <div class="hero-card-step-label">Commission</div>
                        </div>
                        <div class="hero-card-step">
                            <div class="hero-card-step-icon active">●</div>
                            <div class="hero-card-step-label">Soutenance</div>
                        </div>
                    </div>
                </div>
                <div class="hero-decoration"></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-container">
            <div class="section-header">
                <span class="section-subtitle">Fonctionnalités</span>
                <h2 class="section-title">Tout ce dont vous avez besoin</h2>
                <p class="section-description">
                    CheckMaster offre une suite complète d'outils pour gérer efficacement 
                    le parcours des mémoires de fin d'études.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-title">Gestion des candidatures</h3>
                    <p class="feature-description">
                        Soumettez et suivez vos candidatures en temps réel. 
                        Validation automatique par la scolarité et le service communication.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Commission de validation</h3>
                    <p class="feature-description">
                        Système de vote à l'unanimité avec médiation automatique. 
                        Annotations collaboratives sur les rapports.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">Planification des soutenances</h3>
                    <p class="feature-description">
                        Constitution des jurys, planification intelligente et 
                        génération automatique des convocations.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">Gestion financière</h3>
                    <p class="feature-description">
                        Suivi des paiements, calcul automatique des pénalités et 
                        gestion des exonérations avec génération de reçus.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 01-3.46 0"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Notifications temps réel</h3>
                    <p class="feature-description">
                        Alertes automatiques par email à chaque étape. 
                        Rappels SLA et escalade vers le Doyen si nécessaire.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon pink">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Sécurité et traçabilité</h3>
                    <p class="feature-description">
                        Authentification sécurisée, audit complet et archivage 
                        des documents avec vérification d'intégrité.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="workflow" id="workflow">
        <div class="section-container">
            <div class="section-header">
                <span class="section-subtitle">Processus</span>
                <h2 class="section-title">14 états, un parcours fluide</h2>
                <p class="section-description">
                    De l'inscription à la délivrance du diplôme, chaque étape est tracée et notifiée.
                </p>
            </div>
            <div class="workflow-steps">
                <div class="workflow-step">
                    <div class="workflow-step-number">1</div>
                    <h3 class="workflow-step-title">Candidature</h3>
                    <p class="workflow-step-desc">Soumission du thème, validation scolarité et format</p>
                </div>
                <div class="workflow-step">
                    <div class="workflow-step-number">2</div>
                    <h3 class="workflow-step-title">Commission</h3>
                    <p class="workflow-step-desc">Évaluation du rapport, vote unanime requis</p>
                </div>
                <div class="workflow-step">
                    <div class="workflow-step-number">3</div>
                    <h3 class="workflow-step-title">Soutenance</h3>
                    <p class="workflow-step-desc">Constitution jury, planification, saisie notes</p>
                </div>
                <div class="workflow-step">
                    <div class="workflow-step-number">4</div>
                    <h3 class="workflow-step-title">Diplôme</h3>
                    <p class="workflow-step-desc">Génération PV, délivrance et archivage</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-container">
            <h2 class="cta-title">Prêt à commencer ?</h2>
            <p class="cta-description">
                Connectez-vous à votre espace pour suivre votre parcours ou gérer les dossiers de votre département.
            </p>
            <div class="cta-actions">
                <a href="/connexion" class="btn btn-primary btn-lg">
                    Se connecter
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-logo">CM</div>
                <span class="footer-text">© 2024 CheckMaster - UFHB</span>
            </div>
            <div class="footer-links">
                <a href="#" class="footer-link">Aide</a>
                <a href="#" class="footer-link">Contact</a>
                <a href="#" class="footer-link">Mentions légales</a>
            </div>
        </div>
    </footer>
</body>

</html>
