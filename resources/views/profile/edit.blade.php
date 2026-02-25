@extends(auth()->user()->role === 'professor' ? 'layouts.professor' : 'layouts.aluno')

@section('title', 'Meu Perfil - SimplifiKathon')
@section('header', 'Meu Perfil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Page Profile -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
        <div class="h-32 bg-gradient-to-r from-secondary-900 to-primary-600"></div>
        <div class="px-6 sm:px-10 pb-8 content-between flex flex-col sm:flex-row gap-6 sm:gap-10 relative">
            <div class="-mt-16 w-32 h-32 flex-shrink-0 relative">
                <div class="w-full h-full rounded-2xl border-4 border-white shadow-lg bg-white overflow-hidden flex items-center justify-center text-4xl font-bold font-sans text-white bg-gradient-to-br from-primary to-orange-600">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar Atual" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
            </div>
            <div class="flex-1 pt-2 sm:pt-4">
                <h2 class="text-3xl font-bold text-slate-800">{{ $user->name }}</h2>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary uppercase tracking-wider">
                        {{ $user->role === 'professor' ? 'Professor' : 'Aluno' }}
                    </span>
                    <span class="text-slate-500 text-sm flex items-center gap-1">
                        <i class="fas fa-envelope text-slate-400"></i> {{ $user->email }}
                    </span>
                    <span class="text-slate-500 text-sm flex items-center gap-1">
                        <i class="fas fa-calendar-alt text-slate-400"></i> Membro desde {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center gap-3 animate-fade-in-down" role="alert">
            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Avatar Update Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-camera text-primary"></i> Foto de Perfil
                </h3>
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center justify-center w-full mb-4">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors group relative overflow-hidden">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4" id="upload-content">
                                <svg class="w-8 h-8 mb-4 text-gray-500 group-hover:text-primary transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold text-primary">Clique</span> ou arraste</p>
                                <p class="text-xs text-gray-500">PNG, JPG ou GIF (Max. 2MB)</p>
                            </div>
                            <img id="avatar-preview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl" />
                            <input id="dropzone-file" type="file" name="avatar" class="hidden" accept="image/*" onchange="previewImage(event)" />
                        </label>
                    </div>
                    @error('avatar')
                        <p class="text-red-500 text-xs mt-1 mb-3">{{ $message }}</p>
                    @enderror
                    
                    <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                        <i class="fas fa-save mr-2"></i> Salvar Foto
                    </button>
                    <p class="text-xs text-center text-slate-500 mt-3">A foto antiga será substituída automaticamente.</p>
                </form>
            </div>
        </div>

        <!-- Password Update Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
                <div class="mb-6 border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-lock text-secondary"></i> Alterar Senha
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">Mantenha sua conta segura atualizando sua senha regularmente.</p>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">Senha Atual <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-slate-400"></i>
                            </div>
                            <input type="password" name="current_password" id="current_password" 
                                class="pl-10 shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-xl py-2.5 @error('current_password') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="Digite sua senha atual" required>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Nova Senha <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-slate-400"></i>
                                </div>
                                <input type="password" name="password" id="password" 
                                    class="pl-10 shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-xl py-2.5 @error('password') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                    placeholder="Nova senha (mín. 8 caracteres)" required minlength="8">
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmar Nova Senha <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-slate-400"></i>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="pl-10 shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-xl py-2.5" 
                                    placeholder="Confirme a nova senha" required minlength="8">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-secondary hover:bg-secondary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i> Atualizar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const reader = new FileReader();
        const contentDiv = document.getElementById('upload-content');
        const previewImg = document.getElementById('avatar-preview');

        if (input.files && input.files[0]) {
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                // Optar por deixar o overlay semi-transparente ou esconder ícones não é estritamente necessário 
                // para avatar-preview absoluto no dropzone (ele vai sobrepor a borda pontilhada).
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
