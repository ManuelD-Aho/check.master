<?php
/**
 * Vue des documents générés
 * 
 * @see PRD 06 - Documents & Archives
 */
$pageTitle = 'Documents Générés';
$breadcrumb = [
    ['label' => 'Documents', 'active' => true],
];
?>

<div x-data="documentsModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documents Générés</h1>
            <p class="mt-1 text-sm text-gray-600">Gestion des documents PDF générés par le système</p>
        </div>
        <button @click="openGenerateModal()" class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Générer un document
        </button>
    </div>

    <!-- Types de documents -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <template x-for="(label, type) in typesDocuments" :key="type">
            <button @click="filtreType = (filtreType === type ? '' : type)" 
                    :class="filtreType === type ? 'bg-blue-100 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'"
                    class="p-3 rounded-lg border-2 text-center">
                <div class="text-2xl mb-1">📄</div>
                <div class="text-xs font-medium" x-text="label"></div>
            </button>
        </template>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" x-model="filtres.recherche" @input.debounce.300ms="chargerDocuments()" 
                       class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Nom du fichier...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date du</label>
                <input type="date" x-model="filtres.date_debut" @change="chargerDocuments()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" x-model="filtres.date_fin" @change="chargerDocuments()" 
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
    </div>

    <!-- Liste des documents -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taille</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="doc in documents" :key="doc.id_document">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="doc.nom_fichier"></div>
                                    <div class="text-xs text-gray-500 font-mono" x-text="doc.hash_sha256?.substring(0, 16) + '...'"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800" x-text="typesDocuments[doc.type_document] || doc.type_document"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span x-show="doc.entite_type" x-text="doc.entite_type + ' #' + doc.entite_id"></span>
                            <span x-show="!doc.entite_type">-</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(doc.created_at)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatSize(doc.taille)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="telecharger(doc.id_document)" class="text-blue-600 hover:text-blue-900 mr-3" title="Télécharger">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                            <button @click="regenerer(doc.id_document)" class="text-green-600 hover:text-green-900" title="Régénérer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <div x-show="documents.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500">Aucun document trouvé</p>
        </div>
    </div>

    <!-- Modal Génération -->
    <div x-show="showGenerateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeGenerateModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Générer un Document</h3>
                <form @submit.prevent="genererDocument()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type de document *</label>
                            <select x-model="generateForm.type" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Sélectionner...</option>
                                <template x-for="(label, type) in typesDocuments" :key="type">
                                    <option :value="type" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Données (JSON)</label>
                            <textarea x-model="generateForm.donnees" class="mt-1 w-full border-gray-300 rounded-md shadow-sm font-mono text-sm" rows="6" placeholder='{"etudiant_nom": "Nom", ...}'></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Type entité</label>
                                <input type="text" x-model="generateForm.entite_type" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="ex: etudiant">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ID entité</label>
                                <input type="number" x-model="generateForm.entite_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeGenerateModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Générer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function documentsModule() {
    return {
        documents: [],
        typesDocuments: {
            'recu_paiement': 'Reçu paiement',
            'recu_penalite': 'Reçu pénalité',
            'bulletin_notes': 'Bulletin notes',
            'pv_commission': 'PV commission',
            'pv_soutenance': 'PV soutenance',
            'convocation': 'Convocation',
            'attestation_diplome': 'Attestation diplôme',
            'certificat_scolarite': 'Certificat scolarité',
            'lettre_jury': 'Lettre jury',
            'attestation_stage': 'Attestation stage',
            'bordereau_transmission': 'Bordereau',
            'rapport_evaluation': 'Rapport évaluation',
            'bulletin_provisoire': 'Bulletin provisoire'
        },
        filtreType: '',
        filtres: {
            recherche: '',
            date_debut: '',
            date_fin: ''
        },
        showGenerateModal: false,
        generateForm: {
            type: '',
            donnees: '',
            entite_type: '',
            entite_id: ''
        },

        async init() {
            await this.chargerDocuments();
            this.$watch('filtreType', () => this.chargerDocuments());
        },

        async chargerDocuments() {
            const params = {...this.filtres};
            if (this.filtreType) params.type = this.filtreType;
            
            const response = await fetch('/api/documents?' + new URLSearchParams(params));
            const data = await response.json();
            if (data.success) {
                this.documents = data.data;
            }
        },

        telecharger(id) {
            window.open('/api/documents/' + id + '/telecharger', '_blank');
        },

        async regenerer(id) {
            if (!confirm('Régénérer ce document ?')) return;
            
            const response = await fetch('/api/documents/' + id + '/regenerer', {
                method: 'POST'
            });
            const data = await response.json();
            if (data.success) {
                await this.chargerDocuments();
                alert('Document régénéré');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        async genererDocument() {
            let donnees = {};
            try {
                if (this.generateForm.donnees) {
                    donnees = JSON.parse(this.generateForm.donnees);
                }
            } catch (e) {
                alert('Format JSON invalide');
                return;
            }

            const formData = new FormData();
            formData.append('type', this.generateForm.type);
            formData.append('donnees', JSON.stringify(donnees));
            if (this.generateForm.entite_type) formData.append('entite_type', this.generateForm.entite_type);
            if (this.generateForm.entite_id) formData.append('entite_id', this.generateForm.entite_id);

            const response = await fetch('/api/documents/generer', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closeGenerateModal();
                await this.chargerDocuments();
                alert('Document généré avec succès');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        openGenerateModal() {
            this.showGenerateModal = true;
            this.generateForm = {
                type: '',
                donnees: '',
                entite_type: '',
                entite_id: ''
            };
        },

        closeGenerateModal() {
            this.showGenerateModal = false;
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatSize(bytes) {
            if (!bytes) return '-';
            const sizes = ['o', 'Ko', 'Mo', 'Go'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
        }
    }
}
</script>
