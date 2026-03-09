<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SimplifiKathon - Aluno')</title>
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
                            800: '#1e293b',
                            900: '#13294B',
                            DEFAULT: '#13294B',
                        },
                        principal: '#F08223',
                        sidebar: '#343a40', 
                        'sidebar-hover': '#495057',
                        'unifil-orange': '#F08223',
                        'unifil-blue': '#13294B',
                        gold: '#FFD700',
                        silver: '#C0C0C0',
                        bronze: '#CD7F32',
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

    {{-- Sidebar do Aluno --}}
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:static inset-y-0 left-0 w-64 bg-sidebar text-white flex flex-col shadow-2xl lg:shadow-lg flex-shrink-0 transition-transform duration-300 ease-in-out z-50"
    >
        {{-- Logo --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
            <img src="{{ asset('image/Simplifi(K)athon.png') }}" alt="SimplifiKathon" class="h-10 w-auto">
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- User Info --}}
        <div class="px-6 py-6 border-b border-gray-700">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-lg overflow-hidden ring-2 ring-primary/30">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="overflow-hidden">
                    <p class="font-semibold text-white truncate text-sm" title="{{ Auth::user()->name }}">
                        {{ explode(' ', Auth::user()->name)[0] }}
                    </p>
                    <p class="text-xs text-primary font-medium tracking-wide">ALUNO</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard.aluno') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard.aluno') ? 'bg-primary/20 text-white border-l-4 border-primary' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard.aluno') ? 'text-primary' : 'group-hover:text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Início</span>
            </a>
            
            <a href="{{ route('aluno.hackathons.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('aluno.hackathons.*') ? 'bg-primary/20 text-white border-l-4 border-primary' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('aluno.hackathons.*') ? 'text-primary' : 'group-hover:text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Hackathons</span>
            </a>

            <a href="{{ route('grupos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('grupos.*') ? 'bg-primary/20 text-white border-l-4 border-primary' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('grupos.*') ? 'text-primary' : 'group-hover:text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Meus Grupos</span>
            </a>

            <a href="{{ route('aluno.presenca.create') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('aluno.presenca.*') ? 'bg-primary/20 text-white border-l-4 border-primary' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('aluno.presenca.*') ? 'text-primary' : 'group-hover:text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Validar Presença</span>
            </a>

            <a href="{{ route('aluno.ranking') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('aluno.ranking') ? 'bg-primary/20 text-white border-l-4 border-primary' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }} transition-all group">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('aluno.ranking') ? 'text-primary' : 'group-hover:text-primary' }}" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                </svg>
                <span>Ranking</span>
            </a>

            <div class="pt-4 mt-4 border-t border-gray-700">
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('profile.*') ? 'bg-primary/20 text-white border-l-4 border-primary' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }} transition-all group">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('profile.*') ? 'text-primary' : 'group-hover:text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Meu Perfil</span>
                </a>
            </div>
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-gray-700 bg-black/20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition-all duration-200">
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
        {{-- Header Unificado (Mobile + Desktop) --}}
        <div class="bg-white border-b border-gray-200 px-4 lg:px-6 py-3 flex items-center justify-between">
            {{-- Menu Mobile --}}
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            {{-- Logo Mobile --}}
            <img src="{{ asset('image/Simplifi(K)athon.png') }}" alt="SimplifiKathon" class="h-8 w-auto lg:hidden">
            
            {{-- Espaçador Desktop --}}
            <div class="hidden lg:block flex-1"></div>
            
            {{-- Sino de Notificações (único) --}}
            <x-notification-bell position="right" />
        </div>
        
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
