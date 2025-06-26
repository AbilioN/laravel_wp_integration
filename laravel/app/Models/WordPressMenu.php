<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WordPressMenu extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    /**
     * Buscar páginas para o menu de navegação
     */
    public static function getNavigationPages()
    {
        return self::where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->orderBy('menu_order', 'asc')
                  ->orderBy('post_title', 'asc')
                  ->get(['ID', 'post_title', 'post_name', 'menu_order']);
    }

    /**
     * Buscar posts recentes para o menu
     */
    public static function getRecentPosts($limit = 5)
    {
        return self::where('post_type', 'post')
                  ->where('post_status', 'publish')
                  ->orderBy('post_date', 'desc')
                  ->limit($limit)
                  ->get(['ID', 'post_title', 'post_name', 'post_date']);
    }

    /**
     * Buscar páginas por categoria (usando meta dados)
     */
    public static function getPagesByCategory($category)
    {
        $pageIds = DB::connection('wordpress')
                     ->table('postmeta')
                     ->where('meta_key', '_wp_page_template')
                     ->where('meta_value', 'like', "%{$category}%")
                     ->pluck('post_id');

        return self::whereIn('ID', $pageIds)
                  ->where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->orderBy('post_title', 'asc')
                  ->get();
    }

    /**
     * Buscar páginas principais (sem parent)
     */
    public static function getMainPages()
    {
        return self::where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->where('post_parent', 0)
                  ->orderBy('menu_order', 'asc')
                  ->orderBy('post_title', 'asc')
                  ->get(['ID', 'post_title', 'post_name', 'menu_order']);
    }

    /**
     * Buscar subpáginas de uma página específica
     */
    public static function getSubPages($parentId)
    {
        return self::where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->where('post_parent', $parentId)
                  ->orderBy('menu_order', 'asc')
                  ->orderBy('post_title', 'asc')
                  ->get(['ID', 'post_title', 'post_name', 'menu_order']);
    }
} 