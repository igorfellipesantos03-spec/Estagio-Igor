@extends('layouts.professor')

@section('title', 'Relatórios - SimplifiKathon')
@section('header', 'Gerador de Relatórios')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Card Principal --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-secondary-900 via-slate-800 to-secondary-900 px-8 py-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-primary/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Gerador de Relatórios</h2>
                    <p class="text-white/60 text-sm mt-0.5">Exporte dados de hackathons, grupos e alunos em Excel</p>
                </div>
            </div>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('reports.export') }}" method="GET" class="p-8 space-y-6">

            {{-- Select de Hackathon --}}
            <div>
                <label for="hackathon_id" class="block text-sm font-semibold text-slate-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Hackathon Específico
                </label>
                <select name="hackathon_id" id="hackathon_id"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all py-3 px-4 text-sm bg-gray-50">
                    <option value="">Todos os Hackathons</option>
                    @foreach ($hackathons as $hackathon)
                        <option value="{{ $hackathon->id }}">{{ $hackathon->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Datas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="data_inicio" class="block text-sm font-semibold text-slate-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Data Inicial
                    </label>
                    <input type="date" name="data_inicio" id="data_inicio"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all py-3 px-4 text-sm bg-gray-50">
                </div>
                <div>
                    <label for="data_fim" class="block text-sm font-semibold text-slate-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Data Final
                    </label>
                    <input type="date" name="data_fim" id="data_fim"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all py-3 px-4 text-sm bg-gray-50">
                </div>
            </div>

            {{-- Dica --}}
            <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-blue-700">Deixe os filtros em branco para exportar <strong>todos</strong> os dados disponíveis. O relatório inclui Hackathons, Grupos e Alunos participantes.</p>
            </div>

            {{-- Botão --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar para Excel
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
