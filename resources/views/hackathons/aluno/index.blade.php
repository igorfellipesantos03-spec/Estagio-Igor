@extends('layouts.aluno')

@section('title', 'Hackathons Disponíveis - Aluno')

@section('content')
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20 px-6 py-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Hackathons Disponíveis</h1>
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.aluno') }}" class="text-sm text-principal hover:text-orange-600 font-medium flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Voltar ao Dashboard
            </a>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-6 lg:p-10 scroll-smooth">
        <div class="max-w-7xl mx-auto">
            
            @if($hackathons->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                    <div class="bg-blue-50 p-4 rounded-full mb-4">
                        <i class="fas fa-calendar-times text-4xl text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Sem eventos no momento</h3>
                    <p class="text-slate-500 mb-6 max-w-md mx-auto">Não há hackathons abertos para inscrição agora. Fique de olho, novidades em breve!</p>
                    <a href="{{ route('dashboard.aluno') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-slate-700 font-semibold rounded-lg shadow-sm transition-all">
                        Voltar
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($hackathons as $hackathon)
                    @php
                        $isParticipating = $user->grupos->contains('hackathon_id', $hackathon->id);
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border {{ $isParticipating ? 'border-green-400 ring-1 ring-green-400' : 'border-gray-100' }} overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full relative">
                        
                        {{-- Banner do Card --}}
                        <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white relative overflow-hidden">
                            @if($hackathon->banner)
                                <img src="{{ asset('storage/' . $hackathon->banner) }}" alt="Banner" class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-rocket text-4xl transform group-hover:scale-110 transition-transform duration-500"></i>
                            @endif
                            <div class="absolute top-3 right-3 flex gap-2">
                                @if($isParticipating)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm">
                                        <i class="fas fa-check mr-1"></i> Participando
                                    </span>
                                @endif

                                @php
                                    $inscricaoLimite = \Carbon\Carbon::parse($hackathon->data_inicio)->subDay();
                                    $isClosed = now() > $inscricaoLimite;
                                @endphp

                                @if($hackathon->status === 'finalized')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-500 text-white shadow-sm">
                                        <i class="fas fa-trophy mr-1"></i> Finalizado
                                    </span>
                                @elseif($isClosed && now() < $hackathon->data_inicio)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm">
                                        Fechado para Inscrições
                                    </span>
                                @elseif(now() < $hackathon->data_inicio)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-400 text-white shadow-sm">
                                        Em Breve
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-sm animate-pulse">
                                        Em Andamento
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="p-6 flex-1 flex flex-col">
                            <h4 class="font-bold text-lg text-slate-800 mb-2 line-clamp-1" title="{{ $hackathon->nome }}">{{ $hackathon->nome }}</h4>
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2 flex-1">{{ $hackathon->descricao }}</p>
                            
                            <div class="space-y-2 mb-6 border-t border-gray-100 pt-4">
                                <div class="flex items-center text-xs text-slate-500">
                                    <i class="far fa-calendar-alt w-5 text-center mr-2 text-blue-500"></i>
                                    <span>Início: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($hackathon->data_inicio)->format('d/m/Y H:i') }}</span></span>
                                </div>
                                <div class="flex items-center text-xs text-slate-500">
                                    <i class="fas fa-flag-checkered w-5 text-center mr-2 text-red-500"></i>
                                    <span>Fim: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($hackathon->data_fim)->format('d/m/Y H:i') }}</span></span>
                                </div>
                            </div>

                            @if($isParticipating)
                                <button disabled class="w-full mt-auto bg-green-100 text-green-700 font-semibold py-2.5 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Inscrito</span>
                                </button>
                            @elseif($hackathon->status === 'finalized' || $isClosed)
                                <button disabled class="w-full mt-auto bg-gray-100 text-gray-400 font-semibold py-2.5 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-lock text-xs"></i>
                                    <span>Inscrições Encerradas</span>
                                </button>
                            @else
                                <button onclick="openSubscribeModal({{ $hackathon }})" class="w-full mt-auto bg-principal hover:bg-orange-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow">
                                    <span>Inscrever-se</span>
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Inscrição com Lógica de Grupo --}}
    <div id="subscribe-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeSubscribeModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    {{-- Header --}}
                    <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-slate-800" id="modal-title">Inscrição no Hackathon</h3>
                        <button onclick="closeSubscribeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6">
                        {{-- Passo 1: Pergunta Inicial --}}
                        <div id="step-1">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Você já possui um grupo?</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <button onclick="showStep('step-join')" class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl hover:border-principal hover:bg-orange-50 transition-all group">
                                    <i class="fas fa-users text-3xl text-gray-400 group-hover:text-principal mb-2"></i>
                                    <span class="font-semibold text-gray-700 group-hover:text-principal">Sim, tenho um grupo</span>
                                </button>
                                <button onclick="showStep('step-create')" class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl hover:border-principal hover:bg-orange-50 transition-all group">
                                    <i class="fas fa-plus-circle text-3xl text-gray-400 group-hover:text-principal mb-2"></i>
                                    <span class="font-semibold text-gray-700 group-hover:text-principal">Não, quero criar</span>
                                </button>
                            </div>
                        </div>

                        {{-- Passo 2: Criar Grupo --}}
                        <div id="step-create" class="hidden">
                            <div class="flex items-center mb-4 text-sm text-gray-500 cursor-pointer hover:text-principal" onclick="showStep('step-1')">
                                <i class="fas fa-arrow-left mr-2"></i> Voltar
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Criar Novo Grupo</h4>
                            <form action="{{ route('grupos.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="hackathon_id" id="create_hackathon_id">
                                
                                <div class="mb-4">
                                    <label for="nome_grupo" class="block text-sm font-medium text-gray-700 mb-1">Nome do Grupo</label>
                                    <input type="text" name="nome" id="nome_grupo" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20" placeholder="Ex: Rocket Team" required>
                                </div>

                                <button type="submit" class="w-full bg-principal hover:bg-orange-600 text-white font-bold py-2.5 rounded-lg transition-colors">
                                    Criar Grupo e Inscrever-se
                                </button>
                            </form>
                        </div>

                        {{-- Passo 3: Entrar em Grupo --}}
                        <div id="step-join" class="hidden">
                            <div class="flex items-center mb-4 text-sm text-gray-500 cursor-pointer hover:text-principal" onclick="showStep('step-1')">
                                <i class="fas fa-arrow-left mr-2"></i> Voltar
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Entrar em Grupo Existente</h4>
                            <form action="{{ route('grupos.join') }}" method="POST">
                                @csrf
                                <input type="hidden" name="hackathon_id" id="join_hackathon_id"> {{-- Apenas para referência se necessário --}}
                                
                                <div class="mb-4">
                                    <label for="codigo_grupo" class="block text-sm font-medium text-gray-700 mb-1">Código do Grupo</label>
                                    <input type="text" name="codigo" id="codigo_grupo" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20 uppercase" placeholder="Ex: A1B2C3" required>
                                    <p class="text-xs text-gray-500 mt-1">Peça o código ao líder do seu grupo.</p>
                                </div>

                                <button type="submit" class="w-full bg-principal hover:bg-orange-600 text-white font-bold py-2.5 rounded-lg transition-colors">
                                    Entrar no Grupo
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openSubscribeModal(hackathon) {
            const modal = document.getElementById('subscribe-modal');
            document.getElementById('create_hackathon_id').value = hackathon.id;
            // document.getElementById('join_hackathon_id').value = hackathon.id; // Se precisar validar no front
            
            // Resetar para o passo 1
            showStep('step-1');
            
            modal.classList.remove('hidden');
        }

        function closeSubscribeModal() {
            document.getElementById('subscribe-modal').classList.add('hidden');
        }

        function showStep(stepId) {
            ['step-1', 'step-create', 'step-join'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            document.getElementById(stepId).classList.remove('hidden');
        }
    </script>
    @endpush
@endsection