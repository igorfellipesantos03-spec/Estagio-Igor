<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - SimplifiKathon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    
    {{-- Configuração do Tailwind --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        principal: '#f58220',
                        sidebar: '#1e293b',
                        'sidebar-hover': '#334155',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 text-slate-800 font-sans h-screen flex overflow-hidden">

    {{-- Toast Global --}}
    <x-toast />

    {{-- Barra Lateral do ADM --}}
    <aside class="w-72 bg-sidebar text-slate-200 flex flex-col shadow-lg flex-shrink-0 transition-all duration-300">
        <div class="flex items-center justify-center p-6 border-b border-slate-700">
            <img src="{{ asset('image/Simplifi(K)athon.png') }}" alt="SimplifiKathon" class="h-12 w-auto">
        </div>

        <div class="px-6 py-6 border-b border-slate-700">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xl ring-2 ring-offset-2 ring-offset-sidebar ring-red-600 shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-white truncate max-w-[140px]" title="{{ $user->name }}">
                        {{ explode(' ', $user->name)[0] }}
                    </p>
                    <p class="text-xs text-slate-400 font-medium tracking-wide">ADMINISTRADOR</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg bg-sidebar-hover text-white border-l-4 border-red-500 transition-all shadow-sm group">
                <i class="fas fa-users-cog w-6 text-center text-red-500 group-hover:scale-110 transition-transform"></i>
                <span class="ml-3">Gerenciar Usuários</span>
            </a>
            
            {{-- Outros links de ADM podem vir aqui --}}
        </nav>

        <div class="p-4 border-t border-slate-700 bg-slate-900/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-red-400 bg-red-400/10 hover:bg-red-500 hover:text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Sair
                </button>
            </form>
        </div>
    </aside>

    {{-- Conteúdo Principal --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20 px-6 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Gerenciamento de Usuários</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.professor') }}" class="inline-flex items-center px-4 py-2 bg-sidebar hover:bg-sidebar-hover text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-chalkboard-teacher mr-2"></i> Painel do Professor
                </a>
                <button id="open-create-modal" class="inline-flex items-center px-4 py-2 bg-principal hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Novo Usuário
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 lg:p-10 scroll-smooth">
            <div class="max-w-7xl mx-auto">



                {{-- Filtros --}}
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex items-center justify-between">
                    <form method="GET" action="{{ route('users.index') }}" class="flex items-center gap-3">
                        <label for="filtro" class="text-sm font-medium text-gray-700">Filtrar por:</label>
                        <select name="filtro" onchange="this.form.submit()" class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-principal focus:border-principal sm:text-sm rounded-md">
                            <option value="">Todos</option>
                            <option value="aluno" {{ request('filtro') == 'aluno' ? 'selected' : '' }}>Alunos</option>
                            <option value="professor" {{ request('filtro') == 'professor' ? 'selected' : '' }}>Professores</option>
                            <option value="adm" {{ request('filtro') == 'adm' ? 'selected' : '' }}>Administradores</option>
                        </select>
                    </form>
                    <span class="text-sm text-gray-500">Total: <strong>{{ $users->count() }}</strong> usuários</span>
                </div>

                {{-- Tabela de Usuários --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome / Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($users as $u)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold overflow-hidden">
                                                    @if($u->avatar)
                                                        <img src="{{ asset('storage/' . $u->avatar) }}" alt="Avatar" class="h-full w-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $u->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $u->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $u->tipo === 'adm' ? 'bg-red-100 text-red-800' : 
                                               ($u->tipo === 'professor' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($u->tipo) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $u->matricula ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="openEditModal(`{{ $u->id }}`, `{{ addslashes($u->name) }}`, `{{ addslashes($u->email) }}`, `{{ addslashes($u->matricula) }}`, `{{ $u->tipo }}`)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Modal de Criação --}}
    <div id="create-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Novo Usuário</h3>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select name="tipo" id="create_tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                        <option value="adm">Administrador</option>
                    </select>
                </div>
                <div id="create_matricula_container">
                    <label class="block text-sm font-medium text-gray-700">Matrícula</label>
                    <input type="text" name="matricula" id="create_matricula" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-principal focus:ring focus:ring-principal/20">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('create-modal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-principal text-white rounded-lg hover:bg-orange-600">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Edição (Simplificado para o exemplo, mas segue a mesma lógica) --}}
    <div id="edit-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Editar Usuário</h3>
            <form id="edit-form" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- Campos idênticos ao create, mas com IDs diferentes para manipulação via JS --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" name="name" id="edit_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="edit_email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="edit_avatar" class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                    <input type="file" name="avatar" id="edit_avatar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select name="tipo" id="edit_tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                        <option value="adm">Administrador</option>
                    </select>
                </div>
                <div id="edit_matricula_container">
                    <label class="block text-sm font-medium text-gray-700">Matrícula</label>
                    <input type="text" name="matricula" id="edit_matricula" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nova Senha (Opcional)</label>
                    <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('edit-modal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-principal text-white rounded-lg hover:bg-orange-600">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Lógica dos Modais
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        document.getElementById('open-create-modal').addEventListener('click', () => openModal('create-modal'));

        // Toggle Matrícula (Create)
        document.getElementById('create_tipo').addEventListener('change', function(e) {
            const container = document.getElementById('create_matricula_container');
            const input = document.getElementById('create_matricula');
            if(e.target.value === 'aluno') {
                container.classList.remove('hidden');
                input.required = true;
            } else {
                container.classList.add('hidden');
                input.required = false;
                input.value = '';
            }
        });

        // Toggle Matrícula (Edit) - Mesma lógica
        document.getElementById('edit_tipo').addEventListener('change', function(e) {
            const container = document.getElementById('edit_matricula_container');
            if(e.target.value === 'aluno') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });

        // Preencher Modal de Edição
        function openEditModal(id, name, email, matricula, tipo) {
            const form = document.getElementById('edit-form');
            form.action = `/users/${id}`; // Ajuste a rota se necessário
            
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_matricula').value = matricula || '';
            
            const tipoSelect = document.getElementById('edit_tipo');
            tipoSelect.value = tipo;
            
            // Disparar evento change para mostrar/ocultar matrícula
            tipoSelect.dispatchEvent(new Event('change'));
            
            openModal('edit-modal');
        }
    </script>
</body>
</html>