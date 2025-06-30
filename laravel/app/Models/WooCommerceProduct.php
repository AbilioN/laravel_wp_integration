<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WooCommerceProduct extends Model
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
     * Get all published products
     */
    public static function getProducts($limit = 12, $category = null, $search = null)
    {
        $query = self::where('post_type', 'product')
                    ->where('post_status', 'publish');

        if ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('post_title', 'like', "%{$search}%")
                  ->orWhere('post_content', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('post_date', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get product by ID
     */
    public static function getProduct($id)
    {
        return self::where('ID', $id)
                  ->where('post_type', 'product')
                  ->where('post_status', 'publish')
                  ->first();
    }

    /**
     * Get product by slug
     */
    public static function getProductBySlug($slug)
    {
        return self::where('post_name', $slug)
                  ->where('post_type', 'product')
                  ->where('post_status', 'publish')
                  ->first();
    }

    /**
     * Get product meta
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
     * Get all product meta
     */
    public function getAllMeta()
    {
        return DB::connection('wordpress')
                 ->table('postmeta')
                 ->where('post_id', $this->ID)
                 ->pluck('meta_value', 'meta_key')
                 ->toArray();
    }

    /**
     * Get product price
     */
    public function getPrice()
    {
        $salePrice = $this->getMeta('_sale_price');
        $regularPrice = $this->getMeta('_regular_price');
        
        return $salePrice ?: $regularPrice;
    }

    /**
     * Get product regular price
     */
    public function getRegularPrice()
    {
        return $this->getMeta('_regular_price');
    }

    /**
     * Get product sale price
     */
    public function getSalePrice()
    {
        return $this->getMeta('_sale_price');
    }

    /**
     * Get product stock status
     */
    public function getStockStatus()
    {
        return $this->getMeta('_stock_status') ?: 'instock';
    }

    /**
     * Get product stock quantity
     */
    public function getStockQuantity()
    {
        return $this->getMeta('_stock');
    }

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->getStockStatus() === 'instock';
    }

    /**
     * Get product categories
     */
    public function categories()
    {
        return $this->belongsToMany(
            WooCommerceCategory::class,
            'term_relationships',
            'object_id',
            'term_taxonomy_id'
        )->where('taxonomy', 'product_cat');
    }

    /**
     * Get product images
     */
    public function getImages()
    {
        $galleryIds = $this->getMeta('_product_image_gallery');
        
        if (!$galleryIds) {
            return [];
        }

        $imageIds = explode(',', $galleryIds);
        $images = [];

        foreach ($imageIds as $imageId) {
            $image = DB::connection('wordpress')
                      ->table('posts')
                      ->where('ID', $imageId)
                      ->where('post_type', 'attachment')
                      ->first();

            if ($image) {
                $images[] = [
                    'id' => $image->ID,
                    'url' => $image->guid,
                    'title' => $image->post_title
                ];
            }
        }

        return $images;
    }

    /**
     * Get product featured image
     */
    public function getFeaturedImage()
    {
        $thumbnailId = $this->getMeta('_thumbnail_id');
        
        if (!$thumbnailId) {
            return null;
        }

        $image = DB::connection('wordpress')
                  ->table('posts')
                  ->where('ID', $thumbnailId)
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
     * Get product variations
     */
    public function getVariations()
    {
        return DB::connection('wordpress')
                 ->table('posts')
                 ->where('post_parent', $this->ID)
                 ->where('post_type', 'product_variation')
                 ->where('post_status', 'publish')
                 ->get();
    }

    /**
     * Get product attributes
     */
    public function getAttributes()
    {
        $attributes = DB::connection('wordpress')
                       ->table('postmeta')
                       ->where('post_id', $this->ID)
                       ->where('meta_key', 'like', '_product_attributes_%')
                       ->get();

        return $attributes->map(function ($attribute) {
            $data = $this->maybe_unserialize($attribute->meta_value);
            return [
                'name' => $data['name'] ?? '',
                'value' => $data['value'] ?? '',
                'position' => $data['position'] ?? 0,
                'visible' => $data['is_visible'] ?? false,
                'variation' => $data['is_variation'] ?? false
            ];
        });
    }

    /**
     * Check if product is on sale
     */
    public function isOnSale()
    {
        $salePrice = $this->getSalePrice();
        $regularPrice = $this->getRegularPrice();
        
        return $salePrice && $salePrice < $regularPrice;
    }

    /**
     * Get product formatted data
     */
    public function getFormattedData()
    {
        $meta = $this->getAllMeta();
        
        return [
            'id' => $this->ID,
            'name' => $this->post_title,
            'description' => $this->post_content,
            'short_description' => $this->post_excerpt,
            'slug' => $this->post_name,
            'status' => $this->post_status,
            'date' => $this->post_date,
            'modified' => $this->post_modified,
            'price' => $this->getPrice(),
            'regular_price' => $this->getRegularPrice(),
            'sale_price' => $this->getSalePrice(),
            'stock_status' => $this->getStockStatus(),
            'stock_quantity' => $this->getStockQuantity(),
            'is_on_sale' => $this->isOnSale(),
            'is_in_stock' => $this->isInStock(),
            'categories' => $this->categories,
            'images' => $this->getImages(),
            'featured_image' => $this->getFeaturedImage(),
            'attributes' => $this->getAttributes(),
            'variations' => $this->getVariations(),
            'meta' => $meta
        ];
    }

    /**
     * WordPress maybe_unserialize function
     */
    private function maybe_unserialize($data)
    {
        if ($this->is_serialized($data)) {
            return unserialize($data);
        }
        return $data;
    }

    /**
     * WordPress is_serialized function
     */
    private function is_serialized($data)
    {
        if (!is_string($data)) {
            return false;
        }
        
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }
        switch ($badions[1]) {
            case 'a':
            case 'O':
            case 's':
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }
                break;
            case 'b':
            case 'i':
            case 'd':
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                    return true;
                }
                break;
        }
        return false;
    }
} 