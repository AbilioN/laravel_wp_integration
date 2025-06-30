<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Listar produtos
     */
    public function index(Request $request)
    {
        $products = Cache::remember('woocommerce_products', 300, function () {
            return DB::connection('wordpress')
                ->table('posts as p')
                ->join('postmeta as pm', 'p.ID', '=', 'pm.post_id')
                ->where('p.post_type', 'product')
                ->where('p.post_status', 'publish')
                ->where('pm.meta_key', '_price')
                ->select([
                    'p.ID',
                    'p.post_title',
                    'p.post_content',
                    'p.post_excerpt',
                    'p.post_name',
                    'pm.meta_value as price'
                ])
                ->orderBy('p.post_date', 'desc')
                ->get();
        });

        return view('products.index', compact('products'));
    }

    /**
     * Mostrar produto individual
     */
    public function show($slug)
    {
        $product = DB::connection('wordpress')
            ->table('posts as p')
            ->leftJoin('postmeta as pm_price', function($join) {
                $join->on('p.ID', '=', 'pm_price.post_id')
                     ->where('pm_price.meta_key', '_price');
            })
            ->leftJoin('postmeta as pm_sale', function($join) {
                $join->on('p.ID', '=', 'pm_sale.post_id')
                     ->where('pm_sale.meta_key', '_sale_price');
            })
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->where('p.post_name', $slug)
            ->select([
                'p.ID',
                'p.post_title',
                'p.post_content',
                'p.post_excerpt',
                'p.post_name',
                'pm_price.meta_value as price',
                'pm_sale.meta_value as sale_price'
            ])
            ->first();

        if (!$product) {
            abort(404, 'Produto não encontrado');
        }

        // Buscar imagens do produto
        $images = DB::connection('wordpress')
            ->table('postmeta')
            ->where('post_id', $product->ID)
            ->where('meta_key', '_product_image_gallery')
            ->first();

        return view('products.show', compact('product', 'images'));
    }

    /**
     * API para buscar produtos
     */
    public function apiIndex()
    {
        $products = Cache::remember('woocommerce_products_api', 300, function () {
            return DB::connection('wordpress')
                ->table('posts as p')
                ->leftJoin('postmeta as pm_price', function($join) {
                    $join->on('p.ID', '=', 'pm_price.post_id')
                         ->where('pm_price.meta_key', '_price');
                })
                ->where('p.post_type', 'product')
                ->where('p.post_status', 'publish')
                ->select([
                    'p.ID',
                    'p.post_title',
                    'p.post_excerpt',
                    'p.post_name',
                    'pm_price.meta_value as price'
                ])
                ->orderBy('p.post_date', 'desc')
                ->get();
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
        $product = DB::connection('wordpress')
            ->table('posts as p')
            ->leftJoin('postmeta as pm_price', function($join) {
                $join->on('p.ID', '=', 'pm_price.post_id')
                     ->where('pm_price.meta_key', '_price');
            })
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->where('p.ID', $id)
            ->select([
                'p.ID',
                'p.post_title',
                'p.post_content',
                'p.post_excerpt',
                'p.post_name',
                'pm_price.meta_value as price'
            ])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
} 