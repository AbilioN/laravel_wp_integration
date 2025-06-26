<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WordPressSettings extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'options';
    protected $primaryKey = 'option_id';
    public $timestamps = false;

    /**
     * Buscar a página inicial configurada no WordPress
     */
    public static function getHomePage()
    {
        // Buscar a configuração de página inicial
        $showOnFront = self::where('option_name', 'show_on_front')->value('option_value');
        
        if ($showOnFront === 'page') {
            // Se for página, buscar o ID da página inicial
            $pageOnFront = self::where('option_name', 'page_on_front')->value('option_value');
            
            if ($pageOnFront) {
                return WordPressPost::getPageById($pageOnFront);
            }
        }
        
        // Se não for página ou não encontrar, buscar a primeira página
        return WordPressPost::getPages()->first();
    }

    /**
     * Buscar a página de posts configurada no WordPress
     */
    public static function getPostsPage()
    {
        $pageForPosts = self::where('option_name', 'page_for_posts')->value('option_value');
        
        if ($pageForPosts) {
            return WordPressPost::getPageById($pageForPosts);
        }
        
        return null;
    }

    /**
     * Buscar o título do site
     */
    public static function getSiteTitle()
    {
        return self::where('option_name', 'blogname')->value('option_value') ?: 'WordPress Site';
    }

    /**
     * Buscar a descrição do site
     */
    public static function getSiteDescription()
    {
        return self::where('option_name', 'blogdescription')->value('option_value') ?: '';
    }

    /**
     * Buscar a URL do site
     */
    public static function getSiteUrl()
    {
        return self::where('option_name', 'home')->value('option_value') ?: 'http://wordpress.local';
    }

    /**
     * Buscar posts recentes para a página inicial
     */
    public static function getRecentPostsForHome($limit = 3)
    {
        return WordPressPost::getPosts($limit);
    }

    /**
     * Verificar se o WordPress está configurado para mostrar posts na página inicial
     */
    public static function isPostsOnFront()
    {
        return self::where('option_name', 'show_on_front')->value('option_value') === 'posts';
    }

    /**
     * Verificar se o WordPress está configurado para mostrar uma página na página inicial
     */
    public static function isPageOnFront()
    {
        return self::where('option_name', 'show_on_front')->value('option_value') === 'page';
    }
} 