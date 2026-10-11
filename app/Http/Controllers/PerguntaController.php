<?php

namespace App\Http\Controllers;

use App\Models\Pergunta;
use Illuminate\Http\RedirectResponse;

class PerguntaController extends Controller
{
    /** Alterna o voto do usuário autenticado na pergunta. */
    public function votar(Pergunta $pergunta): RedirectResponse
    {
        $resultado = $pergunta->votos()->toggle(auth()->id());

        $mensagem = count($resultado['attached']) > 0
            ? 'Seu voto foi registrado.'
            : 'Seu voto foi removido.';

        return back()->with('status', $mensagem);
    }
}
