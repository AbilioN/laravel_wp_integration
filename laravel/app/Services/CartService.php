<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartService
{
    /**
     * Get cart contents
     */
    public function getCart()
    {
        $cart = Session::get('cart', []);
        
        return [
            'items' => $cart,
            'total' => $this->calculateTotal($cart),
            'item_count' => count($cart)
        ];
    }

    /**
     * Add item to cart
     */
    public function addToCart($data)
    {
        try {
            $cart = Session::get('cart', []);
            
            $productId = $data['product_id'];
            $quantity = $data['quantity'];
            $variationId = $data['variation_id'] ?? null;
            $variation = $data['variation'] ?? [];

            // Generate unique key for cart item
            $itemKey = $this->generateItemKey($productId, $variationId, $variation);

            // Get product details
            $product = $this->getProductDetails($productId);
            
            if (!$product) {
                throw new \Exception('Produto não encontrado');
            }

            // Check if item already exists in cart
            if (isset($cart[$itemKey])) {
                $cart[$itemKey]['quantity'] += $quantity;
            } else {
                $cart[$itemKey] = [
                    'product_id' => $productId,
                    'variation_id' => $variationId,
                    'variation' => $variation,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image'] ?? null,
                    'url' => $product['url'] ?? null
                ];
            }

            // Update cart in session
            Session::put('cart', $cart);

            return $this->getCart();
        } catch (\Exception $e) {
            Log::error('CartService Error - Add to Cart', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem($itemKey, $quantity)
    {
        try {
            $cart = Session::get('cart', []);

            if (!isset($cart[$itemKey])) {
                throw new \Exception('Item não encontrado no carrinho');
            }

            if ($quantity <= 0) {
                unset($cart[$itemKey]);
            } else {
                $cart[$itemKey]['quantity'] = $quantity;
            }

            Session::put('cart', $cart);

            return $this->getCart();
        } catch (\Exception $e) {
            Log::error('CartService Error - Update Cart Item', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($itemKey)
    {
        try {
            $cart = Session::get('cart', []);

            if (isset($cart[$itemKey])) {
                unset($cart[$itemKey]);
                Session::put('cart', $cart);
            }

            return $this->getCart();
        } catch (\Exception $e) {
            Log::error('CartService Error - Remove from Cart', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        Session::forget('cart');
    }

    /**
     * Calculate cart total
     */
    private function calculateTotal($cart)
    {
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Generate unique item key
     */
    private function generateItemKey($productId, $variationId = null, $variation = [])
    {
        $key = $productId;
        
        if ($variationId) {
            $key .= '_' . $variationId;
        }
        
        if (!empty($variation)) {
            $key .= '_' . md5(serialize($variation));
        }
        
        return $key;
    }

    /**
     * Get product details
     */
    private function getProductDetails($productId)
    {
        try {
            $wooCommerceService = app(WooCommerceService::class);
            return $wooCommerceService->getProduct($productId);
        } catch (\Exception $e) {
            Log::error('CartService Error - Get Product Details', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validate cart item
     */
    public function validateCartItem($data)
    {
        $errors = [];

        if (empty($data['product_id'])) {
            $errors['product_id'] = 'ID do produto é obrigatório';
        }

        if (empty($data['quantity']) || $data['quantity'] < 1) {
            $errors['quantity'] = 'Quantidade deve ser maior que zero';
        }

        // Check if product exists and is in stock
        if (!empty($data['product_id'])) {
            $product = $this->getProductDetails($data['product_id']);
            
            if (!$product) {
                $errors['product_id'] = 'Produto não encontrado';
            } elseif ($product['stock_status'] === 'outofstock') {
                $errors['product_id'] = 'Produto fora de estoque';
            } elseif (isset($product['stock_quantity']) && $product['stock_quantity'] < $data['quantity']) {
                $errors['quantity'] = 'Quantidade solicitada não disponível em estoque';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get cart summary
     */
    public function getCartSummary()
    {
        $cart = $this->getCart();
        
        return [
            'item_count' => $cart['item_count'],
            'total' => $cart['total'],
            'formatted_total' => 'R$ ' . number_format($cart['total'], 2, ',', '.')
        ];
    }

    /**
     * Check if cart is empty
     */
    public function isCartEmpty()
    {
        $cart = Session::get('cart', []);
        return empty($cart);
    }

    /**
     * Get cart item count
     */
    public function getCartItemCount()
    {
        $cart = Session::get('cart', []);
        return count($cart);
    }

    /**
     * Get cart total quantity
     */
    public function getCartTotalQuantity()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['quantity'];
        }

        return $total;
    }

    /**
     * Apply discount to cart
     */
    public function applyDiscount($discountCode)
    {
        try {
            // Here you would implement discount logic
            // For now, we'll just store the discount code
            Session::put('discount_code', $discountCode);
            
            return [
                'success' => true,
                'message' => 'Código de desconto aplicado',
                'discount_code' => $discountCode
            ];
        } catch (\Exception $e) {
            Log::error('CartService Error - Apply Discount', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erro ao aplicar desconto'
            ];
        }
    }

    /**
     * Remove discount from cart
     */
    public function removeDiscount()
    {
        Session::forget('discount_code');
        
        return [
            'success' => true,
            'message' => 'Desconto removido'
        ];
    }

    /**
     * Get cart with applied discounts
     */
    public function getCartWithDiscounts()
    {
        $cart = $this->getCart();
        $discountCode = Session::get('discount_code');
        
        if ($discountCode) {
            // Here you would calculate discount amount
            $discountAmount = 0; // Calculate based on discount code
            
            $cart['discount_code'] = $discountCode;
            $cart['discount_amount'] = $discountAmount;
            $cart['final_total'] = $cart['total'] - $discountAmount;
        } else {
            $cart['final_total'] = $cart['total'];
        }
        
        return $cart;
    }

    /**
     * Merge guest cart with user cart after login
     */
    public function mergeGuestCart($userId)
    {
        try {
            $guestCart = Session::get('cart', []);
            
            if (empty($guestCart)) {
                return;
            }

            // Here you would implement logic to merge guest cart with user's saved cart
            // For now, we'll just keep the guest cart in session
            
            Log::info('Guest cart merged for user', ['user_id' => $userId, 'items' => count($guestCart)]);
            
        } catch (\Exception $e) {
            Log::error('CartService Error - Merge Guest Cart', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Save cart for user
     */
    public function saveCartForUser($userId)
    {
        try {
            $cart = Session::get('cart', []);
            
            if (empty($cart)) {
                return;
            }

            // Here you would save cart to database for the user
            // For now, we'll just log it
            
            Log::info('Cart saved for user', ['user_id' => $userId, 'items' => count($cart)]);
            
        } catch (\Exception $e) {
            Log::error('CartService Error - Save Cart for User', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Load saved cart for user
     */
    public function loadSavedCart($userId)
    {
        try {
            // Here you would load saved cart from database for the user
            // For now, we'll just return empty cart
            
            Log::info('Saved cart loaded for user', ['user_id' => $userId]);
            
            return $this->getCart();
            
        } catch (\Exception $e) {
            Log::error('CartService Error - Load Saved Cart', ['error' => $e->getMessage()]);
            return $this->getCart();
        }
    }
} 