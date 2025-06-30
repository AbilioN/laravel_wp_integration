<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;

class CartController extends Controller
{
    /**
     * Mostrar carrinho
     */
    public function index()
    {
        $cart = session('cart', []);
        
        // Limpar itens antigos que não têm a estrutura correta
        $cart = array_filter($cart, function($item) {
            return isset($item['id']) && isset($item['name']) && isset($item['price']) && isset($item['quantity']);
        });
        
        $cartItems = array_values($cart);
        $totalItems = 0;
        $subtotal = 0;

        // Calcular totais
        foreach ($cart as $item) {
            $totalItems += $item['quantity'];
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Atualizar sessão com carrinho limpo
        session(['cart' => $cart]);

        return view('cart.index', compact('cartItems', 'totalItems', 'subtotal'));
    }

    /**
     * Adicionar produto ao carrinho
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Buscar produto no WordPress
        $product = DB::connection('wordpress')
            ->table('posts as p')
            ->leftJoin('postmeta as pm_price', function($join) {
                $join->on('p.ID', '=', 'pm_price.post_id')
                     ->where('pm_price.meta_key', '_price');
            })
            ->leftJoin('postmeta as pm_image', function($join) {
                $join->on('p.ID', '=', 'pm_image.post_id')
                     ->where('pm_image.meta_key', '_thumbnail_id');
            })
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->where('p.ID', $productId)
            ->select([
                'p.ID',
                'p.post_title',
                'p.post_name',
                'pm_price.meta_value as price',
                'pm_image.meta_value as image_id'
            ])
            ->first();

        if (!$product) {
            return back()->withErrors(['product' => 'Produto não encontrado']);
        }

        $cart = session('cart', []);

        // Verificar se produto já está no carrinho
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->ID,
                'name' => $product->post_title,
                'slug' => $product->post_name,
                'price' => (float) $product->price,
                'quantity' => $quantity,
                'image' => $product->image_id ? 'http://localhost:8080/wp-content/uploads/' . $product->image_id . '.jpg' : null
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Produto adicionado ao carrinho!');
    }

    /**
     * Atualizar quantidade no carrinho
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'action' => 'required|in:increase,decrease'
        ]);

        $productId = $request->input('product_id');
        $action = $request->input('action');

        $cart = session('cart', []);

        if (!isset($cart[$productId])) {
            return back()->withErrors(['product' => 'Produto não encontrado no carrinho']);
        }

        if ($action === 'increase') {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId]['quantity']--;
            if ($cart[$productId]['quantity'] <= 0) {
                unset($cart[$productId]);
            }
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Carrinho atualizado!');
    }

    /**
     * Remover produto do carrinho
     */
    public function remove($productId)
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produto removido do carrinho!');
    }

    /**
     * Limpar carrinho
     */
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Carrinho limpo!');
    }

    /**
     * Finalizar compra
     */
    public function checkout()
    {
        // Verificar se usuário está logado
        if (!AuthController::isLoggedIn()) {
            return redirect('/login')->withErrors(['login' => 'Faça login para finalizar a compra']);
        }

        $cart = session('cart', []);
        
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Carrinho vazio']);
        }

        // Calcular total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Redirecionar para WordPress para finalizar compra
        $wordpressUrl = 'http://localhost:8080';
        $checkoutUrl = $wordpressUrl . '/checkout/';

        // Adicionar produtos ao carrinho do WordPress via URL
        $items = [];
        foreach ($cart as $item) {
            $items[] = $item['id'] . ':' . $item['quantity'];
        }

        if (!empty($items)) {
            $checkoutUrl .= '?add-to-cart=' . implode(',', $items);
        }

        return redirect($checkoutUrl);
    }

    /**
     * API para obter carrinho
     */
    public function apiIndex()
    {
        $cart = session('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => array_values($cart),
                'total' => $total,
                'count' => count($cart)
            ]
        ]);
    }

    /**
     * API para adicionar ao carrinho
     */
    public function apiAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Buscar produto
        $product = DB::connection('wordpress')
            ->table('posts as p')
            ->leftJoin('postmeta as pm_price', function($join) {
                $join->on('p.ID', '=', 'pm_price.post_id')
                     ->where('pm_price.meta_key', '_price');
            })
            ->leftJoin('postmeta as pm_image', function($join) {
                $join->on('p.ID', '=', 'pm_image.post_id')
                     ->where('pm_image.meta_key', '_thumbnail_id');
            })
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->where('p.ID', $productId)
            ->select([
                'p.ID',
                'p.post_title',
                'p.post_name',
                'pm_price.meta_value as price',
                'pm_image.meta_value as image_id'
            ])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->ID,
                'name' => $product->post_title,
                'slug' => $product->post_name,
                'price' => (float) $product->price,
                'quantity' => $quantity,
                'image' => $product->image_id ? 'http://localhost:8080/wp-content/uploads/' . $product->image_id . '.jpg' : null
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho',
            'cart_count' => count($cart)
        ]);
    }
} 