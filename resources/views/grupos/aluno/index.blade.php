@extends('layouts.aluno')

@section('title', 'Meus Grupos - SimplifiKathon')

@section('content')
<div class="flex-1 overflow-y-auto p-6 lg:p-10">
    <div class="max-w-5xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Meus Grupos</h1>
            <p class="text-slate-500">Gerencie sua participação nos hackathons e conecte-se com sua equipe.</p>
        </div>

        @if($grupos->isEmpty())
            {{-- Estado Vazio --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-principal to-orange-400 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Meus Grupos
                    </h2>
                </div>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Você não participa de nenhum grupo</h3>
                    <p class="text-slate-500 mb-6 max-w-md mx-auto">Inscreva-se em um Hackathon para criar ou entrar em um grupo.</p>
                    <a href="{{ route('aluno.hackathons.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-principal to-orange-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-orange-600 hover:to-orange-400 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Ver Hackathons
                    </a>
                </div>
            </div>
        @else
            {{-- Grid de Grupos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($grupos as $grupo)
                    @php
                        $isLeader = $grupo->lider_id == Auth::id();
                        $members = $grupo->membros;
                        $maxAvatars = 4;
                    @endphp
                    
                    <div 
                        @click="$dispatch('open-group-modal', { 
                            group: {{ $grupo->load(['hackathon', 'lider', 'membros'])->toJson() }},
                            isLeader: {{ $isLeader ? 'true' : 'false' }}
                        })"
                        class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden cursor-pointer hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                    >
                        {{-- Header com gradiente --}}
                        <div class="bg-gradient-to-r from-principal to-orange-400 px-5 py-4 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative z-10 flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    {{-- Imagem ou Iniciais --}}
                                    <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-sm backdrop-blur-sm overflow-hidden">
                                        @if ($grupo->imagem)
                                            <img src="{{ asset('storage/' . $grupo->imagem) }}" alt="{{ $grupo->nome }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($grupo->nome, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-white truncate max-w-[150px]">{{ $grupo->nome }}</h3>
                                        <p class="text-xs text-white/70 truncate">{{ $grupo->hackathon->nome }}</p>
                                    </div>
                                </div>
                                @if ($isLeader)
                                    <span class="px-2.5 py-1 bg-white/20 text-white text-xs font-bold rounded-full backdrop-blur-sm flex items-center gap-1 flex-shrink-0">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Líder
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Corpo --}}
                        <div class="p-5">
                            {{-- Código do Grupo --}}
                            <div class="mb-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Código do Grupo</p>
                                <p class="text-lg font-mono font-bold text-slate-800 flex items-center gap-1">
                                    <span class="text-primary">#</span>{{ $grupo->codigo }}
                                </p>
                            </div>

                            {{-- Avatar Stack dos Membros --}}
                            <div class="flex items-center justify-between">
                                <div class="flex -space-x-2">
                                    @foreach ($members->take($maxAvatars) as $member)
                                        <div class="relative h-9 w-9 rounded-full ring-2 ring-white bg-gradient-to-br from-primary to-orange-400 flex items-center justify-center text-white text-xs font-bold overflow-hidden transition-transform hover:scale-110 hover:z-10" title="{{ $member->name }}">
                                            @if ($member->avatar)
                                                <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            @endif
                                            @if ($member->id === $grupo->lider_id)
                                                <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-yellow-400 rounded-full flex items-center justify-center ring-2 ring-white">
                                                    <svg class="w-2 h-2 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    
                                    @if ($members->count() > $maxAvatars)
                                        <div class="h-9 w-9 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center text-slate-600 text-xs font-bold">
                                            +{{ $members->count() - $maxAvatars }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 text-sm text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ $members->count() }} membro{{ $members->count() !== 1 ? 's' : '' }}
                                </div>
                            </div>

                            {{-- Botão de Ação --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-center gap-2 text-primary font-medium text-sm group-hover:text-orange-600 transition-colors">
                                    Ver detalhes
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

{{-- Modal de Detalhes do Grupo --}}
<div
    x-data="{
        open: false,
        group: null,
        isLeader: false,
        isEditing: false,
        editedName: '',
        copied: false,
        showDeleteConfirm: false,
        memberToRemove: null,
        showImageUpload: false,
        
        openModal(data) {
            this.group = data.group;
            this.isLeader = data.isLeader;
            this.editedName = this.group.nome;
            this.isEditing = false;
            this.showDeleteConfirm = false;
            this.memberToRemove = null;
            this.showImageUpload = false;
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        
        closeModal() {
            this.open = false;
            this.isEditing = false;
            this.showDeleteConfirm = false;
            this.showImageUpload = false;
            document.body.classList.remove('overflow-hidden');
        },
        
        startEditing() {
            this.editedName = this.group.nome;
            this.isEditing = true;
            this.$nextTick(() => this.$refs.nameInput?.focus());
        },
        
        copyCode() {
            navigator.clipboard.writeText(this.group.codigo);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },
        
        isLeaderMember(memberId) {
            return this.group && this.group.lider_id === memberId;
        },
        
        confirmRemoveMember(member) {
            this.memberToRemove = member;
        },
        
        cancelRemoveMember() {
            this.memberToRemove = null;
        }
    }"
    @open-group-modal.window="openModal($event.detail)"
    @keydown.escape.window="closeModal()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
    style="display: none;"
>
    {{-- Overlay --}}
    <div 
        x-show="open"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeModal()"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
    ></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6">
        <div class="flex min-h-full items-center justify-center">
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                @click.stop
                class="relative w-full max-w-lg"
            >
                {{-- Card Branco --}}
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                    
                    {{-- Header Gradiente --}}
                    <div class="bg-gradient-to-r from-principal to-orange-400 px-6 py-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                {{-- Avatar do Grupo --}}
                                <div class="h-14 w-14 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg backdrop-blur-sm flex-shrink-0 overflow-hidden relative group/avatar">
                                    <template x-if="group?.imagem">
                                        <img :src="'/storage/' + group.imagem" :alt="group.nome" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!group?.imagem">
                                        <span x-text="group?.nome?.substring(0, 2).toUpperCase()"></span>
                                    </template>
                                    
                                    {{-- Botão de alterar imagem (só líder) --}}
                                    <template x-if="isLeader">
                                        <button 
                                            @click.stop="showImageUpload = true"
                                            class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-opacity"
                                        >
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                
                                {{-- Nome (Editável) --}}
                                <div class="flex-1 min-w-0">
                                    <template x-if="!isEditing">
                                        <div class="flex items-center gap-2">
                                            <h2 class="text-xl font-bold text-white truncate" x-text="group?.nome"></h2>
                                            <template x-if="isLeader">
                                                <button 
                                                    @click="startEditing()"
                                                    class="p-1.5 text-white/60 hover:text-white hover:bg-white/20 rounded-lg transition-colors"
                                                    title="Editar nome"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <form method="POST" :action="'/dashboard/aluno/grupos/' + group.id" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input 
                                                type="text"
                                                name="nome"
                                                x-model="editedName"
                                                x-ref="nameInput"
                                                class="flex-1 px-3 py-1.5 bg-white/20 border border-white/30 rounded-lg text-white text-lg font-bold placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50"
                                            >
                                            <button 
                                                type="submit"
                                                class="p-1.5 text-white hover:bg-white/20 rounded-lg transition-colors"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button 
                                                type="button"
                                                @click="isEditing = false"
                                                class="p-1.5 text-white hover:bg-white/20 rounded-lg transition-colors"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </template>
                                    <p class="text-sm text-white/70 mt-0.5" x-text="group?.hackathon?.nome"></p>
                                </div>
                            </div>

                            <button 
                                @click="closeModal()"
                                class="p-2 text-white/60 hover:text-white hover:bg-white/20 rounded-xl transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Corpo --}}
                    <div class="p-6 space-y-6">
                        
                        {{-- Upload de Imagem (Modal interno) --}}
                        <template x-if="showImageUpload && isLeader">
                            <div class="p-4 bg-orange-50 rounded-xl border border-orange-200">
                                <p class="text-sm font-medium text-slate-700 mb-3">Alterar imagem do grupo</p>
                                <form method="POST" :action="'/dashboard/aluno/grupos/' + group.id" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="imagem" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-orange-600 cursor-pointer">
                                    <div class="flex gap-2 mt-3">
                                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">
                                            Salvar
                                        </button>
                                        <button type="button" @click="showImageUpload = false" class="px-4 py-2 bg-gray-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </template>

                        {{-- Código do Grupo --}}
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Código do Grupo</p>
                                    <p class="text-2xl font-mono font-bold text-slate-800 flex items-center gap-1">
                                        <span class="text-primary">#</span>
                                        <span x-text="group?.codigo"></span>
                                    </p>
                                </div>
                                <button 
                                    @click="copyCode()"
                                    class="p-3 rounded-xl transition-all"
                                    :class="copied ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-slate-500 hover:bg-gray-200 hover:text-slate-700'"
                                >
                                    <template x-if="!copied">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </template>
                                    <template x-if="copied">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">Compartilhe este código para outros membros entrarem no grupo</p>
                        </div>

                        {{-- Lista de Membros --}}
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Membros do Grupo
                            </p>
                            
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <template x-for="member in group?.membros" :key="member.id">
                                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:bg-gray-100 transition-all">
                                        <div class="relative h-11 w-11 rounded-full bg-gradient-to-br from-primary to-orange-400 flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">
                                            <template x-if="member.avatar">
                                                <img :src="'/storage/' + member.avatar" :alt="member.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!member.avatar">
                                                <span x-text="member.name.charAt(0).toUpperCase()"></span>
                                            </template>
                                            <template x-if="isLeaderMember(member.id)">
                                                <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center ring-2 ring-white">
                                                    <svg class="w-2.5 h-2.5 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-800 truncate" x-text="member.name"></p>
                                            <p class="text-sm text-slate-500 truncate" x-text="member.email"></p>
                                        </div>

                                        {{-- Badge de Líder ou Botão Expulsar --}}
                                        <template x-if="isLeaderMember(member.id)">
                                            <span class="px-3 py-1 bg-gradient-to-r from-primary to-orange-400 text-white text-xs font-bold rounded-full flex-shrink-0">
                                                Líder
                                            </span>
                                        </template>
                                        
                                        <template x-if="isLeader && !isLeaderMember(member.id)">
                                            <div class="flex-shrink-0">
                                                <template x-if="memberToRemove?.id !== member.id">
                                                    <button 
                                                        @click.stop="confirmRemoveMember(member)"
                                                        class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors"
                                                        title="Remover do grupo"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                                        </svg>
                                                    </button>
                                                </template>
                                                <template x-if="memberToRemove?.id === member.id">
                                                    <div class="flex items-center gap-1">
                                                        <form method="POST" :action="'/dashboard/aluno/grupos/' + group.id + '/membros/' + member.id">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors" title="Confirmar">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <button @click.stop="cancelRemoveMember()" class="p-2 bg-gray-200 text-slate-600 rounded-lg hover:bg-gray-300 transition-colors" title="Cancelar">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Footer com Ações --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <template x-if="!showDeleteConfirm">
                            <div class="flex justify-between">
                                {{-- Sair do grupo (se não for líder) --}}
                                <template x-if="!isLeader">
                                    <form method="POST" :action="'/dashboard/aluno/grupos/' + group.id + '/leave'">
                                        @csrf
                                        <button type="submit" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-xl transition-all flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Sair do grupo
                                        </button>
                                    </form>
                                </template>
                                
                                {{-- Deletar grupo (se for líder) --}}
                                <template x-if="isLeader">
                                    <button 
                                        @click="showDeleteConfirm = true"
                                        class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-xl transition-all flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Excluir grupo
                                    </button>
                                </template>
                                
                                <button 
                                    @click="closeModal()"
                                    class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-slate-700 font-medium rounded-xl transition-all"
                                >
                                    Fechar
                                </button>
                            </div>
                        </template>
                        
                        {{-- Confirmação de Exclusão --}}
                        <template x-if="showDeleteConfirm">
                            <div class="text-center">
                                <p class="text-red-600 font-medium mb-4">Tem certeza que deseja excluir este grupo? Esta ação não pode ser desfeita.</p>
                                <div class="flex justify-center gap-3">
                                    <form method="POST" :action="'/dashboard/aluno/grupos/' + group.id">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-all">
                                            Sim, excluir
                                        </button>
                                    </form>
                                    <button 
                                        @click="showDeleteConfirm = false"
                                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-slate-700 font-medium rounded-xl transition-all"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
