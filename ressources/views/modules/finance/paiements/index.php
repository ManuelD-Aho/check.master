<?php
/**
 * Vue des paiements
 * 
 * @see PRD 07 - Financier
 */
$pageTitle = 'Gestion des Paiements';
$breadcrumb = [
    ['label' => 'Finance', 'url' => '/finance'],
    ['label' => 'Paiements', 'active' => true],
];
?>

<div x-data="paiementsModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Paiements</h1>
            <p class="mt-1 text-sm text-gray-600">Enregistrement et suivi des paiements de scolarité</p>
        </div>
        <button @click="openModal()" class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouveau Paiement
        </button>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Encaissé</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="formatMontant(statistiques.total_encaisse)"></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Étudiants</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="statistiques.nombre_etudiants || 0"></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Paiements</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="statistiques.nombre_paiements || 0"></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Moyenne</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="formatMontant(statistiques.moyenne_paiement)"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" x-model="filtres.recherche" @input.debounce.300ms="chargerPaiements()" 
                       class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Nom, matricule, référence...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                <select x-model="filtres.mode" @change="chargerPaiements()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Tous les modes</option>
                    <option value="Especes">Espèces</option>
                    <option value="Carte">Carte bancaire</option>
                    <option value="Virement">Virement</option>
                    <option value="Cheque">Chèque</option>
                    <option value="Mobile">Mobile Money</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date du</label>
                <input type="date" x-model="filtres.date_debut" @change="chargerPaiements()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" x-model="filtres.date_fin" @change="chargerPaiements()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
    </div>

    <!-- Liste des paiements -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="paiement in paiements" :key="paiement.id_paiement">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm" x-text="paiement.reference"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900" x-text="paiement.nom_etu + ' ' + paiement.prenom_etu"></div>
                            <div class="text-sm text-gray-500" x-text="paiement.numero_carte"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-green-600" x-text="formatMontant(paiement.montant_paye || paiement.montant)"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="paiement.mode_paiement"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(paiement.date_paiement)"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span x-show="paiement.statut === 'Valide'" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Validé</span>
                            <span x-show="paiement.statut === 'Annule'" class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Annulé</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="telechargerRecu(paiement.id_paiement)" class="text-blue-600 hover:text-blue-900 mr-3" title="Télécharger le reçu">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                            <button x-show="paiement.statut === 'Valide'" @click="annulerPaiement(paiement.id_paiement)" class="text-red-600 hover:text-red-900" title="Annuler">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <div x-show="paiements.length === 0" class="text-center py-12">
            <p class="text-gray-500">Aucun paiement trouvé</p>
        </div>
    </div>

    <!-- Modal Nouveau Paiement -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Enregistrer un Paiement</h3>
                <form @submit.prevent="enregistrerPaiement()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Étudiant *</label>
                            <input type="text" x-model="form.etudiant_recherche" @input.debounce.300ms="rechercherEtudiant()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher par nom ou matricule...">
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
                            <label class="block text-sm font-medium text-gray-700">Montant (FCFA) *</label>
                            <input type="number" x-model="form.montant" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mode de paiement *</label>
                            <select x-model="form.mode_paiement" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Especes">Espèces</option>
                                <option value="Carte">Carte bancaire</option>
                                <option value="Virement">Virement</option>
                                <option value="Cheque">Chèque</option>
                                <option value="Mobile">Mobile Money</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Motif</label>
                            <input type="text" x-model="form.motif" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" value="Frais de scolarité">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function paiementsModule() {
    return {
        paiements: [],
        statistiques: {},
        showModal: false,
        filtres: {
            recherche: '',
            mode: '',
            date_debut: '',
            date_fin: ''
        },
        form: {
            etudiant_id: null,
            etudiant_recherche: '',
            montant: '',
            mode_paiement: 'Especes',
            motif: 'Frais de scolarité'
        },
        etudiantsResultats: [],

        async init() {
            await this.chargerPaiements();
            await this.chargerStatistiques();
        },

        async chargerPaiements() {
            const response = await fetch('/api/finance/paiements?' + new URLSearchParams(this.filtres));
            const data = await response.json();
            if (data.success) {
                this.paiements = data.data;
            }
        },

        async chargerStatistiques() {
            const response = await fetch('/api/finance/paiements/statistiques');
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

        async enregistrerPaiement() {
            const formData = new FormData();
            formData.append('etudiant_id', this.form.etudiant_id);
            formData.append('montant', this.form.montant);
            formData.append('mode_paiement', this.form.mode_paiement);
            formData.append('motif', this.form.motif);
            formData.append('annee_acad_id', '<?= $anneeAcadId ?? 1 ?>');

            const response = await fetch('/api/finance/paiements', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closeModal();
                await this.chargerPaiements();
                await this.chargerStatistiques();
                alert('Paiement enregistré avec succès. Référence: ' + data.data.reference);
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async annulerPaiement(id) {
            const motif = prompt('Motif de l\'annulation:');
            if (!motif) return;

            const formData = new FormData();
            formData.append('motif', motif);

            const response = await fetch('/api/finance/paiements/' + id + '/annuler', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerPaiements();
                alert('Paiement annulé');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        telechargerRecu(id) {
            window.open('/api/finance/paiements/' + id + '/recu', '_blank');
        },

        openModal() {
            this.showModal = true;
            this.form = {
                etudiant_id: null,
                etudiant_recherche: '',
                montant: '',
                mode_paiement: 'Especes',
                motif: 'Frais de scolarité'
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
