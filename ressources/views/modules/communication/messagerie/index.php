<?php
/**
 * Vue de la messagerie interne
 * 
 * @see PRD 05 - Communication
 */
$pageTitle = 'Messagerie';
$breadcrumb = [
    ['label' => 'Communication', 'url' => '/communication'],
    ['label' => 'Messagerie', 'active' => true],
];
?>

<div x-data="messagerieModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <div class="flex gap-6">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0">
            <button @click="openComposeModal()" class="w-full mb-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Nouveau message
            </button>

            <nav class="bg-white rounded-lg shadow">
                <a @click.prevent="boite = 'reception'" href="#" :class="boite === 'reception' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'" class="flex items-center px-4 py-3">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    Boîte de réception
                    <span x-show="nonLus > 0" class="ml-auto bg-blue-600 text-white px-2 py-0.5 rounded-full text-xs" x-text="nonLus"></span>
                </a>
                <a @click.prevent="boite = 'envoyes'" href="#" :class="boite === 'envoyes' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'" class="flex items-center px-4 py-3">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Messages envoyés
                </a>
            </nav>
        </div>

        <!-- Messages list -->
        <div class="flex-1 bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <input type="text" x-model="recherche" @input.debounce.300ms="chargerMessages()" 
                       class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher dans les messages...">
            </div>

            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                <template x-for="message in messages" :key="message.id_message">
                    <div @click="ouvrirMessage(message)" :class="!message.lu ? 'bg-blue-50' : ''" class="p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate" :class="!message.lu ? 'text-gray-900 font-semibold' : 'text-gray-700'" x-text="boite === 'reception' ? (message.expediteur_nom || 'Utilisateur') : (message.destinataire_nom || 'Destinataire')"></p>
                                <p class="text-sm text-gray-900 truncate" :class="!message.lu ? 'font-semibold' : ''" x-text="message.sujet || '(Sans sujet)'"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="message.contenu?.substring(0, 80) + '...'"></p>
                            </div>
                            <div class="ml-4 text-right">
                                <p class="text-xs text-gray-500" x-text="formatDate(message.created_at)"></p>
                                <span x-show="!message.lu" class="inline-block w-2 h-2 bg-blue-600 rounded-full mt-1"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="messages.length === 0" class="p-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p>Aucun message</p>
            </div>
        </div>

        <!-- Message detail -->
        <div x-show="messageOuvert" class="w-96 flex-shrink-0 bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium text-gray-900" x-text="messageOuvert?.sujet || '(Sans sujet)'"></h3>
                <button @click="messageOuvert = null" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-sm text-gray-500">De: <span class="text-gray-900" x-text="messageOuvert?.expediteur_nom || 'Utilisateur'"></span></p>
                    <p class="text-sm text-gray-500">À: <span class="text-gray-900" x-text="messageOuvert?.destinataire_nom || 'Moi'"></span></p>
                    <p class="text-sm text-gray-500">Date: <span class="text-gray-900" x-text="formatDate(messageOuvert?.created_at)"></span></p>
                </div>
                <div class="prose prose-sm max-w-none">
                    <p class="whitespace-pre-wrap text-gray-700" x-text="messageOuvert?.contenu"></p>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <button @click="openReplyModal()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Répondre
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nouveau Message -->
    <div x-show="showComposeModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeComposeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="isReply ? 'Répondre' : 'Nouveau message'"></h3>
                <form @submit.prevent="envoyerMessage()">
                    <div class="space-y-4">
                        <div x-show="!isReply">
                            <label class="block text-sm font-medium text-gray-700">Destinataire *</label>
                            <input type="text" x-model="composeForm.destinataire_recherche" @input.debounce.300ms="rechercherUtilisateur()" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Rechercher un utilisateur...">
                            <div x-show="utilisateursResultats.length > 0" class="mt-1 bg-white border rounded-md shadow-sm max-h-40 overflow-y-auto">
                                <template x-for="user in utilisateursResultats" :key="user.id_utilisateur">
                                    <div @click="selectionnerDestinataire(user)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span x-text="user.nom + ' - ' + user.login_utilisateur"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sujet</label>
                            <input type="text" x-model="composeForm.sujet" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Objet du message">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Message *</label>
                            <textarea x-model="composeForm.contenu" class="mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="6" required placeholder="Écrivez votre message..."></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeComposeModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function messagerieModule() {
    return {
        boite: 'reception',
        messages: [],
        messageOuvert: null,
        nonLus: 0,
        recherche: '',
        showComposeModal: false,
        isReply: false,
        composeForm: {
            destinataire_id: null,
            destinataire_recherche: '',
            sujet: '',
            contenu: ''
        },
        utilisateursResultats: [],

        async init() {
            await this.chargerMessages();
            await this.compterNonLus();
            
            this.$watch('boite', () => this.chargerMessages());
        },

        async chargerMessages() {
            const url = this.boite === 'reception' ? '/api/messages/recus' : '/api/messages/envoyes';
            const response = await fetch(url);
            const data = await response.json();
            if (data.success) {
                this.messages = data.data;
            }
        },

        async compterNonLus() {
            const response = await fetch('/api/messages/non-lus/count');
            const data = await response.json();
            if (data.success) {
                this.nonLus = data.data.count;
            }
        },

        async ouvrirMessage(message) {
            const response = await fetch('/api/messages/' + message.id_message);
            const data = await response.json();
            if (data.success) {
                this.messageOuvert = data.data;
                if (!message.lu) {
                    message.lu = true;
                    this.compterNonLus();
                }
            }
        },

        async rechercherUtilisateur() {
            if (this.composeForm.destinataire_recherche.length < 2) {
                this.utilisateursResultats = [];
                return;
            }
            const response = await fetch('/api/admin/utilisateurs?q=' + encodeURIComponent(this.composeForm.destinataire_recherche));
            const data = await response.json();
            if (data.success) {
                this.utilisateursResultats = data.data.slice(0, 10);
            }
        },

        selectionnerDestinataire(user) {
            this.composeForm.destinataire_id = user.id_utilisateur;
            this.composeForm.destinataire_recherche = user.nom || user.login_utilisateur;
            this.utilisateursResultats = [];
        },

        async envoyerMessage() {
            const formData = new FormData();
            formData.append('destinataire_id', this.composeForm.destinataire_id);
            formData.append('sujet', this.composeForm.sujet);
            formData.append('contenu', this.composeForm.contenu);

            const url = this.isReply ? '/api/messages/' + this.messageOuvert.id_message + '/repondre' : '/api/messages';
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                this.closeComposeModal();
                await this.chargerMessages();
                alert('Message envoyé');
            } else {
                alert('Erreur: ' + data.error);
            }
        },

        openComposeModal() {
            this.isReply = false;
            this.showComposeModal = true;
            this.composeForm = {
                destinataire_id: null,
                destinataire_recherche: '',
                sujet: '',
                contenu: ''
            };
        },

        openReplyModal() {
            this.isReply = true;
            this.showComposeModal = true;
            this.composeForm = {
                destinataire_id: this.messageOuvert.expediteur_id,
                destinataire_recherche: this.messageOuvert.expediteur_nom,
                sujet: 'Re: ' + (this.messageOuvert.sujet || ''),
                contenu: ''
            };
        },

        closeComposeModal() {
            this.showComposeModal = false;
        },

        formatDate(date) {
            if (!date) return '-';
            const d = new Date(date);
            const now = new Date();
            if (d.toDateString() === now.toDateString()) {
                return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            }
            return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        }
    }
}
</script>
