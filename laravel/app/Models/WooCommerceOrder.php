<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WooCommerceOrder extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count'
    ];

    /**
     * Get user orders
     */
    public static function getUserOrders($userId, $limit = 10, $status = null)
    {
        $query = self::where('post_type', 'shop_order')
                    ->where('post_author', $userId);

        if ($status) {
            $query->where('post_status', $status);
        }

        return $query->orderBy('post_date', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get order by ID
     */
    public static function getOrder($id)
    {
        return self::where('ID', $id)
                  ->where('post_type', 'shop_order')
                  ->first();
    }

    /**
     * Get order by ID for specific user
     */
    public static function getUserOrder($userId, $orderId)
    {
        return self::where('ID', $orderId)
                  ->where('post_type', 'shop_order')
                  ->where('post_author', $userId)
                  ->first();
    }

    /**
     * Get order meta
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
     * Get all order meta
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
     * Get order total
     */
    public function getTotal()
    {
        return $this->getMeta('_order_total') ?: 0;
    }

    /**
     * Get order currency
     */
    public function getCurrency()
    {
        return $this->getMeta('_order_currency') ?: 'BRL';
    }

    /**
     * Get order status
     */
    public function getStatus()
    {
        return $this->post_status;
    }

    /**
     * Get order billing information
     */
    public function getBilling()
    {
        return [
            'first_name' => $this->getMeta('_billing_first_name'),
            'last_name' => $this->getMeta('_billing_last_name'),
            'company' => $this->getMeta('_billing_company'),
            'address_1' => $this->getMeta('_billing_address_1'),
            'address_2' => $this->getMeta('_billing_address_2'),
            'city' => $this->getMeta('_billing_city'),
            'state' => $this->getMeta('_billing_state'),
            'postcode' => $this->getMeta('_billing_postcode'),
            'country' => $this->getMeta('_billing_country'),
            'email' => $this->getMeta('_billing_email'),
            'phone' => $this->getMeta('_billing_phone')
        ];
    }

    /**
     * Get order shipping information
     */
    public function getShipping()
    {
        return [
            'first_name' => $this->getMeta('_shipping_first_name'),
            'last_name' => $this->getMeta('_shipping_last_name'),
            'company' => $this->getMeta('_shipping_company'),
            'address_1' => $this->getMeta('_shipping_address_1'),
            'address_2' => $this->getMeta('_shipping_address_2'),
            'city' => $this->getMeta('_shipping_city'),
            'state' => $this->getMeta('_shipping_state'),
            'postcode' => $this->getMeta('_shipping_postcode'),
            'country' => $this->getMeta('_shipping_country')
        ];
    }

    /**
     * Get order items
     */
    public function getItems()
    {
        $items = DB::connection('wordpress')
                  ->table('woocommerce_order_items')
                  ->where('order_id', $this->ID)
                  ->where('order_item_type', 'line_item')
                  ->get();

        return $items->map(function ($item) {
            $meta = $this->getItemMeta($item->order_item_id);
            
            return [
                'id' => $item->order_item_id,
                'name' => $item->order_item_name,
                'quantity' => $meta['_qty'] ?? 1,
                'total' => $meta['_line_total'] ?? 0,
                'subtotal' => $meta['_line_subtotal'] ?? 0,
                'product_id' => $meta['_product_id'] ?? null,
                'variation_id' => $meta['_variation_id'] ?? null,
                'tax_class' => $meta['_tax_class'] ?? '',
                'meta' => $meta
            ];
        });
    }

    /**
     * Get order item meta
     */
    private function getItemMeta($itemId)
    {
        return DB::connection('wordpress')
                 ->table('woocommerce_order_itemmeta')
                 ->where('order_item_id', $itemId)
                 ->pluck('meta_value', 'meta_key')
                 ->toArray();
    }

    /**
     * Get payment method
     */
    public function getPaymentMethod()
    {
        return [
            'method' => $this->getMeta('_payment_method'),
            'method_title' => $this->getMeta('_payment_method_title'),
            'transaction_id' => $this->getMeta('_transaction_id')
        ];
    }

    /**
     * Get shipping method
     */
    public function getShippingMethod()
    {
        $shippingItems = DB::connection('wordpress')
                          ->table('woocommerce_order_items')
                          ->where('order_id', $this->ID)
                          ->where('order_item_type', 'shipping')
                          ->get();

        return $shippingItems->map(function ($item) {
            $meta = $this->getItemMeta($item->order_item_id);
            
            return [
                'id' => $item->order_item_id,
                'name' => $item->order_item_name,
                'cost' => $meta['cost'] ?? 0,
                'method_id' => $meta['method_id'] ?? '',
                'meta' => $meta
            ];
        });
    }

    /**
     * Get order notes
     */
    public function getNotes()
    {
        return DB::connection('wordpress')
                 ->table('comments')
                 ->where('comment_post_ID', $this->ID)
                 ->where('comment_type', 'order_note')
                 ->orderBy('comment_date', 'desc')
                 ->get();
    }

    /**
     * Get order customer
     */
    public function getCustomer()
    {
        if (!$this->post_author) {
            return null;
        }

        return DB::connection('wordpress')
                 ->table('users')
                 ->where('ID', $this->post_author)
                 ->first(['ID', 'user_login', 'user_email', 'display_name']);
    }

    /**
     * Check if order is paid
     */
    public function isPaid()
    {
        $paidDate = $this->getMeta('_paid_date');
        return !empty($paidDate);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted()
    {
        return $this->post_status === 'wc-completed';
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled()
    {
        return $this->post_status === 'wc-cancelled';
    }

    /**
     * Check if order is refunded
     */
    public function isRefunded()
    {
        return $this->post_status === 'wc-refunded';
    }

    /**
     * Get order status label
     */
    public function getStatusLabel()
    {
        $statuses = [
            'wc-pending' => 'Pendente',
            'wc-processing' => 'Processando',
            'wc-on-hold' => 'Em espera',
            'wc-completed' => 'Concluído',
            'wc-cancelled' => 'Cancelado',
            'wc-refunded' => 'Reembolsado',
            'wc-failed' => 'Falhou'
        ];

        return $statuses[$this->post_status] ?? $this->post_status;
    }

    /**
     * Get order formatted data
     */
    public function getFormattedData()
    {
        $meta = $this->getAllMeta();
        
        return [
            'id' => $this->ID,
            'number' => $this->ID,
            'status' => $this->post_status,
            'status_label' => $this->getStatusLabel(),
            'date' => $this->post_date,
            'modified' => $this->post_modified,
            'total' => $this->getTotal(),
            'currency' => $this->getCurrency(),
            'billing' => $this->getBilling(),
            'shipping' => $this->getShipping(),
            'payment_method' => $this->getPaymentMethod(),
            'shipping_method' => $this->getShippingMethod(),
            'items' => $this->getItems(),
            'notes' => $this->getNotes(),
            'customer' => $this->getCustomer(),
            'is_paid' => $this->isPaid(),
            'is_completed' => $this->isCompleted(),
            'is_cancelled' => $this->isCancelled(),
            'is_refunded' => $this->isRefunded(),
            'meta' => $meta
        ];
    }

    /**
     * Create order
     */
    public static function createOrder($userId, $orderData)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Create order post
            $orderId = $db->table('posts')->insertGetId([
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
            self::addOrderMeta($orderId, $orderData);

            // Create order items
            if (isset($orderData['line_items'])) {
                self::createOrderItems($orderId, $orderData['line_items']);
            }

            return self::getOrder($orderId);
        } catch (\Exception $e) {
            \Log::error('WooCommerceOrder Error - Create Order', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Add order meta
     */
    private static function addOrderMeta($orderId, $orderData)
    {
        try {
            $db = DB::connection('wordpress');
            
            $metaData = [
                '_order_total' => $orderData['total'] ?? 0,
                '_order_currency' => $orderData['currency'] ?? 'BRL',
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
                $db->table('postmeta')->insert([
                    'post_id' => $orderId,
                    'meta_key' => $key,
                    'meta_value' => $value
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('WooCommerceOrder Error - Add Order Meta', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create order items
     */
    private static function createOrderItems($orderId, $lineItems)
    {
        try {
            $db = DB::connection('wordpress');
            
            foreach ($lineItems as $item) {
                $itemId = $db->table('woocommerce_order_items')->insertGetId([
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
                    $db->table('woocommerce_order_itemmeta')->insert([
                        'order_item_id' => $itemId,
                        'meta_key' => $key,
                        'meta_value' => $value
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('WooCommerceOrder Error - Create Order Items', ['error' => $e->getMessage()]);
        }
    }
} 