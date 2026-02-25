@extends('layouts.aluno')

@section('title', 'Dashboard do Aluno - SimplifiKathon')

@section('content')
    <div class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto space-y-8">
            
            {{-- Hero Banner --}}
            <div class="rounded-2xl p-8 text-white shadow-xl relative overflow-hidden bg-gradient-to-r from-primary-500 via-orange-500 to-purple-600 animate-fade-in-up">
                {{-- Efeitos de fundo --}}
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-60 h-60 bg-purple-500/20 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div class="lg:w-1/2 text-center lg:text-left">
                        <p class="text-orange-100 font-medium mb-2">👋 Bem-vindo de volta!</p>
                        <h2 class="text-3xl lg:text-4xl font-bold mb-3">
                            Olá, {{ explode(' ', Auth::user()->name)[0] }}!
                        </h2>
                        <p class="text-lg text-white/90 mb-6">Pronto para codar e inovar? Confira os hackathons disponíveis!</p>
                        
                        <a href="{{ route('aluno.hackathons.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-primary-600 font-bold text-lg rounded-xl hover:bg-orange-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Ver Hackathons
                        </a>
                    </div>
                    
                    {{-- Countdown Widget --}}
                    @php
                        $proximoHackathon = $hackathons->where('data_inicio', '>', now())->sortBy('data_inicio')->first();
                    @endphp
                    @if ($proximoHackathon)
                        <div class="lg:w-2/5" x-data="countdown('{{ $proximoHackathon->data_inicio }}')" x-init="init()">
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                                <p class="text-sm font-medium text-white/80 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Próximo evento: {{ $proximoHackathon->nome }}
                                </p>
                                <div class="flex justify-center gap-3">
                                    <div class="text-center">
                                        <div class="bg-white/20 rounded-xl px-4 py-3 min-w-[70px]">
                                            <span class="text-3xl font-bold" x-text="days">00</span>
                                        </div>
                                        <p class="text-xs mt-2 text-white/70">Dias</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-white/20 rounded-xl px-4 py-3 min-w-[70px]">
                                            <span class="text-3xl font-bold" x-text="hours">00</span>
                                        </div>
                                        <p class="text-xs mt-2 text-white/70">Horas</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-white/20 rounded-xl px-4 py-3 min-w-[70px]">
                                            <span class="text-3xl font-bold" x-text="minutes">00</span>
                                        </div>
                                        <p class="text-xs mt-2 text-white/70">Min</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-white/20 rounded-xl px-4 py-3 min-w-[70px] animate-pulse">
                                            <span class="text-3xl font-bold" x-text="seconds">00</span>
                                        </div>
                                        <p class="text-xs mt-2 text-white/70">Seg</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Grid de Ações Rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Validar Presença --}}
                <a href="{{ route('aluno.presenca.create') }}" class="group bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Validar Presença</h3>
                            <p class="text-sm text-slate-500">Envie sua foto</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-primary font-medium text-sm">
                        Acessar
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                {{-- Meus Grupos --}}
                <a href="{{ route('grupos.index') }}" class="group bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Meus Grupos</h3>
                            <p class="text-sm text-slate-500">Gerencie sua equipe</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-primary font-medium text-sm">
                        Acessar
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                {{-- Hackathons --}}
                <a href="{{ route('aluno.hackathons.index') }}" class="group bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Hackathons</h3>
                            <p class="text-sm text-slate-500">Eventos disponíveis</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-primary font-medium text-sm">
                        Acessar
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            {{-- Hackathons Disponíveis --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        </svg>
                        Hackathons em Destaque
                    </h3>
                    <a href="{{ route('aluno.hackathons.index') }}" class="text-sm font-medium text-primary hover:text-orange-700 flex items-center gap-1 hover:underline">
                        Ver todos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if($hackathons->isEmpty())
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-600">Nenhum hackathon disponível</h3>
                        <p class="text-slate-400 mt-1">Fique atento, novos eventos serão anunciados em breve!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($hackathons->take(6) as $hackathon)
                            <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                                {{-- Banner --}}
                                <div class="h-40 bg-gradient-to-br from-primary-400 to-purple-500 relative overflow-hidden">
                                    @if ($hackathon->banner)
                                        <img src="{{ asset('storage/' . $hackathon->banner) }}" alt="{{ $hackathon->nome }}" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-20 h-20 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    {{-- Badge de Status --}}
                                    @if (\Carbon\Carbon::parse($hackathon->data_inicio)->isFuture())
                                        <div class="absolute top-3 right-3 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                            Em Breve
                                        </div>
                                    @else
                                        <div class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
                                            Ativo
                                        </div>
                                    @endif
                                </div>

                                {{-- Conteúdo --}}
                                <div class="p-5">
                                    <h4 class="font-bold text-lg text-slate-800 line-clamp-1 group-hover:text-primary transition-colors">{{ $hackathon->nome }}</h4>
                                    <p class="text-slate-500 text-sm mt-2 line-clamp-2">{{ $hackathon->descricao }}</p>
                                    
                                    <div class="flex items-center gap-4 mt-4 text-sm text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($hackathon->data_inicio)->format('d/m') }}
                                        </span>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <a href="{{ route('aluno.hackathons.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary/10 text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-300">
                                            Saiba mais
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function countdown(targetDate) {
        return {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            interval: null,
            
            init() {
                this.updateCountdown();
                this.interval = setInterval(() => this.updateCountdown(), 1000);
            },
            
            updateCountdown() {
                const target = new Date(targetDate).getTime();
                const now = new Date().getTime();
                const distance = target - now;
                
                if (distance < 0) {
                    this.days = '00';
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    clearInterval(this.interval);
                    return;
                }
                
                this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
            }
        }
    }
</script>
@endpush