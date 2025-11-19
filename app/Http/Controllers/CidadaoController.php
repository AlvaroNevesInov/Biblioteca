<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class CidadaoController extends Controller

{

    /**

     * Display a listing of all citizens.

     */

    public function index()
    {
        return view('cidadaos.index');
    }



    /**

     * Display the specified citizen with their request history.

     */

    public function show(User $cidadao)
    {
        $cidadao->load('requisicoes.livro');

        // Separar requisições ativas e passadas

        $requisicoesAtivas = $cidadao->requisicoes()

            ->with('livro.editora', 'livro.autores')
            ->ativas()
            ->recentes()
            ->get();

        $requisicoesPast = $cidadao->requisicoes()

            ->with('livro.editora', 'livro.autores')
            ->passadas()
            ->recentes()
            ->get();

        return view('cidadaos.show', compact('cidadao', 'requisicoesAtivas', 'requisicoesPast'));
    }

}
