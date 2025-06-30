<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Corcel\Model\Option;

class CorcelProductController extends Controller
{
    /**
     * Listar produtos usando Corcel
     */
    public function index(Request $request)
    {
        $products = Cache::remember('corcel_products', 300, function () {
            return Post::type('product')
                      ->status('publish')
                      ->with(['meta', 'taxonomies'])
                      ->orderBy('post_date', 'desc')
                      ->get()
                      ->map(function ($product) {
                          return [
                              'id' => $product->ID,
                              'title' => $product->post_title,
                              'content' => $product->post_content,
                              'excerpt' => $product->post_excerpt,
                              'slug' => $product->post_name,
                              'price' => $product->meta->_price ?? 0,
                              'sale_price' => $product->meta->_sale_price ?? null,
                              'regular_price' => $product->meta->_regular_price ?? 0,
                              'featured_image' => $this->getProductImage($product),
                              'categories' => $this->getProductCategories($product),
                              'tags' => $this->getProductTags($product),
                              'date' => $product->post_date,
                              'modified' => $product->post_modified
                          ];
                      });
        });

        return view('products.index', compact('products'));
    }

    /**
     * Mostrar produto individual usando Corcel
     */
    public function show($slug)
    {
        $product = Post::type('product')
                      ->status('publish')
                      ->where('post_name', $slug)
                      ->with(['meta', 'taxonomies'])
                      ->first();

        if (!$product) {
            abort(404, 'Produto não encontrado');
        }

        $productData = [
            'id' => $product->ID,
            'title' => $product->post_title,
            'content' => $product->post_content,
            'excerpt' => $product->post_excerpt,
            'slug' => $product->post_name,
            'price' => $product->meta->_price ?? 0,
            'sale_price' => $product->meta->_sale_price ?? null,
            'regular_price' => $product->meta->_regular_price ?? 0,
            'featured_image' => $this->getProductImage($product),
            'gallery' => $this->getProductGallery($product),
            'categories' => $this->getProductCategories($product),
            'tags' => $this->getProductTags($product),
            'attributes' => $this->getProductAttributes($product),
            'date' => $product->post_date,
            'modified' => $product->post_modified
        ];

        return view('products.show', compact('productData'));
    }

    /**
     * Buscar produtos por categoria
     */
    public function category($slug)
    {
        $category = Taxonomy::where('taxonomy', 'product_cat')
                           ->where('slug', $slug)
                           ->first();

        if (!$category) {
            abort(404, 'Categoria não encontrada');
        }

        $products = Post::type('product')
                       ->status('publish')
                       ->taxonomy('product_cat', $slug)
                       ->with(['meta', 'taxonomies'])
                       ->orderBy('post_date', 'desc')
                       ->get()
                       ->map(function ($product) {
                           return [
                               'id' => $product->ID,
                               'title' => $product->post_title,
                               'excerpt' => $product->post_excerpt,
                               'slug' => $product->post_name,
                               'price' => $product->meta->_price ?? 0,
                               'featured_image' => $this->getProductImage($product),
                               'date' => $product->post_date
                           ];
                       });

        return view('products.category', compact('products', 'category'));
    }

    /**
     * Buscar produtos
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return redirect()->route('products.index');
        }

        $products = Post::type('product')
                       ->status('publish')
                       ->where('post_title', 'like', "%{$query}%")
                       ->orWhere('post_content', 'like', "%{$query}%")
                       ->orWhere('post_excerpt', 'like', "%{$query}%")
                       ->with(['meta', 'taxonomies'])
                       ->orderBy('post_date', 'desc')
                       ->get()
                       ->map(function ($product) {
                           return [
                               'id' => $product->ID,
                               'title' => $product->post_title,
                               'excerpt' => $product->post_excerpt,
                               'slug' => $product->post_name,
                               'price' => $product->meta->_price ?? 0,
                               'featured_image' => $this->getProductImage($product),
                               'date' => $product->post_date
                           ];
                       });

        return view('products.search', compact('products', 'query'));
    }

    /**
     * API para buscar produtos
     */
    public function apiIndex()
    {
        $products = Post::type('product')
                       ->status('publish')
                       ->with(['meta'])
                       ->orderBy('post_date', 'desc')
                       ->get()
                       ->map(function ($product) {
                           return [
                               'id' => $product->ID,
                               'title' => $product->post_title,
                               'excerpt' => $product->post_excerpt,
                               'slug' => $product->post_name,
                               'price' => $product->meta->_price ?? 0,
                               'featured_image' => $this->getProductImage($product)
                           ];
                       });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * API para buscar produto específico
     */
    public function apiShow($id)
    {
        $product = Post::type('product')
                      ->status('publish')
                      ->where('ID', $id)
                      ->with(['meta', 'taxonomies'])
                      ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $productData = [
            'id' => $product->ID,
            'title' => $product->post_title,
            'content' => $product->post_content,
            'excerpt' => $product->post_excerpt,
            'slug' => $product->post_name,
            'price' => $product->meta->_price ?? 0,
            'sale_price' => $product->meta->_sale_price ?? null,
            'regular_price' => $product->meta->_regular_price ?? 0,
            'featured_image' => $this->getProductImage($product),
            'categories' => $this->getProductCategories($product)
        ];

        return response()->json([
            'success' => true,
            'data' => $productData
        ]);
    }

    /**
     * Obter imagem do produto
     */
    private function getProductImage($product)
    {
        $thumbnailId = $product->meta->_thumbnail_id ?? null;
        
        if ($thumbnailId) {
            $attachment = Post::type('attachment')->find($thumbnailId);
            if ($attachment) {
                return $attachment->guid;
            }
        }

        return null;
    }

    /**
     * Obter galeria do produto
     */
    private function getProductGallery($product)
    {
        $galleryIds = $product->meta->_product_image_gallery ?? '';
        
        if ($galleryIds) {
            $ids = explode(',', $galleryIds);
            return Post::type('attachment')
                      ->whereIn('ID', $ids)
                      ->get()
                      ->pluck('guid');
        }

        return collect();
    }

    /**
     * Obter categorias do produto
     */
    private function getProductCategories($product)
    {
        return $product->taxonomies
                      ->where('taxonomy', 'product_cat')
                      ->map(function ($taxonomy) {
                          return [
                              'id' => $taxonomy->term_id,
                              'name' => $taxonomy->term->name,
                              'slug' => $taxonomy->slug
                          ];
                      });
    }

    /**
     * Obter tags do produto
     */
    private function getProductTags($product)
    {
        return $product->taxonomies
                      ->where('taxonomy', 'post_tag')
                      ->map(function ($taxonomy) {
                          return [
                              'id' => $taxonomy->term_id,
                              'name' => $taxonomy->term->name,
                              'slug' => $taxonomy->slug
                          ];
                      });
    }

    /**
     * Obter atributos do produto
     */
    private function getProductAttributes($product)
    {
        $attributes = [];
        
        // Buscar atributos do WooCommerce
        foreach ($product->meta as $meta) {
            if (strpos($meta->meta_key, '_product_attributes') === 0) {
                $attributes[] = [
                    'key' => $meta->meta_key,
                    'value' => $meta->meta_value
                ];
            }
        }

        return $attributes;
    }
} 