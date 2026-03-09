<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Painel do Professor - SimplifiKathon')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#F08223',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            DEFAULT: '#F08223',
                        },
                        secondary: {
                            700: '#334155',
                            800: '#1e293b',
                            900: '#13294B',
                            DEFAULT: '#13294B',
                        },
                        principal: '#F08223',
                        sidebar: '#13294B', 
                        'sidebar-hover': '#1e293b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out',
                        'fade-in-down': 'fadeInDown 0.5s ease-out',
                        'slide-in-right': 'slideInRight 0.3s ease-out',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeInDown: {
                            '0%': { opacity: '0', transform: 'translateY(-20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInRight: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                    },
                }
            }
        }
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
    @stack('styles')
</head>

<body class="bg-gray-50 text-slate-800 font-sans h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Toast Global --}}
    <x-toast />

    {{-- Overlay Mobile --}}
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
        style="display: none;"
    ></div>

    {{-- Sidebar do Professor --}}
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:static inset-y-0 left-0 w-72 bg-sidebar text-slate-200 flex flex-col shadow-2xl lg:shadow-lg flex-shrink-0 transition-transform duration-300 ease-in-out z-50"
    >
        {{-- Logo --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-700">
            <img src="{{ asset('image/Simplifi(K)athon.png') }}" alt="SimplifiKathon" class="h-12 w-auto">
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- User Info --}}
        <div class="px-6 py-6 border-b border-slate-700">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary to-orange-600 flex items-center justify-center text-white font-bold text-xl ring-2 ring-offset-2 ring-offset-sidebar ring-primary shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-white truncate max-w-[140px]" title="{{ Auth::user()->name }}">
                        {{ explode(' ', Auth::user()->name)[0] }}
                    </p>
                    <p class="text-xs text-primary font-medium tracking-wide">PROFESSOR</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard.professor') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard.professor') ? 'bg-sidebar-hover text-white border-l-4 border-primary' : 'text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard.professor') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Início</span>
            </a>
            
            {{-- Novo Hackathon Button --}}
            @if(request()->routeIs('dashboard.professor'))
            <button id="open-create-modal" onclick="document.getElementById('create-hackathon-modal').classList.remove('hidden')" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary transition-all group">
                <svg class="w-5 h-5 mr-3 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Criar Hackathon</span>
            </button>
            @endif

            <a href="{{ route('hackathons.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('hackathons.*') ? 'bg-sidebar-hover text-white border-l-4 border-primary' : 'text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hackathons.*') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Ver Hackathons</span>
            </a>

            <div class="pt-4 mt-4 border-t border-slate-700">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Gestão</p>
                <a href="{{ route('professor.presenca.hackathons') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('professor.presenca.*') ? 'bg-sidebar-hover text-white border-l-4 border-primary' : 'text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary' }} transition-all group">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('professor.presenca.*') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Validar Presenças</span>
                    @php
                        $pendentes = \App\Models\AttendanceRecord::where('status', \App\Enums\AttendanceStatus::PENDING)->count();
                    @endphp
                    @if ($pendentes > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $pendentes }}</span>
                    @endif
                </a>
                
                <a href="{{ route('professor.grupos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('professor.grupos.*') ? 'bg-sidebar-hover text-white border-l-4 border-primary' : 'text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary' }} transition-all group">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('professor.grupos.*') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Gerenciar Grupos</span>
                </a>
                
                <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('reports.*') ? 'bg-sidebar-hover text-white border-l-4 border-primary' : 'text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary' }} transition-all group">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Relatórios</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('profile.*') ? 'bg-sidebar-hover text-white border-l-4 border-primary' : 'text-slate-300 hover:bg-sidebar-hover hover:text-white border-l-4 border-transparent hover:border-primary' }} transition-all group mt-2">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('profile.*') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Meu Perfil</span>
                </a>
            </div>
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-slate-700 bg-slate-900/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-medium text-red-400 bg-red-400/10 hover:bg-red-500 hover:text-white rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sair
                </button>
            </form>
        </div>
    </aside>

    {{-- Conteúdo Principal --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
        {{-- Header --}}
        <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-20 px-6 py-4 flex items-center justify-between">
            {{-- Mobile Menu Button --}}
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-primary mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">@yield('header', 'Painel do Professor')</h1>
            
            <div class="flex items-center gap-4">
                @if(auth()->user()->tipo === 'adm')
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Voltar para Admin
                </a>
                @endif
                <button class="p-2 text-gray-400 hover:text-primary transition-colors relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                </button>
            </div>
        </header>

        {{-- Área de Scroll --}}
        <div class="flex-1 overflow-y-auto p-6 lg:p-10 scroll-smooth">
            <div class="max-w-7xl mx-auto space-y-8">
                
                @yield('content')

            </div>
        </div>
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>
