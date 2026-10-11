<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventoFormRequest;
use App\Http\Requests\StorePerguntaRequest;
use App\Models\Evento;
use App\Models\Pergunta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventoController extends Controller
{
    public function index(): View
    {
        $eventos = Evento::latest()->get();

        return view('eventos.index', compact('eventos'));
    }

    public function show($id): View
    {
        $evento = Evento::findOrFail($id);

        $perguntas = Pergunta::where('evento_id', $evento->id)
            ->where('is_public', true)
            ->with('user')
            ->withCount('votos')
            ->withExists(['votos as user_voted' => function ($query) {
                $query->where('users.id', Auth::id() ?? 0);
            }])
            ->orderByDesc('votos_count')
            ->latest()
            ->paginate(10);

        return view('eventos.show', compact('evento', 'perguntas'));
    }

    public function storePergunta(StorePerguntaRequest $request, $id): RedirectResponse
    {
        $evento = Evento::findOrFail($id);

        Pergunta::create([
            'evento_id' => $evento->id,
            'user_id' => $request->user()->id,
            'texto' => $request->validated()['texto'],
            'status' => 'pendente',
            // is_public permanece false até aprovação da moderação.
        ]);

        return redirect()->route('eventos.show', $evento->id)
            ->with('sucesso', 'Sua pergunta foi enviada e aguarda moderação.');
    }

    public function create(): View
    {
        return view('eventos.create');
    }

    public function store(EventoFormRequest $request): RedirectResponse
    {
        $evento = $request->user()->eventos()->create($request->validated());

        return redirect()->route('eventos.show', $evento->id);
    }

    public function destroyPergunta(Pergunta $pergunta): RedirectResponse
    {
        $this->authorize('delete', $pergunta);

        $eventoId = $pergunta->evento_id;
        $pergunta->delete();

        return redirect()->route('eventos.show', $eventoId)
            ->with('sucesso', 'Pergunta excluída com sucesso.');
    }
}
