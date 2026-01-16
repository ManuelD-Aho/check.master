<?php
/**
 * Vue du planning des soutenances
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
$pageTitle = 'Planning des Soutenances';
$breadcrumb = [
    ['label' => 'Soutenances', 'url' => '/soutenance'],
    ['label' => 'Planning', 'active' => true],
];
?>

<div x-data="planningModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Planning des Soutenances</h1>
            <p class="mt-1 text-sm text-gray-600">Calendrier et planification des soutenances</p>
        </div>
        <button @click="openPlanningModal()" class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Planifier une soutenance
        </button>
    </div>

    <!-- Navigation du calendrier -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex items-center justify-between">
            <button @click="moisPrecedent()" class="p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <h2 class="text-lg font-semibold text-gray-900" x-text="formatMoisAnnee(dateCourante)"></h2>
            <button @click="moisSuivant()" class="p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Calendrier -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- En-têtes des jours -->
        <div class="grid grid-cols-7 bg-gray-50 border-b">
            <template x-for="jour in joursNoms" :key="jour">
                <div class="p-3 text-center text-sm font-medium text-gray-700" x-text="jour"></div>
            </template>
        </div>

        <!-- Grille du calendrier -->
        <div class="grid grid-cols-7">
            <template x-for="(jour, index) in joursCalendrier" :key="index">
                <div :class="[
                    'min-h-24 border-r border-b p-2 transition-colors',
                    jour.estMoisCourant ? 'bg-white hover:bg-gray-50' : 'bg-gray-50',
                    jour.estAujourdhui ? 'bg-blue-50' : ''
                ]"
                @click="jour.estMoisCourant && selectionnerJour(jour.date)">
                    <!-- Numéro du jour -->
                    <div class="flex items-center justify-between mb-1">
                        <span :class="[
                            'text-sm font-medium',
                            jour.estMoisCourant ? 'text-gray-900' : 'text-gray-400',
                            jour.estAujourdhui ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : ''
                        ]" x-text="jour.numero"></span>
                        <span x-show="jour.soutenances.length > 0" class="bg-green-100 text-green-800 text-xs px-1 rounded" x-text="jour.soutenances.length"></span>
                    </div>
                    
                    <!-- Mini liste des soutenances -->
                    <div class="space-y-1">
                        <template x-for="soutenance in jour.soutenances.slice(0, 3)" :key="soutenance.id_soutenance">
                            <div @click.stop="ouvrirDetailSoutenance(soutenance)" class="text-xs p-1 rounded truncate cursor-pointer"
                                 :class="{
                                     'bg-blue-100 text-blue-800': soutenance.statut === 'Planifiee',
                                     'bg-orange-100 text-orange-800': soutenance.statut === 'En_cours',
                                     'bg-green-100 text-green-800': soutenance.statut === 'Terminee',
                                     'bg-red-100 text-red-800': soutenance.statut === 'Annulee'
                                 }">
                                <span x-text="soutenance.heure_debut + ' ' + soutenance.nom_etu?.substring(0, 10)"></span>
                            </div>
                        </template>
                        <div x-show="jour.soutenances.length > 3" class="text-xs text-gray-500">
                            + <span x-text="jour.soutenances.length - 3"></span> autres
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Liste des soutenances du jour sélectionné -->
    <div x-show="jourSelectionne" class="mt-6 bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            Soutenances du <span x-text="formatDate(jourSelectionne)"></span>
        </h3>
        <div class="divide-y divide-gray-200">
            <template x-for="soutenance in soutenancesJourSelectionne" :key="soutenance.id_soutenance">
                <div class="py-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-900" x-text="soutenance.nom_etu + ' ' + soutenance.prenom_etu"></p>
                        <p class="text-sm text-gray-500" x-text="soutenance.heure_debut + ' - ' + soutenance.heure_fin + ' | ' + (soutenance.nom_salle || 'Salle non définie')"></p>
                        <p class="text-sm text-gray-500" x-text="'Thème: ' + (soutenance.theme || 'Non défini')"></p>
                    </div>
                    <div class="text-right">
                        <span :class="{
                            'bg-blue-100 text-blue-800': soutenance.statut === 'Planifiee',
                            'bg-orange-100 text-orange-800': soutenance.statut === 'En_cours',
                            'bg-green-100 text-green-800': soutenance.statut === 'Terminee',
                            'bg-red-100 text-red-800': soutenance.statut === 'Annulee'
                        }" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="soutenance.statut"></span>
                        <div class="mt-2">
                            <a :href="'/soutenance/' + soutenance.id_soutenance" class="text-sm text-blue-600 hover:text-blue-800">Détails</a>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="soutenancesJourSelectionne.length === 0" class="py-8 text-center text-gray-500">
                Aucune soutenance ce jour
            </div>
        </div>
    </div>

    <!-- Modal Planifier -->
    <div x-show="showPlanningModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closePlanningModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Planifier une Soutenance</h3>
                <form @submit.prevent="planifierSoutenance()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dossier étudiant *</label>
                            <input type="text" x-model="planningForm.dossier_recherche" @input.debounce.300ms="rechercherDossiers()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher par nom ou matricule...">
                            <div x-show="dossiersResultats.length > 0" class="mt-1 bg-white border rounded-md shadow-sm max-h-40 overflow-y-auto">
                                <template x-for="dossier in dossiersResultats" :key="dossier.id_dossier">
                                    <div @click="selectionnerDossier(dossier)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span x-text="dossier.nom_etu + ' ' + dossier.prenom_etu"></span>
                                        <span class="text-gray-500 text-sm" x-text="' - ' + dossier.numero_carte"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date *</label>
                            <input type="date" x-model="planningForm.date_soutenance" @change="verifierDisponibilites()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Heure début *</label>
                                <input type="time" x-model="planningForm.heure_debut" @change="verifierDisponibilites()"
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Heure fin *</label>
                                <input type="time" x-model="planningForm.heure_fin" 
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Salle *</label>
                            <select x-model="planningForm.salle_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Sélectionner une salle</option>
                                <template x-for="salle in sallesDisponibles" :key="salle.id_salle">
                                    <option :value="salle.id_salle" x-text="salle.nom_salle + ' (' + salle.capacite + ' places)'"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Conflits détectés -->
                        <div x-show="conflits.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-medium text-red-800 mb-2">Conflits détectés</h4>
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                <template x-for="conflit in conflits" :key="conflit.message">
                                    <li x-text="conflit.message"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closePlanningModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" :disabled="conflits.length > 0" :class="conflits.length > 0 ? 'bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'" class="px-4 py-2 text-white rounded-md">Planifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Détails Soutenance -->
    <div x-show="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeDetailModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6" x-show="soutenanceSelectionnee">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Détails de la Soutenance</h3>
                    <button @click="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Étudiant</p>
                        <p class="font-medium" x-text="soutenanceSelectionnee?.nom_etu + ' ' + soutenanceSelectionnee?.prenom_etu"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date & Heure</p>
                        <p class="font-medium" x-text="formatDate(soutenanceSelectionnee?.date_soutenance) + ' ' + soutenanceSelectionnee?.heure_debut"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Salle</p>
                        <p class="font-medium" x-text="soutenanceSelectionnee?.nom_salle || 'Non définie'"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        <span :class="{
                            'bg-blue-100 text-blue-800': soutenanceSelectionnee?.statut === 'Planifiee',
                            'bg-orange-100 text-orange-800': soutenanceSelectionnee?.statut === 'En_cours',
                            'bg-green-100 text-green-800': soutenanceSelectionnee?.statut === 'Terminee'
                        }" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="soutenanceSelectionnee?.statut"></span>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-500 mb-1">Thème</p>
                    <p class="text-gray-700" x-text="soutenanceSelectionnee?.theme || 'Non défini'"></p>
                </div>

                <div x-show="soutenanceSelectionnee?.note_finale" class="bg-green-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-500">Résultat</p>
                    <p class="text-2xl font-bold text-green-600" x-text="soutenanceSelectionnee?.note_finale + '/20 - ' + soutenanceSelectionnee?.mention"></p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a :href="'/soutenance/' + soutenanceSelectionnee?.id_soutenance" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Voir détails complets</a>
                    <button x-show="soutenanceSelectionnee?.statut === 'Planifiee'" @click="demarrerSoutenance()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Démarrer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function planningModule() {
    return {
        dateCourante: new Date(),
        joursNoms: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
        joursCalendrier: [],
        soutenancesMois: [],
        jourSelectionne: null,
        soutenancesJourSelectionne: [],
        showPlanningModal: false,
        showDetailModal: false,
        soutenanceSelectionnee: null,
        planningForm: {
            dossier_id: null,
            dossier_recherche: '',
            date_soutenance: '',
            heure_debut: '09:00',
            heure_fin: '11:00',
            salle_id: ''
        },
        dossiersResultats: [],
        sallesDisponibles: [],
        conflits: [],

        async init() {
            await this.chargerSoutenancesMois();
            this.genererCalendrier();
        },

        async chargerSoutenancesMois() {
            const debut = new Date(this.dateCourante.getFullYear(), this.dateCourante.getMonth(), 1);
            const fin = new Date(this.dateCourante.getFullYear(), this.dateCourante.getMonth() + 1, 0);
            
            const params = new URLSearchParams({
                date_debut: debut.toISOString().split('T')[0],
                date_fin: fin.toISOString().split('T')[0]
            });
            
            const response = await fetch('/api/soutenances?' + params);
            const data = await response.json();
            if (data.success) {
                this.soutenancesMois = data.data;
            }
            this.genererCalendrier();
        },

        genererCalendrier() {
            const annee = this.dateCourante.getFullYear();
            const mois = this.dateCourante.getMonth();
            
            const premierJour = new Date(annee, mois, 1);
            const dernierJour = new Date(annee, mois + 1, 0);
            
            // Ajuster pour commencer le lundi
            let jourDebutSemaine = premierJour.getDay() || 7;
            jourDebutSemaine--;
            
            const jours = [];
            const aujourdhui = new Date().toISOString().split('T')[0];
            
            // Jours du mois précédent
            for (let i = jourDebutSemaine - 1; i >= 0; i--) {
                const date = new Date(annee, mois, -i);
                jours.push({
                    date: date.toISOString().split('T')[0],
                    numero: date.getDate(),
                    estMoisCourant: false,
                    estAujourdhui: false,
                    soutenances: []
                });
            }
            
            // Jours du mois courant
            for (let i = 1; i <= dernierJour.getDate(); i++) {
                const date = new Date(annee, mois, i).toISOString().split('T')[0];
                jours.push({
                    date: date,
                    numero: i,
                    estMoisCourant: true,
                    estAujourdhui: date === aujourdhui,
                    soutenances: this.soutenancesMois.filter(s => s.date_soutenance === date)
                });
            }
            
            // Compléter avec les jours du mois suivant
            const joursRestants = 42 - jours.length;
            for (let i = 1; i <= joursRestants; i++) {
                const date = new Date(annee, mois + 1, i);
                jours.push({
                    date: date.toISOString().split('T')[0],
                    numero: i,
                    estMoisCourant: false,
                    estAujourdhui: false,
                    soutenances: []
                });
            }
            
            this.joursCalendrier = jours;
        },

        moisPrecedent() {
            this.dateCourante = new Date(this.dateCourante.getFullYear(), this.dateCourante.getMonth() - 1, 1);
            this.chargerSoutenancesMois();
        },

        moisSuivant() {
            this.dateCourante = new Date(this.dateCourante.getFullYear(), this.dateCourante.getMonth() + 1, 1);
            this.chargerSoutenancesMois();
        },

        selectionnerJour(date) {
            this.jourSelectionne = date;
            this.soutenancesJourSelectionne = this.soutenancesMois.filter(s => s.date_soutenance === date);
        },

        ouvrirDetailSoutenance(soutenance) {
            this.soutenanceSelectionnee = soutenance;
            this.showDetailModal = true;
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.soutenanceSelectionnee = null;
        },

        async rechercherDossiers() {
            if (this.planningForm.dossier_recherche.length < 2) {
                this.dossiersResultats = [];
                return;
            }
            const response = await fetch('/api/scolarite/dossiers?q=' + encodeURIComponent(this.planningForm.dossier_recherche) + '&statut=en_cours');
            const data = await response.json();
            if (data.success) {
                this.dossiersResultats = data.data.slice(0, 10);
            }
        },

        selectionnerDossier(dossier) {
            this.planningForm.dossier_id = dossier.id_dossier;
            this.planningForm.dossier_recherche = dossier.nom_etu + ' ' + dossier.prenom_etu;
            this.dossiersResultats = [];
        },

        async verifierDisponibilites() {
            if (!this.planningForm.date_soutenance || !this.planningForm.heure_debut || !this.planningForm.heure_fin) {
                return;
            }
            
            // Charger les salles disponibles
            const paramsSalles = new URLSearchParams({
                date: this.planningForm.date_soutenance,
                heure_debut: this.planningForm.heure_debut,
                heure_fin: this.planningForm.heure_fin
            });
            const responseSalles = await fetch('/api/calendrier/salles-disponibles?' + paramsSalles);
            const dataSalles = await responseSalles.json();
            if (dataSalles.success) {
                this.sallesDisponibles = dataSalles.data;
            }
            
            // Vérifier les conflits
            if (this.planningForm.dossier_id) {
                const formData = new FormData();
                formData.append('dossier_id', this.planningForm.dossier_id);
                formData.append('date', this.planningForm.date_soutenance);
                formData.append('heure_debut', this.planningForm.heure_debut);
                formData.append('heure_fin', this.planningForm.heure_fin);
                if (this.planningForm.salle_id) {
                    formData.append('salle_id', this.planningForm.salle_id);
                }
                
                const responseConflits = await fetch('/api/calendrier/verifier-conflits', {
                    method: 'POST',
                    body: formData
                });
                const dataConflits = await responseConflits.json();
                if (dataConflits.success) {
                    this.conflits = dataConflits.data.conflits;
                }
            }
        },

        async planifierSoutenance() {
            if (this.conflits.length > 0) {
                alert('Veuillez résoudre les conflits avant de planifier');
                return;
            }

            const formData = new FormData();
            formData.append('dossier_id', this.planningForm.dossier_id);
            formData.append('date_soutenance', this.planningForm.date_soutenance);
            formData.append('heure_debut', this.planningForm.heure_debut);
            formData.append('heure_fin', this.planningForm.heure_fin);
            formData.append('salle_id', this.planningForm.salle_id);

            const response = await fetch('/api/soutenances', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closePlanningModal();
                await this.chargerSoutenancesMois();
                alert('Soutenance planifiée avec succès');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async demarrerSoutenance() {
            if (!confirm('Démarrer cette soutenance ?')) return;
            
            const response = await fetch('/api/soutenances/' + this.soutenanceSelectionnee.id_soutenance + '/demarrer', {
                method: 'POST'
            });
            const data = await response.json();
            if (data.success) {
                this.closeDetailModal();
                await this.chargerSoutenancesMois();
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        openPlanningModal() {
            this.showPlanningModal = true;
            this.planningForm = {
                dossier_id: null,
                dossier_recherche: '',
                date_soutenance: this.jourSelectionne || '',
                heure_debut: '09:00',
                heure_fin: '11:00',
                salle_id: ''
            };
            this.conflits = [];
            this.sallesDisponibles = [];
        },

        closePlanningModal() {
            this.showPlanningModal = false;
        },

        formatMoisAnnee(date) {
            return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }
}
</script>
