<?php
/**
 * Vue du tableau de bord financier étudiant
 * 
 * @see PRD 07 - Financier
 */
$pageTitle = 'Mes Finances';
$breadcrumb = [
    ['label' => 'Espace Étudiant', 'url' => '/etudiant'],
    ['label' => 'Finances', 'active' => true],
];
?>

<div x-data="financesEtudiantModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Mes Finances</h1>
        <p class="mt-1 text-sm text-gray-600">Suivi de vos paiements et frais de scolarité</p>
    </div>

    <!-- Résumé financier -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Montant dû -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Frais de scolarité</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="formatMontant(resume.solde?.montant_du)"></p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total payé -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total payé</p>
                    <p class="text-2xl font-semibold text-green-600" x-text="formatMontant(resume.solde?.total_paye)"></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Exonérations -->
        <div class="bg-white rounded-lg shadow p-6" x-show="resume.solde?.exonerations > 0">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Exonérations</p>
                    <p class="text-2xl font-semibold text-blue-600" x-text="'-' + formatMontant(resume.solde?.exonerations)"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Solde restant -->
        <div :class="resume.solde?.est_complet ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'" class="rounded-lg shadow p-6 border-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" :class="resume.solde?.est_complet ? 'text-green-700' : 'text-red-700'">Solde restant</p>
                    <p class="text-2xl font-bold" :class="resume.solde?.est_complet ? 'text-green-600' : 'text-red-600'" x-text="formatMontant(resume.solde?.solde_restant)"></p>
                </div>
                <div :class="resume.solde?.est_complet ? 'bg-green-100' : 'bg-red-100'" class="p-3 rounded-full">
                    <svg x-show="resume.solde?.est_complet" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="!resume.solde?.est_complet" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p x-show="resume.solde?.est_complet" class="mt-2 text-sm text-green-600">✓ Paiement complet</p>
        </div>
    </div>

    <!-- Alerte pénalités -->
    <div x-show="resume.solde?.penalites > 0" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Pénalités de retard</h3>
                <p class="mt-1 text-sm text-red-700">
                    Vous avez des pénalités de retard d'un montant de <strong x-text="formatMontant(resume.solde?.penalites)"></strong>.
                    Veuillez régulariser votre situation au plus vite.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Historique des paiements -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Historique des paiements</h2>
            </div>
            <div class="divide-y divide-gray-200">
                <template x-for="paiement in resume.paiements" :key="paiement.id_paiement">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900" x-text="paiement.motif || 'Frais de scolarité'"></p>
                            <p class="text-xs text-gray-500" x-text="formatDate(paiement.date_paiement) + ' - ' + paiement.mode_paiement"></p>
                            <p class="text-xs text-gray-400 font-mono" x-text="paiement.reference"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold" :class="paiement.statut === 'Valide' ? 'text-green-600' : 'text-red-600'" x-text="formatMontant(paiement.montant_paye || paiement.montant)"></p>
                            <button @click="telechargerRecu(paiement.id_paiement)" class="text-blue-600 text-xs hover:underline">
                                Télécharger le reçu
                            </button>
                        </div>
                    </div>
                </template>
                <div x-show="resume.paiements?.length === 0" class="px-6 py-8 text-center text-gray-500">
                    Aucun paiement enregistré
                </div>
            </div>
        </div>

        <!-- Pénalités et Exonérations -->
        <div class="space-y-6">
            <!-- Pénalités -->
            <div class="bg-white rounded-lg shadow" x-show="resume.penalites?.length > 0">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-red-700">Pénalités</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <template x-for="penalite in resume.penalites" :key="penalite.id_penalite">
                        <div class="px-6 py-4 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="penalite.motif"></p>
                                <p class="text-xs text-gray-500" x-text="formatDate(penalite.date_application)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-red-600" x-text="formatMontant(penalite.montant)"></p>
                                <span x-show="penalite.payee" class="text-xs text-green-600">Payée</span>
                                <span x-show="!penalite.payee" class="text-xs text-red-600">À payer</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Exonérations -->
            <div class="bg-white rounded-lg shadow" x-show="resume.exonerations?.length > 0">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-blue-700">Exonérations accordées</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <template x-for="exoneration in resume.exonerations" :key="exoneration.id_exoneration">
                        <div class="px-6 py-4 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="exoneration.motif"></p>
                                <p class="text-xs text-gray-500" x-text="formatDate(exoneration.demandee_le || exoneration.date_attribution)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-blue-600" x-text="'-' + formatMontant(exoneration.montant || exoneration.montant_exonere)"></p>
                                <span :class="{'text-green-600': exoneration.statut === 'Approuvee', 'text-yellow-600': exoneration.statut === 'En_attente', 'text-red-600': exoneration.statut === 'Refusee'}" class="text-xs" x-text="exoneration.statut"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Informations -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="font-medium text-blue-800 mb-2">Informations</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Les paiements peuvent être effectués à la caisse de l'établissement</li>
                    <li>• Modes de paiement acceptés : Espèces, Carte bancaire, Mobile Money, Virement</li>
                    <li>• Conservez vos reçus de paiement pour toute réclamation</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function financesEtudiantModule() {
    return {
        resume: {
            solde: null,
            paiements: [],
            penalites: [],
            exonerations: [],
            etudiant: null
        },

        async init() {
            await this.chargerResume();
        },

        async chargerResume() {
            const response = await fetch('/api/etudiant/finances/resume');
            const data = await response.json();
            if (data.success) {
                this.resume = data.data;
            }
        },

        telechargerRecu(id) {
            window.open('/api/finance/paiements/' + id + '/recu', '_blank');
        },

        formatMontant(montant) {
            if (!montant) return '0 FCFA';
            return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR');
        }
    }
}
</script>
