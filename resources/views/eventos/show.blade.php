@extends('layouts.app')

@section('title', $evento->titulo . ' — FalaQ')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm p-3 rounded-lg">
            <h4 class="fw-bold mb-3">💬 Faça sua pergunta</h4>

            @auth
                <form action="{{ route('eventos.perguntas.store', $evento->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                    <div class="mb-3">
                        <label for="texto" class="form-label text-secondary">Texto da pergunta</label>
                        <textarea name="texto" id="texto" rows="4"
                                  class="form-control bg-dark text-white @error('texto') border-red-500 @else border-secondary @enderror"
                                  placeholder="Digite sua dúvida ou comentário para o palestrante...">{{ old('texto') }}</textarea>
                        @error('texto')
                            <p class="text-red-500 text-danger mt-2" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-100 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 fw-bold border-0">
                        Enviar pergunta
                    </button>
                </form>
            @else
                <p class="text-secondary">Entre na sua conta para enviar uma pergunta ou votar.</p>
                <a href="{{ route('login.create') }}" class="btn btn-primary">Entrar</a>
            @endauth
        </div>
    </div>

    <div class="col-md-7">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">📋 Perguntas do evento</h4>
            <span class="text-secondary small">Perguntas públicas: {{ $perguntas->total() }}</span>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif
        @if (session('sucesso'))
            <div class="alert alert-success" role="status">{{ session('sucesso') }}</div>
        @endif

        @forelse($perguntas as $pergunta)
            <div class="card mb-4 shadow-sm border-start border-4 border-primary rounded-lg">
                <div class="card-body p-4">
                    <p class="text-secondary small mb-2">Por: {{ $pergunta->user->name ?? 'Anônimo' }}</p>
                    <p class="fs-5 mb-3 text-white">{{ $pergunta->texto }}</p>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary">👍 {{ $pergunta->votos_count }} votos</span>
                        @auth
                            <form action="{{ route('perguntas.votar', $pergunta) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-2 rounded-md border-0 fw-bold {{ $pergunta->user_voted ? 'bg-success text-white' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                    {{ $pergunta->user_voted ? '✓ Votado (remover voto)' : '👍 Votar' }}
                                </button>
                            </form>
                        @endauth
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>Status: <span class="badge bg-success">{{ $pergunta->status }}</span></span>
                        <span>{{ $pergunta->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    @can('delete', $pergunta)
                        <form action="{{ route('perguntas.destroy', $pergunta) }}" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>Excluir pergunta</x-danger-button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="alert alert-dark text-center p-4 rounded-lg">
                Nenhuma pergunta pública ainda.
            </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $perguntas->links() }}
        </div>
    </div>
</div>
@endsection
