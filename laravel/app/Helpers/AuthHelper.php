<?php

namespace App\Helpers;

class AuthHelper
{
    /**
     * Verificar se o usuário está logado
     */
    public static function isLoggedIn()
    {
        return session('logged_in', false);
    }

    /**
     * Obter dados do usuário atual
     */
    public static function getCurrentUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return (object) [
            'id' => session('user_id'),
            'username' => session('username'),
            'email' => session('email'),
            'display_name' => session('display_name')
        ];
    }

    /**
     * Obter nome de exibição do usuário
     */
    public static function getDisplayName()
    {
        return session('display_name', 'Usuário');
    }

    /**
     * Obter inicial do nome do usuário
     */
    public static function getInitial()
    {
        $displayName = self::getDisplayName();
        return substr($displayName, 0, 1);
    }
} 