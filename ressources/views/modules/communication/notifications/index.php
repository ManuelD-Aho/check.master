<?php
/**
 * Vue des notifications
 * 
 * @see PRD 05 - Communication
 */
$pageTitle = 'Notifications';
$breadcrumb = [
    ['label' => 'Communication', 'url' => '/communication'],
    ['label' => 'Notifications', 'active' => true],
];
?>

<div x-data="notificationsModule()" x-init="init()" class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Centre de Notifications</h1>
            <p class="mt-1 text-sm text-gray-600">Gérez vos notifications et alertes</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <button @click="marquerToutLu()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Tout marquer comme lu
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="flex space-x-2 mb-6">
        <button @click="filtreType = ''" :class="filtreType === '' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg shadow">
            Toutes
            <span class="ml-1 bg-gray-100 text-gray-700 px-2 rounded-full text-sm" x-show="filtreType === ''" x-text="statistiques.total || 0"></span>
        </button>
        <button @click="filtreType = 'non_lues'" :class="filtreType === 'non_lues' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg shadow">
            Non lues
            <span x-show="statistiques.non_lues > 0" class="ml-1 bg-red-100 text-red-700 px-2 rounded-full text-sm" x-text="statistiques.non_lues"></span>
        </button>
        <button @click="filtreType = 'importantes'" :class="filtreType === 'importantes' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg shadow">
            Importantes
        </button>
    </div>

    <!-- Liste des notifications -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            <template x-for="notif in notifications" :key="notif.id_notification">
                <div :class="notif.lu ? 'bg-white' : 'bg-blue-50'" class="p-4 hover:bg-gray-50 cursor-pointer transition-colors" @click="ouvrirNotification(notif)">
                    <div class="flex items-start">
                        <!-- Icône selon le type -->
                        <div :class="{
                            'bg-blue-100 text-blue-600': notif.type === 'info',
                            'bg-green-100 text-green-600': notif.type === 'success',
                            'bg-yellow-100 text-yellow-600': notif.type === 'warning',
                            'bg-red-100 text-red-600': notif.type === 'error',
                            'bg-purple-100 text-purple-600': notif.type === 'soutenance',
                            'bg-indigo-100 text-indigo-600': notif.type === 'document',
                            'bg-pink-100 text-pink-600': notif.type === 'finance'
                        }" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
                            <svg x-show="notif.type === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="notif.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="notif.type === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <svg x-show="notif.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="notif.type === 'soutenance'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <svg x-show="notif.type === 'document'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <svg x-show="notif.type === 'finance'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        
                        <!-- Contenu -->
                        <div class="ml-4 flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p :class="notif.lu ? 'text-gray-700' : 'text-gray-900 font-semibold'" class="text-sm" x-text="notif.titre"></p>
                                <span x-show="!notif.lu" class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2" x-text="notif.contenu"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="formatDateRelative(notif.created_at)"></p>
                        </div>
                        
                        <!-- Actions -->
                        <div class="ml-4 flex items-center space-x-2">
                            <button x-show="notif.action_url" @click.stop="allerVers(notif.action_url)" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                            <button @click.stop="supprimerNotification(notif.id_notification)" class="text-gray-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <div x-show="notifications.length === 0" class="p-12 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p>Aucune notification</p>
        </div>
        
        <!-- Pagination -->
        <div x-show="hasMore" class="p-4 border-t border-gray-200 text-center">
            <button @click="chargerPlus()" class="text-blue-600 hover:text-blue-800 font-medium">
                Charger plus de notifications
            </button>
        </div>
    </div>

    <!-- Préférences de notifications -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Préférences de notifications</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-700">Notifications par email</p>
                    <p class="text-sm text-gray-500">Recevoir les notifications importantes par email</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="preferences.email" @change="sauvegarderPreferences()" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-700">Rappels de soutenance</p>
                    <p class="text-sm text-gray-500">Recevoir les rappels J-7, J-1 et jour J</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="preferences.rappels_soutenance" @change="sauvegarderPreferences()" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-700">Alertes financières</p>
                    <p class="text-sm text-gray-500">Notifications de paiement et pénalités</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="preferences.alertes_finance" @change="sauvegarderPreferences()" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>
    </div>
</div>

<script>
function notificationsModule() {
    return {
        notifications: [],
        statistiques: {total: 0, non_lues: 0},
        filtreType: '',
        page: 1,
        hasMore: false,
        preferences: {
            email: true,
            rappels_soutenance: true,
            alertes_finance: true
        },

        async init() {
            await this.chargerNotifications();
            await this.chargerPreferences();
            
            this.$watch('filtreType', () => {
                this.page = 1;
                this.chargerNotifications();
            });
        },

        async chargerNotifications() {
            const params = new URLSearchParams({page: this.page});
            if (this.filtreType === 'non_lues') params.append('non_lues', '1');
            if (this.filtreType === 'importantes') params.append('importantes', '1');
            
            const response = await fetch('/api/notifications?' + params);
            const data = await response.json();
            if (data.success) {
                if (this.page === 1) {
                    this.notifications = data.data;
                } else {
                    this.notifications = [...this.notifications, ...data.data];
                }
                this.hasMore = data.pagination?.has_more || false;
                this.statistiques = data.statistiques || {total: this.notifications.length, non_lues: 0};
            }
        },

        async chargerPlus() {
            this.page++;
            await this.chargerNotifications();
        },

        async chargerPreferences() {
            const response = await fetch('/api/notifications/preferences');
            const data = await response.json();
            if (data.success && data.data) {
                this.preferences = {...this.preferences, ...data.data};
            }
        },

        async ouvrirNotification(notif) {
            if (!notif.lu) {
                const response = await fetch('/api/notifications/' + notif.id_notification + '/lire', {
                    method: 'POST'
                });
                if (response.ok) {
                    notif.lu = true;
                    this.statistiques.non_lues = Math.max(0, this.statistiques.non_lues - 1);
                }
            }
            
            if (notif.action_url) {
                window.location.href = notif.action_url;
            }
        },

        async marquerToutLu() {
            const response = await fetch('/api/notifications/marquer-tout-lu', {
                method: 'POST'
            });
            if (response.ok) {
                this.notifications.forEach(n => n.lu = true);
                this.statistiques.non_lues = 0;
            }
        },

        async supprimerNotification(id) {
            if (!confirm('Supprimer cette notification ?')) return;
            
            const response = await fetch('/api/notifications/' + id, {
                method: 'DELETE'
            });
            if (response.ok) {
                this.notifications = this.notifications.filter(n => n.id_notification !== id);
            }
        },

        async sauvegarderPreferences() {
            const formData = new FormData();
            Object.entries(this.preferences).forEach(([key, value]) => {
                formData.append(key, value ? '1' : '0');
            });
            
            await fetch('/api/notifications/preferences', {
                method: 'POST',
                body: formData
            });
        },

        allerVers(url) {
            window.location.href = url;
        },

        formatDateRelative(date) {
            if (!date) return '';
            const d = new Date(date);
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            
            if (diff < 60) return 'À l\'instant';
            if (diff < 3600) return Math.floor(diff / 60) + ' min';
            if (diff < 86400) return Math.floor(diff / 3600) + ' h';
            if (diff < 604800) return Math.floor(diff / 86400) + ' j';
            
            return d.toLocaleDateString('fr-FR');
        }
    }
}
</script>
