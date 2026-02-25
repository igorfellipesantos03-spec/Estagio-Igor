@extends('layouts.aluno')

@section('title', 'Validar Presença - SimplifiKathon')

@section('content')
<div class="flex-1 overflow-y-auto p-6 lg:p-10">
    <div class="max-w-3xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Validar Presença</h1>
            <p class="text-slate-500">Envie uma foto comprovando sua presença no hackathon.</p>
        </div>

        {{-- Alertas --}}
        @if (session('success'))
            <div class="flex items-center p-4 mb-6 text-sm text-green-800 border border-green-200 rounded-lg bg-green-50" role="alert">
                <i class="fas fa-check-circle text-lg mr-3"></i>
                <div>
                    <span class="font-bold">Sucesso!</span> {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex p-4 mb-6 text-sm text-red-800 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="fas fa-exclamation-circle text-lg mr-3 mt-0.5"></i>
                <div>
                    <span class="font-bold block mb-1">Por favor, corrija os seguintes erros:</span>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Card do Formulário --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-principal to-orange-400 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-camera"></i>
                    Enviar Foto de Presença
                </h2>
            </div>

            <form 
                action="{{ route('aluno.presenca.store') }}" 
                method="POST" 
                enctype="multipart/form-data"
                class="p-6 space-y-6"
                x-data="imagePreview()"
            >
                @csrf

                {{-- Select Hackathon --}}
                <div>
                    <label for="hackathon_id" class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-laptop-code text-principal mr-1"></i>
                        Selecione o Hackathon
                    </label>
                    <select 
                        name="hackathon_id" 
                        id="hackathon_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-principal focus:border-principal transition-all bg-gray-50 hover:bg-white"
                    >
                        <option value="">-- Escolha um hackathon --</option>
                        @forelse ($hackathons as $hackathon)
                            @php
                                $registro = $presencas->get($hackathon->id);
                                $podeEnviar = !$registro || $registro->status->value === 'rejected';
                            @endphp
                            @if ($podeEnviar)
                                <option value="{{ $hackathon->id }}" {{ old('hackathon_id') == $hackathon->id ? 'selected' : '' }}>
                                    {{ $hackathon->nome }} {{ $registro && $registro->status->value === 'rejected' ? '(Reenvio)' : '' }}
                                </option>
                            @endif
                        @empty
                            <option value="" disabled>Nenhum hackathon disponível</option>
                        @endforelse
                    </select>
                    @if (count($hackathons) > 0 && count($hackathons) === $presencas->whereIn('status.value', ['pending', 'approved'])->count())
                        <p class="mt-2 text-sm text-amber-600 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            Você já enviou presença para todos os hackathons disponíveis.
                        </p>
                    @endif
                </div>

                {{-- Upload de Foto com Preview --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-image text-principal mr-1"></i>
                        Foto de Comprovação
                    </label>
                    
                    <div class="relative">
                        {{-- Área de Drop/Click --}}
                        <label 
                            for="photo"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-principal transition-all"
                            :class="{ 'border-principal bg-orange-50': hasImage }"
                        >
                            {{-- Preview da Imagem --}}
                            <template x-if="hasImage">
                                <div class="relative w-full h-full p-4">
                                    <img 
                                        :src="imageUrl" 
                                        alt="Preview" 
                                        class="w-full h-full object-contain rounded-lg"
                                    >
                                    <button 
                                        type="button"
                                        @click.prevent="removeImage()"
                                        class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition-colors"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>

                            {{-- Placeholder --}}
                            <template x-if="!hasImage">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-principal/10 flex items-center justify-center">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-principal"></i>
                                    </div>
                                    <p class="mb-2 text-sm text-slate-600">
                                        <span class="font-semibold">Clique para selecionar</span> ou arraste uma imagem
                                    </p>
                                    <p class="text-xs text-slate-400">PNG, JPG ou JPEG (máx. 5MB)</p>
                                </div>
                            </template>

                            <input 
                                type="file" 
                                name="photo" 
                                id="photo"
                                accept="image/*"
                                class="hidden"
                                @change="previewImage($event)"
                                required
                            >
                        </label>
                    </div>
                </div>

                {{-- Botão Submit --}}
                <div class="pt-4">
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-principal to-orange-500 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl hover:from-orange-600 hover:to-orange-400 transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-paper-plane"></i>
                        Enviar Comprovante de Presença
                    </button>
                </div>
            </form>
        </div>

        {{-- Histórico de Presenças Enviadas --}}
        @php
            $minhasPresencas = \App\Models\AttendanceRecord::where('user_id', $user->id)
                ->with('hackathon')
                ->latest()
                ->get();
        @endphp

        @if ($minhasPresencas->count() > 0)
            <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-slate-800 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-history"></i>
                        Minhas Presenças
                    </h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @foreach ($minhasPresencas as $presenca)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-laptop-code text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $presenca->hackathon->nome }}</p>
                                    <p class="text-sm text-slate-400">Enviado em {{ $presenca->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <div>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colors[$presenca->status->value] ?? 'bg-gray-100' }}">
                                        {{ $presenca->status->label() }}
                                    </span>
                                </div>
                                @if($presenca->status->value === 'rejected' && $presenca->admin_note)
                                    <p class="mt-2 text-xs text-red-600 max-w-xs ml-auto">
                                        <span class="font-bold">Motivo:</span> {{ $presenca->admin_note }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function imagePreview() {
        return {
            hasImage: false,
            imageUrl: '',
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.hasImage = true;
                    this.imageUrl = URL.createObjectURL(file);
                }
            },
            removeImage() {
                this.hasImage = false;
                this.imageUrl = '';
                document.getElementById('photo').value = '';
            }
        }
    }
</script>
@endpush
