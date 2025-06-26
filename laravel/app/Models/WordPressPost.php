<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WordPressPost extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'post_title',
        'post_content',
        'post_excerpt',
        'post_status',
        'post_type',
        'post_date',
        'post_modified',
        'post_name',
        'guid'
    ];

    /**
     * Buscar páginas publicadas
     */
    public static function getPages()
    {
        return self::where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->orderBy('menu_order', 'asc')
                  ->orderBy('post_title', 'asc')
                  ->get();
    }

    /**
     * Buscar uma página específica por slug
     */
    public static function getPageBySlug($slug)
    {
        return self::where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->where('post_name', $slug)
                  ->first();
    }

    /**
     * Buscar uma página por ID
     */
    public static function getPageById($id)
    {
        return self::where('post_type', 'page')
                  ->where('post_status', 'publish')
                  ->where('ID', $id)
                  ->first();
    }

    /**
     * Buscar posts publicados
     */
    public static function getPosts($limit = 10)
    {
        return self::where('post_type', 'post')
                  ->where('post_status', 'publish')
                  ->orderBy('post_date', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Buscar meta dados de uma página
     */
    public function getMeta($key)
    {
        return DB::connection('wordpress')
                 ->table('postmeta')
                 ->where('post_id', $this->ID)
                 ->where('meta_key', $key)
                 ->value('meta_value');
    }

    /**
     * Buscar todos os meta dados de uma página
     */
    public function getAllMeta()
    {
        return DB::connection('wordpress')
                 ->table('postmeta')
                 ->where('post_id', $this->ID)
                 ->pluck('meta_value', 'meta_key')
                 ->toArray();
    }
}
