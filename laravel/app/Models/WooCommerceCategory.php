<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WooCommerceCategory extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'term_group'
    ];

    /**
     * Get all product categories
     */
    public static function getCategories($parent = null)
    {
        $query = DB::connection('wordpress')
                  ->table('terms as t')
                  ->join('term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                  ->where('tt.taxonomy', 'product_cat')
                  ->where('tt.count', '>', 0);

        if ($parent !== null) {
            $query->where('tt.parent', $parent);
        }

        return $query->orderBy('t.name', 'asc')
                    ->get(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count', 'tt.parent']);
    }

    /**
     * Get category by ID
     */
    public static function getCategory($id)
    {
        return DB::connection('wordpress')
                 ->table('terms as t')
                 ->join('term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                 ->where('t.term_id', $id)
                 ->where('tt.taxonomy', 'product_cat')
                 ->first(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count', 'tt.parent']);
    }

    /**
     * Get category by slug
     */
    public static function getCategoryBySlug($slug)
    {
        return DB::connection('wordpress')
                 ->table('terms as t')
                 ->join('term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                 ->where('t.slug', $slug)
                 ->where('tt.taxonomy', 'product_cat')
                 ->first(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count', 'tt.parent']);
    }

    /**
     * Get category products
     */
    public function getProducts($limit = 12, $orderBy = 'date', $order = 'desc')
    {
        $query = DB::connection('wordpress')
                  ->table('posts as p')
                  ->join('term_relationships as tr', 'p.ID', '=', 'tr.object_id')
                  ->join('term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                  ->where('p.post_type', 'product')
                  ->where('p.post_status', 'publish')
                  ->where('tt.taxonomy', 'product_cat')
                  ->where('tt.term_id', $this->term_id);

        // Order by
        switch ($orderBy) {
            case 'price':
                $query->orderBy('p.menu_order', $order);
                break;
            case 'title':
                $query->orderBy('p.post_title', $order);
                break;
            case 'popularity':
                $query->orderBy('p.comment_count', $order);
                break;
            default:
                $query->orderBy('p.post_date', $order);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get subcategories
     */
    public function getSubcategories()
    {
        return DB::connection('wordpress')
                 ->table('terms as t')
                 ->join('term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                 ->where('tt.taxonomy', 'product_cat')
                 ->where('tt.parent', $this->term_id)
                 ->where('tt.count', '>', 0)
                 ->orderBy('t.name', 'asc')
                 ->get(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count']);
    }

    /**
     * Get parent category
     */
    public function getParentCategory()
    {
        if (!$this->parent) {
            return null;
        }

        return self::getCategory($this->parent);
    }

    /**
     * Get category image
     */
    public function getImage()
    {
        $imageId = DB::connection('wordpress')
                    ->table('termmeta')
                    ->where('term_id', $this->term_id)
                    ->where('meta_key', 'thumbnail_id')
                    ->value('meta_value');

        if (!$imageId) {
            return null;
        }

        $image = DB::connection('wordpress')
                  ->table('posts')
                  ->where('ID', $imageId)
                  ->where('post_type', 'attachment')
                  ->first();

        if (!$image) {
            return null;
        }

        return [
            'id' => $image->ID,
            'url' => $image->guid,
            'title' => $image->post_title
        ];
    }

    /**
     * Get category meta
     */
    public function getMeta($key)
    {
        return DB::connection('wordpress')
                 ->table('termmeta')
                 ->where('term_id', $this->term_id)
                 ->where('meta_key', $key)
                 ->value('meta_value');
    }

    /**
     * Get all category meta
     */
    public function getAllMeta()
    {
        return DB::connection('wordpress')
                 ->table('termmeta')
                 ->where('term_id', $this->term_id)
                 ->pluck('meta_value', 'meta_key')
                 ->toArray();
    }

    /**
     * Get category formatted data
     */
    public function getFormattedData()
    {
        $meta = $this->getAllMeta();
        
        return [
            'id' => $this->term_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'count' => $this->count,
            'parent' => $this->parent,
            'image' => $this->getImage(),
            'meta' => $meta,
            'url' => "/category/{$this->slug}"
        ];
    }

    /**
     * Check if category has products
     */
    public function hasProducts()
    {
        return $this->count > 0;
    }

    /**
     * Check if category has subcategories
     */
    public function hasSubcategories()
    {
        $subcategories = $this->getSubcategories();
        return $subcategories->count() > 0;
    }

    /**
     * Get category breadcrumb
     */
    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $currentCategory = $this;

        while ($currentCategory) {
            array_unshift($breadcrumb, [
                'id' => $currentCategory->term_id,
                'name' => $currentCategory->name,
                'slug' => $currentCategory->slug,
                'url' => "/category/{$currentCategory->slug}"
            ]);

            $currentCategory = $currentCategory->getParentCategory();
        }

        return $breadcrumb;
    }
} 