<?php

namespace App\Models;

use Corcel\Model\User as CorcelUser;

class WordPressUser extends CorcelUser
{
    /**
     * Verificar se o usuário está ativo
     */
    public function isActive()
    {
        return $this->user_status === 0;
    }

    /**
     * Obter nome de exibição
     */
    public function getDisplayName()
    {
        return $this->display_name ?: $this->user_login;
    }

    /**
     * Verificar se é administrador
     */
    public function isAdmin()
    {
        return $this->hasRole('administrator');
    }

    /**
     * Verificar se tem uma role específica
     */
    public function hasRole($role)
    {
        return $this->meta()->where('meta_key', 'wp_capabilities')
            ->where('meta_value', 'like', '%"' . $role . '"%')
            ->exists();
    }
} 