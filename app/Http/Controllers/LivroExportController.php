<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class LivroExportController extends Controller
{
    public function exportarExcel()
    {
        $livros = Livro::with(['editora', 'autores'])->get();

        return Excel::download(
            new LivrosExport($livros),
            'catalogo-livros-' . date('Y-m-d') . '.xlsx'
        );
    }
}
