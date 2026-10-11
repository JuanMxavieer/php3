<?php

namespace App\Policies;

use App\Models\Pergunta;
use App\Models\User;

class PerguntaPolicy
{
    public function delete(User $user, Pergunta $pergunta): bool
    {
        return (int) $user->id === (int) $pergunta->user_id
            || (int) $user->id === (int) $pergunta->evento?->user_id;
    }
}
