<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WooCommerceProduct;
use App\Models\WooCommerceOrder;
use App\Models\WooCommerceCategory;
use App\Services\WooCommerceService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WooCommerceApiController extends Controller
{
    protected $wooCommerceService;
    protected $cartService;

    public function __construct(WooCommerceService $wooCommerceService, CartService $cartService)
    {
        $this->wooCommerceService = $wooCommerceService;
        $this->cartService = $cartService;
    }

    /**
     * Get all products
     */
    public function products(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 12);
            $category = $request->get('category');
            $search = $request->get('search');
            $orderBy = $request->get('orderby', 'date');
            $order = $request->get('order', 'desc');

            $products = Cache::remember("api_products_{$perPage}_{$category}_{$search}_{$orderBy}_{$order}", 300, function () use ($perPage, $category, $search, $orderBy, $order) {
                return $this->wooCommerceService->getProducts($perPage, $category, $search, $orderBy, $order);
            });

            return response()->json([
                'success' => true,
                'data' => $products,
                'meta' => [
                    'total' => $products->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Products', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar produtos'
            ], 500);
        }
    }

    /**
     * Get specific product by ID
     */
    public function product($id)
    {
        try {
            $product = Cache::remember("api_product_{$id}", 300, function () use ($id) {
                return $this->wooCommerceService->getProduct($id);
            });

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
        } catch (\Exception $e) {
            Log::error('API Error - Get Product', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar produto'
            ], 500);
        }
    }

    /**
     * Get products by category
     */
    public function productsByCategory($category, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 12);
            $orderBy = $request->get('orderby', 'date');
            $order = $request->get('order', 'desc');

            $products = Cache::remember("api_products_category_{$category}_{$perPage}_{$orderBy}_{$order}", 300, function () use ($category, $perPage, $orderBy, $order) {
                return $this->wooCommerceService->getProductsByCategory($category, $perPage, $orderBy, $order);
            });

            return response()->json([
                'success' => true,
                'data' => $products,
                'meta' => [
                    'category' => $category,
                    'total' => $products->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Products by Category', ['error' => $e->getMessage(), 'category' => $category]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar produtos da categoria'
            ], 500);
        }
    }

    /**
     * Search products
     */
    public function searchProducts(Request $request)
    {
        try {
            $query = $request->get('q');
            $perPage = $request->get('per_page', 12);
            $category = $request->get('category');

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query de busca é obrigatória'
                ], 400);
            }

            $products = $this->wooCommerceService->searchProducts($query, $perPage, $category);

            return response()->json([
                'success' => true,
                'data' => $products,
                'meta' => [
                    'query' => $query,
                    'total' => $products->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Search Products', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro na busca de produtos'
            ], 500);
        }
    }

    /**
     * Get all categories
     */
    public function categories(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $parent = $request->get('parent');

            $categories = Cache::remember("api_categories_{$perPage}_{$parent}", 600, function () use ($perPage, $parent) {
                return $this->wooCommerceService->getCategories($perPage, $parent);
            });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Categories', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar categorias'
            ], 500);
        }
    }

    /**
     * Get specific category by ID
     */
    public function category($id)
    {
        try {
            $category = Cache::remember("api_category_{$id}", 600, function () use ($id) {
                return $this->wooCommerceService->getCategory($id);
            });

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Category', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar categoria'
            ], 500);
        }
    }

    /**
     * Get user orders (authenticated)
     */
    public function orders(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);
            $status = $request->get('status');

            $orders = $this->wooCommerceService->getUserOrders($user->id, $perPage, $status);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'meta' => [
                    'total' => $orders->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get User Orders', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedidos'
            ], 500);
        }
    }

    /**
     * Get specific order (authenticated)
     */
    public function order($id)
    {
        try {
            $user = Auth::user();
            $order = $this->wooCommerceService->getUserOrder($user->id, $id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Order', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedido'
            ], 500);
        }
    }

    /**
     * Create order (authenticated)
     */
    public function createOrder(Request $request)
    {
        try {
            $user = Auth::user();
            $orderData = $request->validate([
                'billing' => 'required|array',
                'shipping' => 'required|array',
                'payment_method' => 'required|string',
                'payment_method_title' => 'required|string',
                'line_items' => 'required|array|min:1'
            ]);

            $order = $this->wooCommerceService->createOrder($user->id, $orderData);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Pedido criado com sucesso'
            ], 201);
        } catch (\Exception $e) {
            Log::error('API Error - Create Order', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pedido'
            ], 500);
        }
    }

    /**
     * Update order (authenticated)
     */
    public function updateOrder($id, Request $request)
    {
        try {
            $user = Auth::user();
            $orderData = $request->all();

            $order = $this->wooCommerceService->updateOrder($user->id, $id, $orderData);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Pedido atualizado com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Update Order', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar pedido'
            ], 500);
        }
    }

    /**
     * Get cart
     */
    public function getCart()
    {
        try {
            $cart = $this->cartService->getCart();

            return response()->json([
                'success' => true,
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar carrinho'
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        try {
            $data = $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'variation_id' => 'nullable|integer',
                'variation' => 'nullable|array'
            ]);

            $cart = $this->cartService->addToCart($data);

            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => 'Item adicionado ao carrinho'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Add to Cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item ao carrinho'
            ], 500);
        }
    }

    /**
     * Update cart item
     */
    public function updateCart(Request $request)
    {
        try {
            $data = $request->validate([
                'item_key' => 'required|string',
                'quantity' => 'required|integer|min:0'
            ]);

            $cart = $this->cartService->updateCartItem($data['item_key'], $data['quantity']);

            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => 'Carrinho atualizado'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Update Cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar carrinho'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($itemKey)
    {
        try {
            $cart = $this->cartService->removeFromCart($itemKey);

            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => 'Item removido do carrinho'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Remove from Cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover item do carrinho'
            ], 500);
        }
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Carrinho limpo'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Clear Cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar carrinho'
            ], 500);
        }
    }

    /**
     * Process checkout
     */
    public function checkout(Request $request)
    {
        try {
            $data = $request->validate([
                'billing' => 'required|array',
                'shipping' => 'required|array',
                'payment_method' => 'required|string',
                'payment_method_title' => 'required|string',
                'customer_note' => 'nullable|string'
            ]);

            $order = $this->wooCommerceService->processCheckout($data);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Checkout processado com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Checkout', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro no checkout'
            ], 500);
        }
    }

    /**
     * Validate checkout data
     */
    public function validateCheckout(Request $request)
    {
        try {
            $data = $request->all();
            $validation = $this->wooCommerceService->validateCheckout($data);

            return response()->json([
                'success' => true,
                'data' => $validation
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Validate Checkout', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro na validação do checkout'
            ], 500);
        }
    }

    /**
     * Get user orders (alias for authenticated endpoint)
     */
    public function userOrders(Request $request)
    {
        return $this->orders($request);
    }

    /**
     * Get user addresses
     */
    public function userAddresses()
    {
        try {
            $user = Auth::user();
            $addresses = $this->wooCommerceService->getUserAddresses($user->id);

            return response()->json([
                'success' => true,
                'data' => $addresses
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get User Addresses', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar endereços'
            ], 500);
        }
    }

    /**
     * Create user address
     */
    public function createAddress(Request $request)
    {
        try {
            $user = Auth::user();
            $addressData = $request->validate([
                'type' => 'required|in:billing,shipping',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'company' => 'nullable|string',
                'address_1' => 'required|string',
                'address_2' => 'nullable|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'postcode' => 'required|string',
                'country' => 'required|string',
                'email' => 'required|email',
                'phone' => 'nullable|string'
            ]);

            $address = $this->wooCommerceService->createUserAddress($user->id, $addressData);

            return response()->json([
                'success' => true,
                'data' => $address,
                'message' => 'Endereço criado com sucesso'
            ], 201);
        } catch (\Exception $e) {
            Log::error('API Error - Create Address', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar endereço'
            ], 500);
        }
    }

    /**
     * Update user address
     */
    public function updateAddress($id, Request $request)
    {
        try {
            $user = Auth::user();
            $addressData = $request->all();

            $address = $this->wooCommerceService->updateUserAddress($user->id, $id, $addressData);

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endereço não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $address,
                'message' => 'Endereço atualizado com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Update Address', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar endereço'
            ], 500);
        }
    }

    /**
     * Delete user address
     */
    public function deleteAddress($id)
    {
        try {
            $user = Auth::user();
            $deleted = $this->wooCommerceService->deleteUserAddress($user->id, $id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endereço não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Endereço removido com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Delete Address', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover endereço'
            ], 500);
        }
    }

    /**
     * Handle webhook for order created
     */
    public function handleOrderCreated(Request $request)
    {
        try {
            $data = $request->all();
            
            // Clear relevant caches
            Cache::forget('api_orders');
            
            Log::info('Webhook: Order created', $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook Error - Order Created', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle webhook for order updated
     */
    public function handleOrderUpdated(Request $request)
    {
        try {
            $data = $request->all();
            
            // Clear relevant caches
            $orderId = $data['id'] ?? null;
            if ($orderId) {
                Cache::forget("api_order_{$orderId}");
            }
            Cache::forget('api_orders');
            
            Log::info('Webhook: Order updated', $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook Error - Order Updated', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle webhook for product update
     */
    public function handleProductUpdate(Request $request)
    {
        try {
            $data = $request->all();
            
            // Clear relevant caches
            $productId = $data['id'] ?? null;
            if ($productId) {
                Cache::forget("api_product_{$productId}");
            }
            Cache::forget('api_products');
            
            Log::info('Webhook: Product updated', $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook Error - Product Update', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }
} 