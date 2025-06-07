<?php

namespace App\Policies;

use App\Models\TablaGeneral\Anexo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnexoPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Anexo $anexo)
    {
        return $user->id_empresa === $anexo->id_empresa;
    }

    public function create(User $user)
    {
        return $user->can('crear_anexos');
    }

    public function update(User $user, Anexo $anexo)
    {
        return $user->id_empresa === $anexo->id_empresa &&
            $user->can('editar_anexos');
    }

    public function delete(User $user, Anexo $anexo)
    {
        return $user->id_empresa === $anexo->id_empresa &&
            $user->can('eliminar_anexos') &&
            !$anexo->compras()->exists() &&
            !$anexo->ventas()->exists();
    }
}
