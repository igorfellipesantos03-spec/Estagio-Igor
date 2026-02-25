@extends('layouts.professor')

@section('title', 'Gerenciar Hackathons - Professor')
@section('header', 'Gerenciar Hackathons')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Seus Eventos</h2>
            <button onclick="document.getElementById('create-hackathon-modal').classList.remove('hidden')" class="text-sm text-principal font-medium hover:underline">
                + Criar Novo
            </button>
        </div>

        @if($hackathons->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-50 mb-4">
                    <i class="fas fa-laptop-code text-2xl text-principal"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-800 mb-2">Nenhum hackathon criado</h3>
                <p class="text-slate-500 mb-6">Comece criando seu primeiro evento para os alunos.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                            <th class="px-6 py-4">Evento</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Período</th>
                            <th class="px-6 py-4">Grupos</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($hackathons as $hackathon)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 overflow-hidden shrink-0">
                                        @if($hackathon->banner)
                                            <img src="{{ asset('storage/' . $hackathon->banner) }}" alt="Banner" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
                                                <i class="fas fa-rocket text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-sm">{{ $hackathon->nome }}</p>
                                        <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $hackathon->descricao }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($hackathon->status === 'finalized')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-trophy text-[10px] mr-1.5"></i>
                                        Finalizado
                                    </span>
                                    @if($hackathon->winnerGroup)
                                        <p class="text-xs text-slate-500 mt-1">
                                            Vencedor: {{ $hackathon->winnerGroup->nome }}
                                        </p>
                                    @endif
                                @elseif(now() < $hackathon->data_inicio)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Em Breve
                                    </span>
                                @elseif(now() > $hackathon->data_fim)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Encerrado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Em Andamento
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-600">
                                    <p><i class="far fa-calendar-alt mr-1 text-gray-400"></i> {{ \Carbon\Carbon::parse($hackathon->data_inicio)->format('d/m/Y H:i') }}</p>
                                    <p class="mt-1"><i class="fas fa-flag-checkered mr-1 text-gray-400"></i> {{ \Carbon\Carbon::parse($hackathon->data_fim)->format('d/m/Y H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    <i class="fas fa-users text-[10px] mr-1.5"></i>
                                    {{ $hackathon->grupos->count() }} grupos
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($hackathon->status !== 'finalized' && $hackathon->grupos->count() > 0)
                                        <button onclick="openFinalizeModal({{ $hackathon->id }}, '{{ $hackathon->nome }}', {{ json_encode($hackathon->grupos->map(fn($g) => ['id' => $g->id, 'nome' => $g->nome])) }})" 
                                                class="text-slate-400 hover:text-purple-600 transition-colors p-2 rounded-full hover:bg-purple-50" 
                                                title="Finalizar Evento">
                                            <i class="fas fa-trophy"></i>
                                        </button>
                                    @endif
                                    <button onclick="openEditModal({{ $hackathon }})" class="text-slate-400 hover:text-blue-600 transition-colors p-2 rounded-full hover:bg-blue-50" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($hackathon->grupos->count() === 0)
                                        <form action="{{ route('hackathons.destroy', $hackathon) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este hackathon?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors p-2 rounded-full hover:bg-red-50" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-300 p-2 cursor-not-allowed" title="Não é possível excluir - há grupos inscritos">
                                            <i class="fas fa-trash-alt"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('modals')
    {{-- Modal de Finalização --}}
    <div id="finalize-hackathon-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-data="{ loading: false }">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeFinalizeModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 px-6 py-5 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trophy text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Finalizar Hackathon</h3>
                                <p id="finalize-hackathon-name" class="text-sm text-white/80"></p>
                            </div>
                        </div>
                        <button onclick="closeFinalizeModal()" class="text-white/70 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="finalize-form" method="POST" @submit="loading = true">
                        @csrf
                        <div class="px-6 py-6 space-y-6">
                            
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                <div class="flex gap-3">
                                    <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                                    <div class="text-sm text-amber-800">
                                        <p class="font-medium">Atenção!</p>
                                        <p class="mt-1">Esta ação é irreversível. Ao finalizar o hackathon:</p>
                                        <ul class="list-disc ml-4 mt-2 space-y-1">
                                            <li>O grupo vencedor receberá <strong>1000 XP</strong></li>
                                            <li>Os demais participantes receberão <strong>200 XP</strong></li>
                                            <li>Todos serão notificados do resultado</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Selecione o Grupo Vencedor</label>
                                <select name="winner_group_id" id="finalize-grupos-select" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                                    <option value="">Escolha um grupo...</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="closeFinalizeModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="loading" class="px-5 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:from-purple-600 hover:to-indigo-700 font-medium flex items-center gap-2 disabled:opacity-50">
                                <i class="fas fa-trophy" x-show="!loading"></i>
                                <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                                <span x-text="loading ? 'Finalizando...' : 'Encerrar e Premiar'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Edição --}}
    <div id="edit-hackathon-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    
                    <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-slate-800">Editar Hackathon</h3>
                        <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="edit-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="px-6 py-6 space-y-6">
                            
                            <div class="flex items-start gap-6">
                                <div class="shrink-0">
                                    <label class="block w-32 h-32 rounded-xl border-2 border-dashed border-gray-300 hover:border-principal bg-gray-50 cursor-pointer flex flex-col items-center justify-center overflow-hidden relative">
                                        <img id="edit-preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                                        <div id="edit-placeholder" class="flex flex-col items-center">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                            <span class="text-xs text-gray-500">Alterar Logo</span>
                                        </div>
                                        <input type="file" name="banner" class="hidden" onchange="previewEditImage(this)">
                                    </label>
                                </div>

                                <div class="flex-1 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Nome do Evento</label>
                                        <input type="text" name="nome" id="edit-nome" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Descrição</label>
                                        <textarea name="descricao" id="edit-descricao" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20 resize-none" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Início</label>
                                    <input type="datetime-local" name="data_inicio" id="edit-data_inicio" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Fim</label>
                                    <input type="datetime-local" name="data_fim" id="edit-data_fim" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                             <button type="button" onclick="setNow()" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-stop-circle mr-1"></i> Finalizar Agora
                            </button>
                            <div class="flex gap-3">
                                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
                                <button type="submit" class="px-4 py-2 bg-principal text-white rounded-lg hover:bg-orange-600">Salvar Alterações</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // Funções do Modal de Finalização
        function openFinalizeModal(hackathonId, hackathonNome, grupos) {
            const modal = document.getElementById('finalize-hackathon-modal');
            const form = document.getElementById('finalize-form');
            const select = document.getElementById('finalize-grupos-select');
            
            // Configurar form action
            form.action = `/dashboard/professor/hackathons/${hackathonId}/finalize`;
            
            // Mostrar nome do hackathon
            document.getElementById('finalize-hackathon-name').textContent = hackathonNome;
            
            // Popular select com grupos
            select.innerHTML = '<option value="">Escolha um grupo...</option>';
            grupos.forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id;
                option.textContent = grupo.nome;
                select.appendChild(option);
            });

            modal.classList.remove('hidden');
        }

        function closeFinalizeModal() {
            document.getElementById('finalize-hackathon-modal').classList.add('hidden');
        }

        // Funções do Modal de Edição
        function openEditModal(hackathon) {
            const modal = document.getElementById('edit-hackathon-modal');
            const form = document.getElementById('edit-form');
            
            // Populate fields
            form.action = `/dashboard/professor/hackathons/${hackathon.id}`;
            document.getElementById('edit-nome').value = hackathon.nome;
            document.getElementById('edit-descricao').value = hackathon.descricao;
            document.getElementById('edit-data_inicio').value = hackathon.data_inicio;
            document.getElementById('edit-data_fim').value = hackathon.data_fim;

            // Handle Banner Preview
            const preview = document.getElementById('edit-preview');
            const placeholder = document.getElementById('edit-placeholder');
            if (hackathon.banner) {
                preview.src = `/storage/${hackathon.banner}`;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-hackathon-modal').classList.add('hidden');
        }

        function previewEditImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('edit-preview').src = e.target.result;
                    document.getElementById('edit-preview').classList.remove('hidden');
                    document.getElementById('edit-placeholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function setNow() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('edit-data_fim').value = now.toISOString().slice(0,16);
        }
    </script>
@endpush
