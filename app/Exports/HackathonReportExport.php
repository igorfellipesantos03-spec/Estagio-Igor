<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class HackathonReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * Retorna a coleção de dados já achatada (uma linha por grupo).
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Cabeçalhos do Excel.
     */
    public function headings(): array
    {
        return [
            'Hackathon',
            'Data de Início',
            'Data de Término',
            'Grupo',
            'Código do Grupo',
            'Alunos (Nome - Matrícula)',
        ];
    }

    /**
     * Mapeamento de cada item para as colunas.
     */
    public function map($row): array
    {
        return [
            $row['hackathon'],
            $row['data_inicio'],
            $row['data_fim'],
            $row['grupo'],
            $row['codigo'],
            $row['alunos'],
        ];
    }

    /**
     * Estilos da planilha.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
