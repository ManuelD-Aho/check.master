<?php
/**
 * Vue de la liste des soutenances
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
$pageTitle = 'Gestion des Soutenances';
$breadcrumb = [
    ['label' => 'Soutenances', 'active' => true],
];
?>

<div x-data="soutenancesModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Soutenances</h1>
            <p class="mt-1 text-sm text-gray-600">Planification et suivi des soutenances</p>
        </div>
        <a href="/soutenance/planning" class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Planning
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Planifiées</p>
            <p class="text-2xl font-semibold text-blue-600" x-text="statistiques.planifiees || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">En cours</p>
            <p class="text-2xl font-semibold text-orange-600" x-text="statistiques.en_cours || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Terminées</p>
            <p class="text-2xl font-semibold text-green-600" x-text="statistiques.terminees || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Reportées</p>
            <p class="text-2xl font-semibold text-yellow-600" x-text="statistiques.reportees || 0"></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Moyenne des notes</p>
            <p class="text-2xl font-semibold text-purple-600" x-text="(statistiques.moyenne_notes || 0).toFixed(2) + '/20'"></p>
        </div>
    </div>

    <!-- Soutenances du jour -->
    <div x-show="soutenancesJour.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-yellow-800 mb-2">
            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            Soutenances aujourd'hui
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="sout in soutenancesJour" :key="sout.id_soutenance">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium" x-text="sout.nom_etu + ' ' + sout.prenom_etu"></p>
                            <p class="text-sm text-gray-500" x-text="sout.heure_debut + ' - ' + sout.heure_fin"></p>
                            <p class="text-sm text-gray-500" x-text="sout.nom_salle"></p>
                        </div>
                        <span :class="getStatutClass(sout.statut)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="sout.statut"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" x-model="filtres.recherche" @input.debounce.300ms="chargerSoutenances()" 
                       class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Étudiant, thème...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select x-model="filtres.statut" @change="chargerSoutenances()" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Tous les statuts</option>
                    <option value="Planifiee">Planifiées</option>
                    <option value="En_cours">En cours</option>
                    <option value="Terminee">Terminées</option>
                    <option value="Reportee">Reportées</option>
                    <option value="Annulee">Annulées</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date du</label>
                <input type="date" x-model="filtres.date_debut" @change="chargerSoutenances()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" x-model="filtres.date_fin" @change="chargerSoutenances()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
    </div>

    <!-- Liste des soutenances -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thème</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Heure</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="sout in soutenances" :key="sout.id_soutenance">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900" x-text="sout.nom_etu + ' ' + sout.prenom_etu"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 line-clamp-2" x-text="sout.theme"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div x-text="formatDate(sout.date_soutenance)"></div>
                            <div x-text="sout.heure_debut + ' - ' + sout.heure_fin"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="sout.nom_salle || '-'"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getStatutClass(sout.statut)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="sout.statut"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span x-show="sout.note_finale" class="text-sm font-semibold" x-text="sout.note_finale + '/20'"></span>
                            <span x-show="sout.mention" class="text-xs text-gray-500 block" x-text="sout.mention"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a :href="'/soutenance/' + sout.id_soutenance" class="text-blue-600 hover:text-blue-900 mr-3">Détails</a>
                            <button x-show="sout.pv_genere" @click="telechargerPV(sout.id_soutenance)" class="text-green-600 hover:text-green-900" title="PV">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <div x-show="soutenances.length === 0" class="text-center py-12">
            <p class="text-gray-500">Aucune soutenance trouvée</p>
        </div>
    </div>
</div>

<script>
function soutenancesModule() {
    return {
        soutenances: [],
        soutenancesJour: [],
        statistiques: {},
        filtres: {
            recherche: '',
            statut: '',
            date_debut: '',
            date_fin: ''
        },

        async init() {
            await this.chargerSoutenances();
            await this.chargerStatistiques();
            await this.chargerSoutenancesJour();
        },

        async chargerSoutenances() {
            const params = new URLSearchParams(this.filtres);
            const response = await fetch('/api/soutenances?' + params);
            const data = await response.json();
            if (data.success) {
                this.soutenances = data.data;
            }
        },

        async chargerStatistiques() {
            const response = await fetch('/api/soutenances/statistiques');
            const data = await response.json();
            if (data.success) {
                this.statistiques = data.data;
            }
        },

        async chargerSoutenancesJour() {
            const response = await fetch('/api/soutenances/planning/jour');
            const data = await response.json();
            if (data.success) {
                this.soutenancesJour = data.data;
            }
        },

        telechargerPV(id) {
            window.open('/api/soutenances/' + id + '/pv', '_blank');
        },

        getStatutClass(statut) {
            const classes = {
                'Planifiee': 'bg-blue-100 text-blue-800',
                'En_cours': 'bg-orange-100 text-orange-800',
                'Terminee': 'bg-green-100 text-green-800',
                'Reportee': 'bg-yellow-100 text-yellow-800',
                'Annulee': 'bg-red-100 text-red-800'
            };
            return classes[statut] || 'bg-gray-100 text-gray-800';
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    }
}
</script>
