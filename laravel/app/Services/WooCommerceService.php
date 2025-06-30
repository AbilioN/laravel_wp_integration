<?php

namespace App\Services;

use App\Models\WooCommerceProduct;
use App\Models\WooCommerceOrder;
use App\Models\WooCommerceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WooCommerceService
{
    /**
     * Get products with filters
     */
    public function getProducts($perPage = 12, $category = null, $search = null, $orderBy = 'date', $order = 'desc')
    {
        try {
            $db = DB::connection('wordpress');
            
            $query = $db->table('wp_posts as p')
                ->where('p.post_type', 'product')
                ->where('p.post_status', 'publish');

            // Filter by category
            if ($category) {
                $query->join('wp_term_relationships as tr', 'p.ID', '=', 'tr.object_id')
                      ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                      ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
                      ->where('tt.taxonomy', 'product_cat')
                      ->where('t.slug', $category);
            }

            // Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.post_title', 'like', "%{$search}%")
                      ->orWhere('p.post_content', 'like', "%{$search}%");
                });
            }

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

            $products = $query->limit($perPage)->get();

            return $products->map(function ($product) {
                return $this->formatProduct($product);
            });
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get Products', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get product by ID
     */
    public function getProduct($id)
    {
        try {
            $db = DB::connection('wordpress');
            
            $product = $db->table('wp_posts as p')
                ->where('p.ID', $id)
                ->where('p.post_type', 'product')
                ->where('p.post_status', 'publish')
                ->first();

            if (!$product) {
                return null;
            }

            return $this->formatProduct($product);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get Product', ['error' => $e->getMessage(), 'id' => $id]);
            return null;
        }
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($category, $perPage = 12, $orderBy = 'date', $order = 'desc')
    {
        return $this->getProducts($perPage, $category, null, $orderBy, $order);
    }

    /**
     * Search products
     */
    public function searchProducts($query, $perPage = 12, $category = null)
    {
        return $this->getProducts($perPage, $category, $query);
    }

    /**
     * Get categories
     */
    public function getCategories($perPage = 50, $parent = null)
    {
        try {
            $db = DB::connection('wordpress');
            
            $query = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'product_cat')
                ->where('tt.count', '>', 0);

            if ($parent !== null) {
                $query->where('tt.parent', $parent);
            }

            $categories = $query->orderBy('t.name', 'asc')
                ->limit($perPage)
                ->get(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count', 'tt.parent']);

            return $categories->map(function ($category) {
                return $this->formatCategory($category);
            });
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get Categories', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get category by ID
     */
    public function getCategory($id)
    {
        try {
            $db = DB::connection('wordpress');
            
            $category = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->where('t.term_id', $id)
                ->where('tt.taxonomy', 'product_cat')
                ->first(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count', 'tt.parent']);

            if (!$category) {
                return null;
            }

            return $this->formatCategory($category);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get Category', ['error' => $e->getMessage(), 'id' => $id]);
            return null;
        }
    }

    /**
     * Get user orders
     */
    public function getUserOrders($userId, $perPage = 10, $status = null)
    {
        try {
            $db = DB::connection('wordpress');
            
            $query = $db->table('wp_posts as p')
                ->where('p.post_type', 'shop_order')
                ->where('p.post_author', $userId);

            if ($status) {
                $query->where('p.post_status', $status);
            }

            $orders = $query->orderBy('p.post_date', 'desc')
                ->limit($perPage)
                ->get();

            return $orders->map(function ($order) {
                return $this->formatOrder($order);
            });
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get User Orders', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get user order
     */
    public function getUserOrder($userId, $orderId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $order = $db->table('wp_posts as p')
                ->where('p.ID', $orderId)
                ->where('p.post_type', 'shop_order')
                ->where('p.post_author', $userId)
                ->first();

            if (!$order) {
                return null;
            }

            return $this->formatOrder($order);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get User Order', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create order
     */
    public function createOrder($userId, $orderData)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Create order post
            $orderId = $db->table('wp_posts')->insertGetId([
                'post_author' => $userId,
                'post_date' => now(),
                'post_date_gmt' => now()->utc(),
                'post_content' => '',
                'post_title' => "Order #{$orderId}",
                'post_excerpt' => '',
                'post_status' => 'wc-pending',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_password' => '',
                'post_name' => "order-{$orderId}",
                'to_ping' => '',
                'pinged' => '',
                'post_modified' => now(),
                'post_modified_gmt' => now()->utc(),
                'post_content_filtered' => '',
                'post_parent' => 0,
                'guid' => '',
                'menu_order' => 0,
                'post_type' => 'shop_order',
                'post_mime_type' => '',
                'comment_count' => 0
            ]);

            // Add order meta
            $this->addOrderMeta($orderId, $orderData);

            // Create order items
            $this->createOrderItems($orderId, $orderData['line_items']);

            return $this->getUserOrder($userId, $orderId);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Create Order', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Update order
     */
    public function updateOrder($userId, $orderId, $orderData)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Verify order belongs to user
            $order = $db->table('wp_posts')
                ->where('ID', $orderId)
                ->where('post_author', $userId)
                ->where('post_type', 'shop_order')
                ->first();

            if (!$order) {
                return null;
            }

            // Update order meta
            $this->updateOrderMeta($orderId, $orderData);

            return $this->getUserOrder($userId, $orderId);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Update Order', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Process checkout
     */
    public function processCheckout($checkoutData)
    {
        try {
            $userId = auth()->id();
            
            // Validate cart
            $cart = Session::get('cart', []);
            if (empty($cart)) {
                throw new \Exception('Carrinho vazio');
            }

            // Create order
            $order = $this->createOrder($userId, $checkoutData);

            if (!$order) {
                throw new \Exception('Erro ao criar pedido');
            }

            // Clear cart
            Session::forget('cart');

            return $order;
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Process Checkout', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate checkout data
     */
    public function validateCheckout($checkoutData)
    {
        $errors = [];

        // Validate billing
        if (empty($checkoutData['billing']['first_name'])) {
            $errors['billing.first_name'] = 'Nome é obrigatório';
        }
        if (empty($checkoutData['billing']['last_name'])) {
            $errors['billing.last_name'] = 'Sobrenome é obrigatório';
        }
        if (empty($checkoutData['billing']['email'])) {
            $errors['billing.email'] = 'Email é obrigatório';
        }
        if (empty($checkoutData['billing']['address_1'])) {
            $errors['billing.address_1'] = 'Endereço é obrigatório';
        }
        if (empty($checkoutData['billing']['city'])) {
            $errors['billing.city'] = 'Cidade é obrigatória';
        }
        if (empty($checkoutData['billing']['postcode'])) {
            $errors['billing.postcode'] = 'CEP é obrigatório';
        }

        // Validate shipping
        if (empty($checkoutData['shipping']['first_name'])) {
            $errors['shipping.first_name'] = 'Nome é obrigatório';
        }
        if (empty($checkoutData['shipping']['last_name'])) {
            $errors['shipping.last_name'] = 'Sobrenome é obrigatório';
        }
        if (empty($checkoutData['shipping']['address_1'])) {
            $errors['shipping.address_1'] = 'Endereço é obrigatório';
        }
        if (empty($checkoutData['shipping']['city'])) {
            $errors['shipping.city'] = 'Cidade é obrigatória';
        }
        if (empty($checkoutData['shipping']['postcode'])) {
            $errors['shipping.postcode'] = 'CEP é obrigatório';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get user addresses
     */
    public function getUserAddresses($userId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $addresses = $db->table('wp_usermeta')
                ->where('user_id', $userId)
                ->whereIn('meta_key', [
                    'billing_first_name', 'billing_last_name', 'billing_company',
                    'billing_address_1', 'billing_address_2', 'billing_city',
                    'billing_state', 'billing_postcode', 'billing_country',
                    'billing_email', 'billing_phone',
                    'shipping_first_name', 'shipping_last_name', 'shipping_company',
                    'shipping_address_1', 'shipping_address_2', 'shipping_city',
                    'shipping_state', 'shipping_postcode', 'shipping_country'
                ])
                ->get();

            return $this->formatAddresses($addresses);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Get User Addresses', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Create user address
     */
    public function createUserAddress($userId, $addressData)
    {
        try {
            $db = DB::connection('wordpress');
            
            $type = $addressData['type'];
            $prefix = $type === 'billing' ? 'billing_' : 'shipping_';

            foreach ($addressData as $key => $value) {
                if ($key !== 'type') {
                    $metaKey = $prefix . $key;
                    $db->table('wp_usermeta')->updateOrInsert(
                        ['user_id' => $userId, 'meta_key' => $metaKey],
                        ['meta_value' => $value]
                    );
                }
            }

            return $this->getUserAddresses($userId);
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Create User Address', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Update user address
     */
    public function updateUserAddress($userId, $addressId, $addressData)
    {
        // For simplicity, we'll treat this as creating/updating address
        return $this->createUserAddress($userId, $addressData);
    }

    /**
     * Delete user address
     */
    public function deleteUserAddress($userId, $addressId)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Delete all address meta for the user
            $db->table('wp_usermeta')
                ->where('user_id', $userId)
                ->whereIn('meta_key', [
                    'billing_first_name', 'billing_last_name', 'billing_company',
                    'billing_address_1', 'billing_address_2', 'billing_city',
                    'billing_state', 'billing_postcode', 'billing_country',
                    'billing_email', 'billing_phone',
                    'shipping_first_name', 'shipping_last_name', 'shipping_company',
                    'shipping_address_1', 'shipping_address_2', 'shipping_city',
                    'shipping_state', 'shipping_postcode', 'shipping_country'
                ])
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Delete User Address', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Format product data
     */
    private function formatProduct($product)
    {
        $meta = $this->getPostMeta($product->ID);
        $categories = $this->getProductCategories($product->ID);
        $images = $this->getProductImages($product->ID);
        
        return [
            'id' => $product->ID,
            'name' => $product->post_title,
            'description' => $product->post_content,
            'short_description' => $product->post_excerpt,
            'slug' => $product->post_name,
            'status' => $product->post_status,
            'date' => $product->post_date,
            'modified' => $product->post_modified,
            'price' => $this->getProductPrice($meta),
            'regular_price' => $meta['_regular_price'] ?? null,
            'sale_price' => $meta['_sale_price'] ?? null,
            'stock_status' => $meta['_stock_status'] ?? 'instock',
            'stock_quantity' => $meta['_stock'] ?? null,
            'categories' => $categories,
            'images' => $images,
            'attributes' => $this->getProductAttributes($product->ID),
            'variations' => $this->getProductVariations($product->ID),
            'meta' => $meta,
            'url' => "/products/{$product->ID}",
            'add_to_cart_url' => "/api/v1/woocommerce/cart/add"
        ];
    }

    /**
     * Format category data
     */
    private function formatCategory($category)
    {
        return [
            'id' => $category->term_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'count' => $category->count,
            'parent' => $category->parent,
            'url' => "/category/{$category->slug}"
        ];
    }

    /**
     * Format order data
     */
    private function formatOrder($order)
    {
        $meta = $this->getPostMeta($order->ID);
        
        return [
            'id' => $order->ID,
            'number' => $order->ID,
            'status' => $order->post_status,
            'date' => $order->post_date,
            'modified' => $order->post_modified,
            'total' => $meta['_order_total'] ?? 0,
            'currency' => $meta['_order_currency'] ?? 'BRL',
            'billing' => $this->getOrderBilling($meta),
            'shipping' => $this->getOrderShipping($meta),
            'items' => $this->getOrderItems($order->ID),
            'meta' => $meta,
            'url' => "/orders/{$order->ID}"
        ];
    }

    /**
     * Get post meta
     */
    private function getPostMeta($postId)
    {
        try {
            $db = DB::connection('wordpress');
            
            return $db->table('wp_postmeta')
                ->where('post_id', $postId)
                ->pluck('meta_value', 'meta_key')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get product price
     */
    private function getProductPrice($meta)
    {
        if (isset($meta['_sale_price']) && !empty($meta['_sale_price'])) {
            return $meta['_sale_price'];
        }
        
        return $meta['_regular_price'] ?? 0;
    }

    /**
     * Get product categories
     */
    private function getProductCategories($productId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $categories = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->join('wp_term_relationships as tr', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->where('tr.object_id', $productId)
                ->where('tt.taxonomy', 'product_cat')
                ->get(['t.term_id', 't.name', 't.slug']);

            return $categories->map(function ($category) {
                return [
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get product images
     */
    private function getProductImages($productId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $images = $db->table('wp_posts as p')
                ->join('wp_postmeta as pm', 'p.ID', '=', 'pm.post_id')
                ->where('pm.meta_key', '_product_image_gallery')
                ->where('pm.post_id', $productId)
                ->first();

            if (!$images) {
                return [];
            }

            $imageIds = explode(',', $images->meta_value);
            $productImages = [];

            foreach ($imageIds as $imageId) {
                $image = $db->table('wp_posts')
                    ->where('ID', $imageId)
                    ->where('post_type', 'attachment')
                    ->first();

                if ($image) {
                    $productImages[] = [
                        'id' => $image->ID,
                        'url' => $image->guid,
                        'title' => $image->post_title
                    ];
                }
            }

            return $productImages;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get product attributes
     */
    private function getProductAttributes($productId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $attributes = $db->table('wp_postmeta')
                ->where('post_id', $productId)
                ->where('meta_key', 'like', '_product_attributes_%')
                ->get();

            return $attributes->map(function ($attribute) {
                $data = maybe_unserialize($attribute->meta_value);
                return [
                    'name' => $data['name'] ?? '',
                    'value' => $data['value'] ?? '',
                    'position' => $data['position'] ?? 0,
                    'visible' => $data['is_visible'] ?? false,
                    'variation' => $data['is_variation'] ?? false
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get product variations
     */
    private function getProductVariations($productId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $variations = $db->table('wp_posts')
                ->where('post_parent', $productId)
                ->where('post_type', 'product_variation')
                ->where('post_status', 'publish')
                ->get();

            return $variations->map(function ($variation) {
                $meta = $this->getPostMeta($variation->ID);
                
                return [
                    'id' => $variation->ID,
                    'price' => $meta['_regular_price'] ?? 0,
                    'sale_price' => $meta['_sale_price'] ?? null,
                    'stock_quantity' => $meta['_stock'] ?? null,
                    'attributes' => $this->getVariationAttributes($variation->ID)
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get variation attributes
     */
    private function getVariationAttributes($variationId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $attributes = $db->table('wp_postmeta')
                ->where('post_id', $variationId)
                ->where('meta_key', 'like', 'attribute_%')
                ->get();

            return $attributes->mapWithKeys(function ($attribute) {
                $key = str_replace('attribute_', '', $attribute->meta_key);
                return [$key => $attribute->meta_value];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get order billing
     */
    private function getOrderBilling($meta)
    {
        return [
            'first_name' => $meta['_billing_first_name'] ?? '',
            'last_name' => $meta['_billing_last_name'] ?? '',
            'company' => $meta['_billing_company'] ?? '',
            'address_1' => $meta['_billing_address_1'] ?? '',
            'address_2' => $meta['_billing_address_2'] ?? '',
            'city' => $meta['_billing_city'] ?? '',
            'state' => $meta['_billing_state'] ?? '',
            'postcode' => $meta['_billing_postcode'] ?? '',
            'country' => $meta['_billing_country'] ?? '',
            'email' => $meta['_billing_email'] ?? '',
            'phone' => $meta['_billing_phone'] ?? ''
        ];
    }

    /**
     * Get order shipping
     */
    private function getOrderShipping($meta)
    {
        return [
            'first_name' => $meta['_shipping_first_name'] ?? '',
            'last_name' => $meta['_shipping_last_name'] ?? '',
            'company' => $meta['_shipping_company'] ?? '',
            'address_1' => $meta['_shipping_address_1'] ?? '',
            'address_2' => $meta['_shipping_address_2'] ?? '',
            'city' => $meta['_shipping_city'] ?? '',
            'state' => $meta['_shipping_state'] ?? '',
            'postcode' => $meta['_shipping_postcode'] ?? '',
            'country' => $meta['_shipping_country'] ?? ''
        ];
    }

    /**
     * Get order items
     */
    private function getOrderItems($orderId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $items = $db->table('wp_woocommerce_order_items')
                ->where('order_id', $orderId)
                ->where('order_item_type', 'line_item')
                ->get();

            return $items->map(function ($item) {
                $meta = $this->getOrderItemMeta($item->order_item_id);
                
                return [
                    'id' => $item->order_item_id,
                    'name' => $item->order_item_name,
                    'quantity' => $meta['_qty'] ?? 1,
                    'total' => $meta['_line_total'] ?? 0,
                    'product_id' => $meta['_product_id'] ?? null,
                    'variation_id' => $meta['_variation_id'] ?? null
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get order item meta
     */
    private function getOrderItemMeta($itemId)
    {
        try {
            $db = DB::connection('wordpress');
            
            return $db->table('wp_woocommerce_order_itemmeta')
                ->where('order_item_id', $itemId)
                ->pluck('meta_value', 'meta_key')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Add order meta
     */
    private function addOrderMeta($orderId, $orderData)
    {
        try {
            $db = DB::connection('wordpress');
            
            $metaData = [
                '_order_total' => 0,
                '_order_currency' => 'BRL',
                '_billing_first_name' => $orderData['billing']['first_name'] ?? '',
                '_billing_last_name' => $orderData['billing']['last_name'] ?? '',
                '_billing_company' => $orderData['billing']['company'] ?? '',
                '_billing_address_1' => $orderData['billing']['address_1'] ?? '',
                '_billing_address_2' => $orderData['billing']['address_2'] ?? '',
                '_billing_city' => $orderData['billing']['city'] ?? '',
                '_billing_state' => $orderData['billing']['state'] ?? '',
                '_billing_postcode' => $orderData['billing']['postcode'] ?? '',
                '_billing_country' => $orderData['billing']['country'] ?? '',
                '_billing_email' => $orderData['billing']['email'] ?? '',
                '_billing_phone' => $orderData['billing']['phone'] ?? '',
                '_shipping_first_name' => $orderData['shipping']['first_name'] ?? '',
                '_shipping_last_name' => $orderData['shipping']['last_name'] ?? '',
                '_shipping_company' => $orderData['shipping']['company'] ?? '',
                '_shipping_address_1' => $orderData['shipping']['address_1'] ?? '',
                '_shipping_address_2' => $orderData['shipping']['address_2'] ?? '',
                '_shipping_city' => $orderData['shipping']['city'] ?? '',
                '_shipping_state' => $orderData['shipping']['state'] ?? '',
                '_shipping_postcode' => $orderData['shipping']['postcode'] ?? '',
                '_shipping_country' => $orderData['shipping']['country'] ?? '',
                '_payment_method' => $orderData['payment_method'] ?? '',
                '_payment_method_title' => $orderData['payment_method_title'] ?? ''
            ];

            foreach ($metaData as $key => $value) {
                $db->table('wp_postmeta')->insert([
                    'post_id' => $orderId,
                    'meta_key' => $key,
                    'meta_value' => $value
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Add Order Meta', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update order meta
     */
    private function updateOrderMeta($orderId, $orderData)
    {
        try {
            $db = DB::connection('wordpress');
            
            foreach ($orderData as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $metaKey = "_{$key}_{$subKey}";
                        $db->table('wp_postmeta')
                            ->where('post_id', $orderId)
                            ->where('meta_key', $metaKey)
                            ->update(['meta_value' => $subValue]);
                    }
                } else {
                    $metaKey = "_{$key}";
                    $db->table('wp_postmeta')
                        ->where('post_id', $orderId)
                        ->where('meta_key', $metaKey)
                        ->update(['meta_value' => $value]);
                }
            }
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Update Order Meta', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create order items
     */
    private function createOrderItems($orderId, $lineItems)
    {
        try {
            $db = DB::connection('wordpress');
            
            foreach ($lineItems as $item) {
                $itemId = $db->table('wp_woocommerce_order_items')->insertGetId([
                    'order_id' => $orderId,
                    'order_item_name' => $item['name'] ?? '',
                    'order_item_type' => 'line_item'
                ]);

                // Add item meta
                $itemMeta = [
                    '_qty' => $item['quantity'] ?? 1,
                    '_tax_class' => '',
                    '_product_id' => $item['product_id'] ?? 0,
                    '_variation_id' => $item['variation_id'] ?? 0,
                    '_line_subtotal' => $item['subtotal'] ?? 0,
                    '_line_subtotal_tax' => 0,
                    '_line_total' => $item['total'] ?? 0,
                    '_line_tax' => 0,
                    '_line_tax_data' => serialize(['subtotal' => [], 'total' => []])
                ];

                foreach ($itemMeta as $key => $value) {
                    $db->table('wp_woocommerce_order_itemmeta')->insert([
                        'order_item_id' => $itemId,
                        'meta_key' => $key,
                        'meta_value' => $value
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('WooCommerceService Error - Create Order Items', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Format addresses
     */
    private function formatAddresses($addresses)
    {
        $billing = [];
        $shipping = [];

        foreach ($addresses as $address) {
            $key = $address->meta_key;
            $value = $address->meta_value;

            if (strpos($key, 'billing_') === 0) {
                $field = str_replace('billing_', '', $key);
                $billing[$field] = $value;
            } elseif (strpos($key, 'shipping_') === 0) {
                $field = str_replace('shipping_', '', $key);
                $shipping[$field] = $value;
            }
        }

        return [
            'billing' => $billing,
            'shipping' => $shipping
        ];
    }
} 