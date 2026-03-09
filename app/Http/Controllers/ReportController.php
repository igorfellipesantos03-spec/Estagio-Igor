<?php

namespace App\Http\Controllers;

use App\Exports\HackathonReportExport;
use App\Models\Hackathon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Exibe a página de filtros para geração de relatórios.
     */
    public function index()
    {
        $hackathons = Hackathon::orderBy('nome')->get();

        return view('reports.professor.index', compact('hackathons'));
    }

    /**
     * Exporta o relatório filtrado em formato Excel.
     */
    public function export(Request $request)
    {
        $request->validate([
            'hackathon_id' => 'nullable|exists:hackathons,id',
            'data_inicio'  => 'nullable|date',
            'data_fim'     => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $hackathons = Hackathon::with(['grupos.membros'])
            ->when($request->hackathon_id, function ($query, $hackathonId) {
                $query->where('id', $hackathonId);
            })
            ->when($request->data_inicio, function ($query, $dataInicio) {
                $query->where('data_inicio', '>=', $dataInicio);
            })
            ->when($request->data_fim, function ($query, $dataFim) {
                $query->where('data_fim', '<=', $dataFim);
            })
            ->orderBy('data_inicio', 'desc')
            ->get();

        // Achata os dados: uma linha por grupo
        $dados = collect();

        foreach ($hackathons as $hackathon) {
            foreach ($hackathon->grupos as $grupo) {
                $alunos = $grupo->membros
                    ->map(fn($m) => $m->name . ($m->matricula ? ' - ' . $m->matricula : ''))
                    ->implode(', ');

                $dados->push([
                    'hackathon'   => $hackathon->nome,
                    'data_inicio' => $hackathon->data_inicio->format('d/m/Y H:i'),
                    'data_fim'    => $hackathon->data_fim->format('d/m/Y H:i'),
                    'grupo'       => $grupo->nome,
                    'codigo'      => $grupo->codigo,
                    'alunos'      => $alunos ?: 'Sem integrantes',
                ]);
            }

            // Hackathon sem grupos
            if ($hackathon->grupos->isEmpty()) {
                $dados->push([
                    'hackathon'   => $hackathon->nome,
                    'data_inicio' => $hackathon->data_inicio->format('d/m/Y H:i'),
                    'data_fim'    => $hackathon->data_fim->format('d/m/Y H:i'),
                    'grupo'       => '-',
                    'codigo'      => '-',
                    'alunos'      => '-',
                ]);
            }
        }

        if ($dados->isEmpty()) {
            return back()->with('error', 'Nenhum dado encontrado com os filtros aplicados.');
        }

        $filename = 'relatorio_hackathons_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new HackathonReportExport($dados), $filename);
    }
}
