<?php
/**
 * Vue de gestion des jurys
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
$pageTitle = 'Gestion des Jurys';
$breadcrumb = [
    ['label' => 'Soutenances', 'url' => '/soutenance'],
    ['label' => 'Jurys', 'active' => true],
];
?>

<div x-data="juryModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Jurys</h1>
            <p class="mt-1 text-sm text-gray-600">Constitution et suivi des jurys de soutenance</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Total invitations</p>
            <p class="text-2xl font-semibold text-gray-900" x-text="statistiques.total_invitations || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Acceptées</p>
            <p class="text-2xl font-semibold text-green-600" x-text="statistiques.acceptees || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">En attente</p>
            <p class="text-2xl font-semibold text-yellow-600" x-text="statistiques.en_attente || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Jurys complets</p>
            <p class="text-2xl font-semibold text-blue-600" x-text="statistiques.jurys_complets || 0"></p>
        </div>
    </div>

    <!-- Onglets -->
    <div class="mb-6">
        <nav class="flex space-x-4">
            <button @click="onglet = 'dossiers'" :class="onglet === 'dossiers' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-medium">
                Par Dossier
            </button>
            <button @click="onglet = 'invitations'" :class="onglet === 'invitations' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-medium">
                Mes Invitations
            </button>
        </nav>
    </div>

    <!-- Liste par dossier -->
    <div x-show="onglet === 'dossiers'" class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Filtres -->
        <div class="p-4 border-b border-gray-200">
            <input type="text" x-model="recherche" @input.debounce.300ms="chargerDossiers()" 
                   class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher un dossier...">
        </div>
        
        <div class="divide-y divide-gray-200">
            <template x-for="dossier in dossiers" :key="dossier.id_dossier">
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium text-gray-900" x-text="dossier.nom_etu + ' ' + dossier.prenom_etu"></h3>
                            <p class="text-sm text-gray-500" x-text="dossier.theme || 'Thème non défini'"></p>
                        </div>
                        <div class="text-right">
                            <span :class="dossier.jury_complet ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" 
                                  class="px-2 py-1 text-xs font-semibold rounded-full" 
                                  x-text="dossier.jury_complet ? 'Jury complet' : (dossier.nombre_acceptes || 0) + '/5 acceptés'">
                            </span>
                        </div>
                    </div>
                    
                    <!-- Membres du jury -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Composition du jury</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            <template x-for="membre in dossier.jury" :key="membre.id_jury_membre">
                                <div :class="{
                                    'bg-green-50 border-green-200': membre.statut === 'Accepte',
                                    'bg-yellow-50 border-yellow-200': membre.statut === 'Invite',
                                    'bg-red-50 border-red-200': membre.statut === 'Refuse'
                                }" class="p-2 border rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium" x-text="membre.nom_ens + ' ' + membre.prenom_ens"></p>
                                            <p class="text-xs text-gray-500" x-text="membre.role"></p>
                                        </div>
                                        <button x-show="membre.statut !== 'Accepte'" @click="retirerMembre(membre.id_jury_membre)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Ajouter un membre -->
                        <button x-show="!dossier.jury_complet" @click="openAjouterMembreModal(dossier.id_dossier)" 
                                class="mt-3 text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Ajouter un membre
                        </button>
                    </div>
                </div>
            </template>
        </div>
        
        <div x-show="dossiers.length === 0" class="p-12 text-center text-gray-500">
            Aucun dossier trouvé
        </div>
    </div>

    <!-- Mes invitations -->
    <div x-show="onglet === 'invitations'" class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            <template x-for="invitation in mesInvitations" :key="invitation.id_jury_membre">
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-medium text-gray-900" x-text="invitation.nom_etu + ' ' + invitation.prenom_etu"></h3>
                        <p class="text-sm text-gray-500" x-text="invitation.theme || 'Thème non défini'"></p>
                        <p class="text-sm text-gray-500">
                            Rôle: <span class="font-medium" x-text="invitation.role"></span>
                        </p>
                        <p class="text-sm text-gray-500" x-text="'Date soutenance: ' + formatDate(invitation.date_soutenance)"></p>
                    </div>
                    <div x-show="invitation.statut === 'Invite'" class="flex space-x-2">
                        <button @click="accepterInvitation(invitation.id_jury_membre)" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Accepter
                        </button>
                        <button @click="refuserInvitation(invitation.id_jury_membre)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Refuser
                        </button>
                    </div>
                    <div x-show="invitation.statut !== 'Invite'">
                        <span :class="{
                            'bg-green-100 text-green-800': invitation.statut === 'Accepte',
                            'bg-red-100 text-red-800': invitation.statut === 'Refuse'
                        }" class="px-3 py-1 text-sm font-semibold rounded-full" x-text="invitation.statut"></span>
                    </div>
                </div>
            </template>
        </div>
        
        <div x-show="mesInvitations.length === 0" class="p-12 text-center text-gray-500">
            Aucune invitation
        </div>
    </div>

    <!-- Modal Ajouter Membre -->
    <div x-show="showAjouterModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeAjouterModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter un Membre au Jury</h3>
                <form @submit.prevent="ajouterMembre()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Enseignant *</label>
                            <input type="text" x-model="ajouterForm.enseignant_recherche" @input.debounce.300ms="rechercherEnseignants()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher un enseignant...">
                            <div x-show="enseignantsDisponibles.length > 0" class="mt-1 bg-white border rounded-md shadow-sm max-h-40 overflow-y-auto">
                                <template x-for="ens in enseignantsDisponibles" :key="ens.id_enseignant">
                                    <div @click="selectionnerEnseignant(ens)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span x-text="ens.nom_ens + ' ' + ens.prenom_ens"></span>
                                        <span class="text-gray-500 text-sm" x-text="' - ' + ens.lib_grade"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rôle *</label>
                            <select x-model="ajouterForm.role" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="President">Président</option>
                                <option value="Rapporteur">Rapporteur</option>
                                <option value="Examinateur">Examinateur</option>
                                <option value="Encadreur">Encadreur</option>
                                <option value="Co-encadreur">Co-encadreur</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeAjouterModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function juryModule() {
    return {
        onglet: 'dossiers',
        dossiers: [],
        mesInvitations: [],
        statistiques: {},
        recherche: '',
        showAjouterModal: false,
        dossierId: null,
        ajouterForm: {
            enseignant_id: null,
            enseignant_recherche: '',
            role: 'Examinateur'
        },
        enseignantsDisponibles: [],

        async init() {
            await this.chargerDossiers();
            await this.chargerMesInvitations();
            await this.chargerStatistiques();
        },

        async chargerDossiers() {
            const params = new URLSearchParams();
            if (this.recherche) params.append('q', this.recherche);
            
            const response = await fetch('/api/scolarite/dossiers?statut=jury_en_constitution&' + params);
            const data = await response.json();
            if (data.success) {
                // Charger le jury pour chaque dossier
                const dossiersAvecJury = await Promise.all(data.data.map(async (dossier) => {
                    const juryResponse = await fetch('/api/jury/dossier/' + dossier.id_dossier);
                    const juryData = await juryResponse.json();
                    if (juryData.success) {
                        dossier.jury = juryData.data.membres || [];
                        dossier.nombre_acceptes = juryData.data.membres?.filter(m => m.statut === 'Accepte').length || 0;
                        dossier.jury_complet = dossier.nombre_acceptes >= 3;
                    } else {
                        dossier.jury = [];
                    }
                    return dossier;
                }));
                this.dossiers = dossiersAvecJury;
            }
        },

        async chargerMesInvitations() {
            const response = await fetch('/api/jury/mes-invitations');
            const data = await response.json();
            if (data.success) {
                this.mesInvitations = data.data;
            }
        },

        async chargerStatistiques() {
            const response = await fetch('/api/jury/statistiques');
            const data = await response.json();
            if (data.success) {
                this.statistiques = data.data;
            }
        },

        async rechercherEnseignants() {
            if (this.ajouterForm.enseignant_recherche.length < 2) {
                this.enseignantsDisponibles = [];
                return;
            }
            const response = await fetch('/api/jury/enseignants-disponibles?q=' + encodeURIComponent(this.ajouterForm.enseignant_recherche));
            const data = await response.json();
            if (data.success) {
                this.enseignantsDisponibles = data.data.slice(0, 10);
            }
        },

        selectionnerEnseignant(ens) {
            this.ajouterForm.enseignant_id = ens.id_enseignant;
            this.ajouterForm.enseignant_recherche = ens.nom_ens + ' ' + ens.prenom_ens;
            this.enseignantsDisponibles = [];
        },

        async ajouterMembre() {
            const formData = new FormData();
            formData.append('dossier_id', this.dossierId);
            formData.append('enseignant_id', this.ajouterForm.enseignant_id);
            formData.append('role', this.ajouterForm.role);

            const response = await fetch('/api/jury', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closeAjouterModal();
                await this.chargerDossiers();
                alert('Membre ajouté et invitation envoyée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async retirerMembre(membreId) {
            if (!confirm('Retirer ce membre du jury ?')) return;
            
            const response = await fetch('/api/jury/' + membreId, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerDossiers();
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async accepterInvitation(membreId) {
            const response = await fetch('/api/jury/' + membreId + '/accepter', {
                method: 'POST'
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerMesInvitations();
                alert('Invitation acceptée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async refuserInvitation(membreId) {
            const motif = prompt('Motif du refus:');
            if (!motif) return;

            const formData = new FormData();
            formData.append('motif', motif);

            const response = await fetch('/api/jury/' + membreId + '/refuser', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerMesInvitations();
                alert('Invitation refusée');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        openAjouterMembreModal(dossierId) {
            this.dossierId = dossierId;
            this.showAjouterModal = true;
            this.ajouterForm = {
                enseignant_id: null,
                enseignant_recherche: '',
                role: 'Examinateur'
            };
        },

        closeAjouterModal() {
            this.showAjouterModal = false;
        },

        formatDate(date) {
            if (!date) return 'Non définie';
            return new Date(date).toLocaleDateString('fr-FR');
        }
    }
}
</script>
