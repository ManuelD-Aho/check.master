<?php
/**
 * Vue des exonérations
 * 
 * @see PRD 07 - Financier
 */
$pageTitle = 'Gestion des Exonérations';
$breadcrumb = [
    ['label' => 'Finance', 'url' => '/finance'],
    ['label' => 'Exonérations', 'active' => true],
];
?>

<div x-data="exonerationsModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Exonérations</h1>
            <p class="mt-1 text-sm text-gray-600">Demandes et attributions d'exonérations de frais</p>
        </div>
        <button @click="openModal()" class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle Demande
        </button>
    </div>

    <!-- Onglets -->
    <div class="mb-6">
        <nav class="flex space-x-4">
            <button @click="onglet = 'attente'" :class="onglet === 'attente' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-medium">
                En attente
                <span x-show="statistiques.en_attente > 0" class="ml-2 bg-yellow-500 text-white px-2 py-0.5 rounded-full text-xs" x-text="statistiques.en_attente"></span>
            </button>
            <button @click="onglet = 'toutes'" :class="onglet === 'toutes' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-medium">
                Toutes
            </button>
        </nav>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Total exonéré</p>
            <p class="text-2xl font-semibold text-blue-600" x-text="formatMontant(statistiques.total_exonere)"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">En attente</p>
            <p class="text-2xl font-semibold text-yellow-600" x-text="statistiques.en_attente || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Approuvées</p>
            <p class="text-2xl font-semibold text-green-600" x-text="statistiques.approuvees || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Refusées</p>
            <p class="text-2xl font-semibold text-red-600" x-text="statistiques.refusees || 0"></p>
        </div>
    </div>

    <!-- Filtres -->
    <div x-show="onglet === 'toutes'" class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" x-model="filtres.recherche" @input.debounce.300ms="chargerExonerations()" 
                       class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Nom, matricule...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select x-model="filtres.statut" @change="chargerExonerations()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Tous les statuts</option>
                    <option value="En_attente">En attente</option>
                    <option value="Approuvee">Approuvées</option>
                    <option value="Refusee">Refusées</option>
                    <option value="Annulee">Annulées</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select x-model="filtres.type" @change="chargerExonerations()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Tous les types</option>
                    <option value="Boursier">Boursier</option>
                    <option value="Merite">Mérite</option>
                    <option value="Social">Social</option>
                    <option value="Handicap">Handicap</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                <select x-model="filtres.annee_id" @change="chargerExonerations()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Toutes les années</option>
                    <!-- Options générées dynamiquement -->
                </select>
            </div>
        </div>
    </div>

    <!-- Liste des exonérations -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date demande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="exo in exonerations" :key="exo.id_exoneration">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900" x-text="exo.nom_etu + ' ' + exo.prenom_etu"></div>
                            <div class="text-sm text-gray-500" x-text="exo.numero_carte"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm" x-text="exo.type || exo.motif?.split(' ')[0] || '-'"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span x-show="exo.montant || exo.montant_exonere" class="text-sm font-semibold text-blue-600" x-text="formatMontant(exo.montant || exo.montant_exonere)"></span>
                            <span x-show="exo.pourcentage" class="text-sm font-semibold text-blue-600" x-text="exo.pourcentage + '%'"></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 line-clamp-2" x-text="exo.motif"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(exo.demandee_le || exo.date_attribution)"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span x-show="exo.statut === 'En_attente'" class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                            <span x-show="exo.statut === 'Approuvee'" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approuvée</span>
                            <span x-show="exo.statut === 'Refusee'" class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Refusée</span>
                            <span x-show="exo.statut === 'Annulee'" class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Annulée</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <template x-if="exo.statut === 'En_attente'">
                                <div class="flex justify-end space-x-2">
                                    <button @click="approuver(exo.id_exoneration)" class="text-green-600 hover:text-green-900" title="Approuver">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <button @click="refuser(exo.id_exoneration)" class="text-red-600 hover:text-red-900" title="Refuser">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="exo.statut === 'Approuvee'">
                                <button @click="annuler(exo.id_exoneration)" class="text-gray-600 hover:text-gray-900" title="Annuler">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <div x-show="exonerations.length === 0" class="text-center py-12">
            <p class="text-gray-500">Aucune exonération trouvée</p>
        </div>
    </div>

    <!-- Modal Nouvelle Demande -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Demande d'Exonération</h3>
                <form @submit.prevent="creerDemande()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Étudiant *</label>
                            <input type="text" x-model="form.etudiant_recherche" @input.debounce.300ms="rechercherEtudiant()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher...">
                            <div x-show="etudiantsResultats.length > 0" class="mt-1 bg-white border rounded-md shadow-sm max-h-40 overflow-y-auto">
                                <template x-for="etudiant in etudiantsResultats" :key="etudiant.id_etudiant">
                                    <div @click="selectionnerEtudiant(etudiant)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span x-text="etudiant.nom_etu + ' ' + etudiant.prenom_etu"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type *</label>
                            <select x-model="form.type" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Boursier">Boursier</option>
                                <option value="Merite">Mérite</option>
                                <option value="Social">Social</option>
                                <option value="Handicap">Handicap</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Montant (FCFA)</label>
                                <input type="number" x-model="form.montant" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ou Pourcentage (%)</label>
                                <input type="number" x-model="form.pourcentage" min="0" max="100" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Motif *</label>
                            <textarea x-model="form.motif" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Justificatif</label>
                            <input type="file" @change="form.justificatif = $event.target.files[0]" class="mt-1 w-full">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function exonerationsModule() {
    return {
        exonerations: [],
        statistiques: {},
        showModal: false,
        onglet: 'attente',
        filtres: {
            recherche: '',
            statut: '',
            type: '',
            annee_id: ''
        },
        form: {
            etudiant_id: null,
            etudiant_recherche: '',
            type: 'Boursier',
            montant: '',
            pourcentage: '',
            motif: '',
            justificatif: null
        },
        etudiantsResultats: [],

        async init() {
            await this.chargerExonerations();
            await this.chargerStatistiques();
        },

        async chargerExonerations() {
            let url = '/api/finance/exonerations';
            if (this.onglet === 'attente') {
                url = '/api/finance/exonerations/en-attente';
            } else {
                url += '?' + new URLSearchParams(this.filtres);
            }
            const response = await fetch(url);
            const data = await response.json();
            if (data.success) {
                this.exonerations = data.data;
            }
        },

        async chargerStatistiques() {
            const response = await fetch('/api/finance/exonerations/statistiques');
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

        async creerDemande() {
            const formData = new FormData();
            formData.append('etudiant_id', this.form.etudiant_id);
            formData.append('type', this.form.type);
            formData.append('motif', this.form.motif);
            if (this.form.montant) formData.append('montant', this.form.montant);
            if (this.form.pourcentage) formData.append('pourcentage', this.form.pourcentage);
            if (this.form.justificatif) formData.append('justificatif', this.form.justificatif);

            const response = await fetch('/api/finance/exonerations', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closeModal();
                await this.chargerExonerations();
                await this.chargerStatistiques();
                alert('Demande créée avec succès');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async approuver(id) {
            const commentaire = prompt('Commentaire (optionnel):');
            const formData = new FormData();
            if (commentaire) formData.append('commentaire', commentaire);

            const response = await fetch('/api/finance/exonerations/' + id + '/approuver', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerExonerations();
                await this.chargerStatistiques();
                alert('Exonération approuvée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async refuser(id) {
            const motif = prompt('Motif du refus:');
            if (!motif) return;

            const formData = new FormData();
            formData.append('motif', motif);

            const response = await fetch('/api/finance/exonerations/' + id + '/refuser', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerExonerations();
                await this.chargerStatistiques();
                alert('Exonération refusée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async annuler(id) {
            const motif = prompt('Motif de l\'annulation:');
            if (!motif) return;

            const formData = new FormData();
            formData.append('motif', motif);

            const response = await fetch('/api/finance/exonerations/' + id + '/annuler', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerExonerations();
                await this.chargerStatistiques();
                alert('Exonération annulée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        openModal() {
            this.showModal = true;
            this.form = {
                etudiant_id: null,
                etudiant_recherche: '',
                type: 'Boursier',
                montant: '',
                pourcentage: '',
                motif: '',
                justificatif: null
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
