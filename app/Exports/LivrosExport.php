<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LivrosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $livros;

    public function __construct($livros)
    {
        $this->livros = $livros;
    }

    public function collection()
    {
        return $this->livros->map(function($livro) {
            return [
                $livro->isbn,
                $livro->nome,
                $livro->editora->nome ?? 'N/A',
                $livro->autores->pluck('nome')->join(', ') ?: 'Sem autor',
                $livro->bibliografia,
                $livro->preco ? '€ ' . number_format($livro->preco, 2, ',', '.') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Título',
            'Editora',
            'Autores',
            'Bibliografia',
            'Preço'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo do cabeçalho
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 20,
            'D' => 25,
            'E' => 40,
            'F' => 12,
        ];
    }
}
