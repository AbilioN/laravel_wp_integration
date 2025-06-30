<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WordPressPost;
use App\Models\WordPressMenu;
use App\Services\WordPressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WordPressApiController extends Controller
{
    protected $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    /**
     * Get all pages
     */
    public function pages(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $pages = Cache::remember("api_pages_{$perPage}", 300, function () use ($perPage) {
                return $this->wordPressService->getPages($perPage);
            });

            return response()->json([
                'success' => true,
                'data' => $pages,
                'meta' => [
                    'total' => $pages->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Pages', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar páginas'
            ], 500);
        }
    }

    /**
     * Get specific page by slug
     */
    public function page($slug)
    {
        try {
            $page = Cache::remember("api_page_{$slug}", 300, function () use ($slug) {
                return $this->wordPressService->getPageBySlug($slug);
            });

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Página não encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $page
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Page', ['error' => $e->getMessage(), 'slug' => $slug]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar página'
            ], 500);
        }
    }

    /**
     * Get all posts
     */
    public function posts(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $category = $request->get('category');
            $tag = $request->get('tag');

            $posts = Cache::remember("api_posts_{$perPage}_{$category}_{$tag}", 300, function () use ($perPage, $category, $tag) {
                return $this->wordPressService->getPosts($perPage, $category, $tag);
            });

            return response()->json([
                'success' => true,
                'data' => $posts,
                'meta' => [
                    'total' => $posts->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Posts', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar posts'
            ], 500);
        }
    }

    /**
     * Get specific post by slug
     */
    public function post($slug)
    {
        try {
            $post = Cache::remember("api_post_{$slug}", 300, function () use ($slug) {
                return $this->wordPressService->getPostBySlug($slug);
            });

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Post', ['error' => $e->getMessage(), 'slug' => $slug]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar post'
            ], 500);
        }
    }

    /**
     * Search content
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            $type = $request->get('type', 'all'); // all, posts, pages
            $perPage = $request->get('per_page', 10);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query de busca é obrigatória'
                ], 400);
            }

            $results = $this->wordPressService->search($query, $type, $perPage);

            return response()->json([
                'success' => true,
                'data' => $results,
                'meta' => [
                    'query' => $query,
                    'type' => $type,
                    'total' => $results->count(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Search', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro na busca'
            ], 500);
        }
    }

    /**
     * Get menu by location
     */
    public function menu($location)
    {
        try {
            $menu = Cache::remember("api_menu_{$location}", 300, function () use ($location) {
                return $this->wordPressService->getMenu($location);
            });

            return response()->json([
                'success' => true,
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Menu', ['error' => $e->getMessage(), 'location' => $location]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar menu'
            ], 500);
        }
    }

    /**
     * Get categories
     */
    public function categories(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $categories = Cache::remember("api_categories_{$perPage}", 600, function () use ($perPage) {
                return $this->wordPressService->getCategories($perPage);
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
     * Get tags
     */
    public function tags(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $tags = Cache::remember("api_tags_{$perPage}", 600, function () use ($perPage) {
                return $this->wordPressService->getTags($perPage);
            });

            return response()->json([
                'success' => true,
                'data' => $tags
            ]);
        } catch (\Exception $e) {
            Log::error('API Error - Get Tags', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar tags'
            ], 500);
        }
    }

    /**
     * Handle webhook for post updates
     */
    public function handlePostUpdate(Request $request)
    {
        try {
            $data = $request->all();
            
            // Validate webhook signature if needed
            // $this->validateWebhookSignature($request);
            
            // Clear relevant caches
            $postId = $data['post_id'] ?? null;
            $postSlug = $data['post_name'] ?? null;
            
            if ($postSlug) {
                Cache::forget("api_post_{$postSlug}");
                Cache::forget("api_page_{$postSlug}");
            }
            
            // Clear general caches
            Cache::forget('api_posts_10');
            Cache::forget('api_pages_10');
            
            Log::info('Webhook: Post updated', $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook Error - Post Update', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }
} 