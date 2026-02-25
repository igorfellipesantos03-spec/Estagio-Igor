@props(['position' => 'right'])

<div 
    x-data="notificationBell()"
    x-init="init()"
    class="relative"
>
    {{-- Botão do Sino --}}
    <button
        @click="toggle()"
        class="relative p-2 text-slate-400 hover:text-unifil-orange transition-colors rounded-xl hover:bg-slate-100"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        {{-- Badge de Notificações não lidas --}}
        <template x-if="unreadCount > 0">
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                <span 
                    class="relative inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
                    x-text="unreadCount > 9 ? '9+' : unreadCount"
                ></span>
            </span>
        </template>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        @click.outside="open = false"
        class="absolute {{ $position === 'right' ? 'right-0' : 'left-0' }} mt-2 w-96 max-h-[32rem] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-unifil-blue to-slate-700 px-4 py-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Notificações
                </h3>
                <button 
                    @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    class="text-xs text-white/70 hover:text-white transition-colors"
                >
                    Marcar todas como lidas
                </button>
            </div>
        </div>

        {{-- Abas de Filtro --}}
        <div class="flex border-b border-gray-100 bg-gray-50">
            <template x-for="tab in tabs" :key="tab.key">
                <button
                    @click="activeTab = tab.key"
                    :class="activeTab === tab.key 
                        ? 'text-unifil-orange border-b-2 border-unifil-orange bg-white' 
                        : 'text-slate-500 hover:text-slate-700 border-b-2 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-sm font-medium transition-all"
                    x-text="tab.label"
                ></button>
            </template>
        </div>

        {{-- Lista de Notificações --}}
        <div class="max-h-80 overflow-y-auto">
            <template x-if="loading">
                <div class="flex items-center justify-center py-12">
                    <svg class="animate-spin h-8 w-8 text-unifil-orange" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </template>

            <template x-if="!loading && filteredNotifications.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm">Nenhuma notificação</p>
                </div>
            </template>

            <template x-if="!loading && filteredNotifications.length > 0">
                <div class="divide-y divide-gray-100">
                    <template x-for="notification in filteredNotifications" :key="notification.id + notification.type">
                        <div
                            :class="{
                                'bg-red-50 border-l-4 border-red-500': notification.level === 'danger',
                                'bg-amber-50 border-l-4 border-amber-500': notification.level === 'warning',
                                'bg-green-50 border-l-4 border-green-500': notification.level === 'success' && !notification.is_read,
                                'bg-slate-50 border-l-4 border-slate-300': notification.category === 'general' && notification.level === 'info',
                                'bg-white': notification.is_read && notification.level !== 'danger',
                                'opacity-60': notification.is_read
                            }"
                            class="p-4 hover:bg-gray-50/80 transition-colors relative group"
                        >
                            <div class="flex gap-3">
                                {{-- Ícone --}}
                                <div 
                                    :class="{
                                        'bg-red-100 text-red-600': notification.level === 'danger',
                                        'bg-amber-100 text-amber-600': notification.level === 'warning',
                                        'bg-green-100 text-green-600': notification.level === 'success',
                                        'bg-blue-100 text-blue-600': notification.level === 'info',
                                    }"
                                    class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
                                >
                                    {{-- Ícone dinâmico baseado no tipo --}}
                                    <template x-if="notification.icon === 'check-circle'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="notification.icon === 'exclamation-triangle'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="notification.icon === 'star'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                    <template x-if="notification.icon === 'sparkles'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="notification.icon === 'photograph'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="notification.icon === 'megaphone'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="!['check-circle','exclamation-triangle','star','sparkles','photograph','megaphone'].includes(notification.icon)">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                        </svg>
                                    </template>
                                </div>

                                {{-- Conteúdo --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p 
                                            class="text-sm font-semibold truncate"
                                            :class="notification.level === 'danger' ? 'text-red-800' : 'text-slate-800'"
                                            x-text="notification.title"
                                        ></p>
                                        
                                        {{-- Botão de marcar como lida --}}
                                        <button 
                                            x-show="!notification.is_read"
                                            @click.stop="markAsRead(notification)"
                                            class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-slate-600 transition-all"
                                            title="Marcar como lida"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <p 
                                        class="text-xs mt-1 line-clamp-2"
                                        :class="notification.level === 'danger' ? 'text-red-700' : 'text-slate-600'"
                                        x-text="notification.message"
                                    ></p>
                                    
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-[10px] text-slate-400" x-text="formatTime(notification.created_at)"></span>
                                        
                                        {{-- Badge de Categoria --}}
                                        <span 
                                            :class="{
                                                'bg-blue-100 text-blue-700': notification.category === 'general',
                                                'bg-green-100 text-green-700': notification.category === 'individual',
                                            }"
                                            class="text-[10px] px-2 py-0.5 rounded-full font-medium"
                                            x-text="getCategoryLabel(notification.category)"
                                        ></span>

                                        {{-- Badge Urgente --}}
                                        <template x-if="notification.is_urgent">
                                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-red-500 text-white animate-pulse">
                                                URGENTE
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Botão de Ação --}}
                                    <template x-if="notification.action_url">
                                        <a 
                                            :href="notification.action_url"
                                            class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-unifil-orange hover:text-orange-600 transition-colors"
                                        >
                                            Ver detalhes
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
            <a 
                href="#"
                class="text-sm text-unifil-orange hover:text-orange-600 font-medium flex items-center justify-center gap-1"
            >
                Ver todas as notificações
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        loading: true,
        notifications: [],
        unreadCount: 0,
        activeTab: 'all',
        tabs: [
            { key: 'all', label: 'Tudo' },
            { key: 'general', label: 'Geral' },
            { key: 'individual', label: 'Pessoal' },
        ],

        async init() {
            await this.fetchNotifications();
            // Atualizar a cada 30 segundos
            setInterval(() => this.fetchUnreadCount(), 30000);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.fetchNotifications();
            }
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/api/notifications');
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            } catch (error) {
                console.error('Erro ao carregar notificações:', error);
            }
            this.loading = false;
        },

        async fetchUnreadCount() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                const data = await response.json();
                this.unreadCount = data.unread_count || 0;
            } catch (error) {
                console.error('Erro ao buscar contagem:', error);
            }
        },

        async markAsRead(notification) {
            try {
                const response = await fetch('/api/notifications/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id: notification.id
                    })
                });
                const data = await response.json();
                if (data.success) {
                    notification.is_read = true;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Erro ao marcar como lida:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Erro ao marcar todas como lidas:', error);
            }
        },

        get filteredNotifications() {
            if (this.activeTab === 'all') {
                return this.notifications;
            }
            return this.notifications.filter(n => n.category === this.activeTab);
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Agora';
            if (diffMins < 60) return `${diffMins}min atrás`;
            if (diffHours < 24) return `${diffHours}h atrás`;
            if (diffDays < 7) return `${diffDays}d atrás`;
            
            return date.toLocaleDateString('pt-BR');
        },

        getCategoryLabel(category) {
            const labels = {
                'general': 'Geral',
                'individual': 'Pessoal'
            };
            return labels[category] || 'Pessoal'; // Fallback mapping older db records to Pessoal
        }
    }
}
</script>
