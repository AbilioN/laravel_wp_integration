<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PWAController extends Controller
{
    /**
     * Show PWA status and information
     */
    public function status()
    {
        $pwaInfo = [
            'name' => 'WordPress Laravel Integration',
            'short_name' => 'WP Laravel',
            'version' => '1.0.0',
            'manifest_url' => url('/manifest.json'),
            'sw_url' => url('/sw.js'),
            'offline_support' => true,
            'installable' => true,
            'features' => [
                'Offline Support',
                'Push Notifications',
                'Background Sync',
                'App-like Experience'
            ]
        ];

        return view('pwa.status', compact('pwaInfo'));
    }

    /**
     * Handle push notification subscription
     */
    public function subscribe(Request $request)
    {
        try {
            $subscription = $request->input('subscription');
            $userId = Auth::check() ? Auth::id() : 'guest';
            
            // Store subscription in cache or database
            Cache::put('push_subscription_' . $userId, $subscription, now()->addDays(30));
            
            Log::info('Push notification subscription saved', [
                'user_id' => $userId,
                'subscription' => $subscription
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription saved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Push subscription error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save subscription'
            ], 500);
        }
    }

    /**
     * Send push notification
     */
    public function sendNotification(Request $request)
    {
        try {
            $title = $request->input('title', 'Nova notificação');
            $body = $request->input('body', 'Você tem uma nova notificação');
            $url = $request->input('url', '/');
            $userId = Auth::check() ? Auth::id() : 'guest';

            // Get subscription from cache
            $subscription = Cache::get('push_subscription_' . $userId);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subscription found'
                ], 404);
            }

            // Here you would implement the actual push notification sending
            // For now, we'll just log it
            Log::info('Push notification would be sent', [
                'title' => $title,
                'body' => $body,
                'url' => $url,
                'subscription' => $subscription
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Send notification error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification'
            ], 500);
        }
    }

    /**
     * Get PWA manifest
     */
    public function manifest()
    {
        $manifest = [
            'name' => 'WordPress Laravel Integration',
            'short_name' => 'WP Laravel',
            'description' => 'Integração WordPress com Laravel',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#007bff',
            'orientation' => 'portrait-primary',
            'scope' => '/',
            'lang' => 'pt-BR',
            'icons' => [
                [
                    'src' => '/icons/icon-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-96x96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-128x128.png',
                    'sizes' => '128x128',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-144x144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-152x152.png',
                    'sizes' => '152x152',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-384x384.png',
                    'sizes' => '384x384',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/icons/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ]
            ],
            'categories' => ['business', 'productivity']
        ];

        return response()->json($manifest);
    }

    /**
     * Clear PWA cache
     */
    public function clearCache()
    {
        try {
            // Clear Laravel cache
            Cache::flush();
            
            Log::info('PWA cache cleared');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Clear cache error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
} 