<?php
/**
 * Vue des pénalités
 * 
 * @see PRD 07 - Financier
 */
$pageTitle = 'Gestion des Pénalités';
$breadcrumb = [
    ['label' => 'Finance', 'url' => '/finance'],
    ['label' => 'Pénalités', 'active' => true],
];
?>

<div x-data="penalitesModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Pénalités</h1>
            <p class="mt-1 text-sm text-gray-600">Suivi des pénalités de retard et sanctions financières</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <button @click="calculerPenalitesAuto()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Calculer automatiquement
            </button>
            <button @click="openModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouvelle Pénalité
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Total pénalités</p>
            <p class="text-2xl font-semibold text-red-600" x-text="formatMontant(statistiques.total_penalites)"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Impayées</p>
            <p class="text-2xl font-semibold text-orange-600" x-text="formatMontant(statistiques.total_impayees)"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Payées</p>
            <p class="text-2xl font-semibold text-green-600" x-text="formatMontant(statistiques.total_payees)"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Étudiants concernés</p>
            <p class="text-2xl font-semibold text-gray-900" x-text="statistiques.nombre_etudiants || 0"></p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" x-model="filtres.recherche" @input.debounce.300ms="chargerPenalites()" 
                       class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Nom, matricule...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select x-model="filtres.payee" @change="chargerPenalites()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Tous les statuts</option>
                    <option value="0">Non payées</option>
                    <option value="1">Payées</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select x-model="filtres.type" @change="chargerPenalites()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Tous les types</option>
                    <option value="Retard">Retard</option>
                    <option value="Absence">Absence</option>
                    <option value="Document_manquant">Document manquant</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depuis le</label>
                <input type="date" x-model="filtres.date_debut" @change="chargerPenalites()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
    </div>

    <!-- Liste des pénalités -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="penalite in penalites" :key="penalite.id_penalite">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900" x-text="penalite.nom_etu + ' ' + penalite.prenom_etu"></div>
                            <div class="text-sm text-gray-500" x-text="penalite.numero_carte"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900" x-text="penalite.motif"></div>
                            <div x-show="penalite.type" class="text-xs text-gray-500" x-text="penalite.type"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-red-600" x-text="formatMontant(penalite.montant)"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(penalite.date_application)"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span x-show="penalite.payee" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Payée</span>
                            <span x-show="!penalite.payee && !penalite.annulee" class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Impayée</span>
                            <span x-show="penalite.annulee" class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Annulée</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button x-show="!penalite.payee && !penalite.annulee" @click="payerPenalite(penalite.id_penalite)" class="text-green-600 hover:text-green-900 mr-3" title="Marquer comme payée">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                            <button x-show="!penalite.payee && !penalite.annulee" @click="annulerPenalite(penalite.id_penalite)" class="text-red-600 hover:text-red-900" title="Annuler">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <div x-show="penalites.length === 0" class="text-center py-12">
            <p class="text-gray-500">Aucune pénalité trouvée</p>
        </div>
    </div>

    <!-- Modal Nouvelle Pénalité -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Créer une Pénalité</h3>
                <form @submit.prevent="creerPenalite()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Étudiant *</label>
                            <input type="text" x-model="form.etudiant_recherche" @input.debounce.300ms="rechercherEtudiant()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher...">
                            <div x-show="etudiantsResultats.length > 0" class="mt-1 bg-white border rounded-md shadow-sm max-h-40 overflow-y-auto">
                                <template x-for="etudiant in etudiantsResultats" :key="etudiant.id_etudiant">
                                    <div @click="selectionnerEtudiant(etudiant)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span x-text="etudiant.nom_etu + ' ' + etudiant.prenom_etu"></span>
                                        <span class="text-gray-500 text-sm" x-text="' - ' + etudiant.numero_carte"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select x-model="form.type" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="Retard">Retard</option>
                                <option value="Absence">Absence</option>
                                <option value="Document_manquant">Document manquant</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Montant (FCFA) *</label>
                            <input type="number" x-model="form.montant" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Motif *</label>
                            <textarea x-model="form.motif" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function penalitesModule() {
    return {
        penalites: [],
        statistiques: {},
        showModal: false,
        filtres: {
            recherche: '',
            payee: '',
            type: '',
            date_debut: ''
        },
        form: {
            etudiant_id: null,
            etudiant_recherche: '',
            type: 'Retard',
            montant: '',
            motif: ''
        },
        etudiantsResultats: [],

        async init() {
            await this.chargerPenalites();
            await this.chargerStatistiques();
        },

        async chargerPenalites() {
            const response = await fetch('/api/finance/penalites?' + new URLSearchParams(this.filtres));
            const data = await response.json();
            if (data.success) {
                this.penalites = data.data;
            }
        },

        async chargerStatistiques() {
            const response = await fetch('/api/finance/penalites/statistiques');
            const data = await response.json();
            if (data.success) {
                this.statistiques = data.data;
            }
        },

        async rechercherEtudiant() {
            if (this.form.etudiant_recherche.length < 2) {
                this.etudiantsResultats = [];
                return;
            }
            const response = await fetch('/api/scolarite/etudiants?q=' + encodeURIComponent(this.form.etudiant_recherche));
            const data = await response.json();
            if (data.success) {
                this.etudiantsResultats = data.data.slice(0, 10);
            }
        },

        selectionnerEtudiant(etudiant) {
            this.form.etudiant_id = etudiant.id_etudiant;
            this.form.etudiant_recherche = etudiant.nom_etu + ' ' + etudiant.prenom_etu;
            this.etudiantsResultats = [];
        },

        async creerPenalite() {
            const formData = new FormData();
            formData.append('etudiant_id', this.form.etudiant_id);
            formData.append('montant', this.form.montant);
            formData.append('motif', this.form.motif);
            formData.append('type', this.form.type);

            const response = await fetch('/api/finance/penalites', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closeModal();
                await this.chargerPenalites();
                await this.chargerStatistiques();
                alert('Pénalité créée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async payerPenalite(id) {
            if (!confirm('Confirmer le paiement de cette pénalité ?')) return;

            const formData = new FormData();
            formData.append('mode_paiement', 'Especes');

            const response = await fetch('/api/finance/penalites/' + id + '/payer', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerPenalites();
                await this.chargerStatistiques();
                alert('Pénalité marquée comme payée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async annulerPenalite(id) {
            const motif = prompt('Motif de l\'annulation:');
            if (!motif) return;

            const formData = new FormData();
            formData.append('motif', motif);

            const response = await fetch('/api/finance/penalites/' + id + '/annuler', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerPenalites();
                alert('Pénalité annulée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async calculerPenalitesAuto() {
            if (!confirm('Calculer automatiquement les pénalités de retard ?')) return;

            const response = await fetch('/api/finance/penalites/calculer-auto', {
                method: 'POST'
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerPenalites();
                await this.chargerStatistiques();
                alert('Calcul terminé: ' + data.data.nombre_penalites + ' pénalité(s) créée(s)');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        openModal() {
            this.showModal = true;
            this.form = {
                etudiant_id: null,
                etudiant_recherche: '',
                type: 'Retard',
                montant: '',
                motif: ''
            };
        },

        closeModal() {
            this.showModal = false;
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
