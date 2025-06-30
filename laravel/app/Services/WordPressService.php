<?php

namespace App\Services;

use App\Models\WordPressPost;
use App\Models\WordPressMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WordPressService
{
    /**
     * Get pages with pagination
     */
    public function getPages($perPage = 10, $page = 1)
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $pages = WordPressPost::where('post_type', 'page')
                ->where('post_status', 'publish')
                ->orderBy('menu_order', 'asc')
                ->orderBy('post_title', 'asc')
                ->skip($offset)
                ->take($perPage)
                ->get()
                ->map(function ($page) {
                    return $this->formatPage($page);
                });

            return $pages;
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Pages', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get page by slug
     */
    public function getPageBySlug($slug)
    {
        try {
            $page = WordPressPost::where('post_type', 'page')
                ->where('post_status', 'publish')
                ->where('post_name', $slug)
                ->first();

            if (!$page) {
                return null;
            }

            return $this->formatPage($page);
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Page by Slug', ['error' => $e->getMessage(), 'slug' => $slug]);
            return null;
        }
    }

    /**
     * Get posts with filters
     */
    public function getPosts($perPage = 10, $category = null, $tag = null, $page = 1)
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $query = WordPressPost::where('post_type', 'post')
                ->where('post_status', 'publish');

            // Filter by category
            if ($category) {
                $query->whereHas('terms', function ($q) use ($category) {
                    $q->where('taxonomy', 'category')
                      ->where('slug', $category);
                });
            }

            // Filter by tag
            if ($tag) {
                $query->whereHas('terms', function ($q) use ($tag) {
                    $q->where('taxonomy', 'post_tag')
                      ->where('slug', $tag);
                });
            }

            $posts = $query->orderBy('post_date', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get()
                ->map(function ($post) {
                    return $this->formatPost($post);
                });

            return $posts;
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Posts', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get post by slug
     */
    public function getPostBySlug($slug)
    {
        try {
            $post = WordPressPost::where('post_type', 'post')
                ->where('post_status', 'publish')
                ->where('post_name', $slug)
                ->first();

            if (!$post) {
                return null;
            }

            return $this->formatPost($post);
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Post by Slug', ['error' => $e->getMessage(), 'slug' => $slug]);
            return null;
        }
    }

    /**
     * Search content
     */
    public function search($query, $type = 'all', $perPage = 10)
    {
        try {
            $db = DB::connection('wordpress');
            
            $sql = "SELECT * FROM {$db->getTablePrefix()}posts 
                    WHERE post_status = 'publish' 
                    AND (post_title LIKE ? OR post_content LIKE ?)";
            
            $params = ["%{$query}%", "%{$query}%"];

            if ($type !== 'all') {
                $sql .= " AND post_type = ?";
                $params[] = $type;
            }

            $sql .= " ORDER BY post_date DESC LIMIT ?";
            $params[] = $perPage;

            $results = $db->select($sql, $params);

            return collect($results)->map(function ($item) {
                if ($item->post_type === 'post') {
                    return $this->formatPost((object) $item);
                } else {
                    return $this->formatPage((object) $item);
                }
            });
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Search', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get menu by location
     */
    public function getMenu($location)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Get menu ID by location
            $menuId = $db->table('wp_term_relationships as tr')
                ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
                ->where('tt.taxonomy', 'nav_menu')
                ->where('t.slug', $location)
                ->value('tr.object_id');

            if (!$menuId) {
                return [];
            }

            // Get menu items
            $menuItems = $db->table('wp_posts as p')
                ->join('wp_postmeta as pm', 'p.ID', '=', 'pm.post_id')
                ->where('p.post_type', 'nav_menu_item')
                ->where('p.post_status', 'publish')
                ->where('pm.meta_key', '_menu_item_menu_item_parent')
                ->where('pm.meta_value', '0')
                ->orderBy('p.menu_order', 'asc')
                ->get();

            return $this->formatMenuItems($menuItems);
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Menu', ['error' => $e->getMessage(), 'location' => $location]);
            return [];
        }
    }

    /**
     * Get categories
     */
    public function getCategories($perPage = 50)
    {
        try {
            $db = DB::connection('wordpress');
            
            $categories = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'category')
                ->where('tt.count', '>', 0)
                ->orderBy('t.name', 'asc')
                ->limit($perPage)
                ->get(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count']);

            return $categories->map(function ($category) {
                return [
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'count' => $category->count,
                    'url' => "/category/{$category->slug}"
                ];
            });
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Categories', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get tags
     */
    public function getTags($perPage = 50)
    {
        try {
            $db = DB::connection('wordpress');
            
            $tags = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'post_tag')
                ->where('tt.count', '>', 0)
                ->orderBy('t.name', 'asc')
                ->limit($perPage)
                ->get(['t.term_id', 't.name', 't.slug', 't.description', 'tt.count']);

            return $tags->map(function ($tag) {
                return [
                    'id' => $tag->term_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'description' => $tag->description,
                    'count' => $tag->count,
                    'url' => "/tag/{$tag->slug}"
                ];
            });
        } catch (\Exception $e) {
            Log::error('WordPressService Error - Get Tags', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Format page data
     */
    private function formatPage($page)
    {
        $meta = $this->getPostMeta($page->ID);
        
        return [
            'id' => $page->ID,
            'title' => $page->post_title,
            'content' => $page->post_content,
            'excerpt' => $page->post_excerpt,
            'slug' => $page->post_name,
            'status' => $page->post_status,
            'type' => $page->post_type,
            'date' => $page->post_date,
            'modified' => $page->post_modified,
            'author' => $this->getAuthor($page->post_author),
            'meta' => $meta,
            'url' => "/pages/{$page->post_name}",
            'featured_image' => $this->getFeaturedImage($page->ID, $meta),
            'seo' => $this->getSEOData($meta)
        ];
    }

    /**
     * Format post data
     */
    private function formatPost($post)
    {
        $meta = $this->getPostMeta($post->ID);
        $categories = $this->getPostCategories($post->ID);
        $tags = $this->getPostTags($post->ID);
        
        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'slug' => $post->post_name,
            'status' => $post->post_status,
            'type' => $post->post_type,
            'date' => $post->post_date,
            'modified' => $post->post_modified,
            'author' => $this->getAuthor($post->post_author),
            'categories' => $categories,
            'tags' => $tags,
            'meta' => $meta,
            'url' => "/posts/{$post->post_name}",
            'featured_image' => $this->getFeaturedImage($post->ID, $meta),
            'seo' => $this->getSEOData($meta),
            'comment_count' => $this->getCommentCount($post->ID)
        ];
    }

    /**
     * Get post meta data
     */
    private function getPostMeta($postId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $meta = $db->table('postmeta')
                ->where('post_id', $postId)
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            return $meta;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get author data
     */
    private function getAuthor($authorId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $author = $db->table('users')
                ->where('ID', $authorId)
                ->first(['ID', 'display_name', 'user_email', 'user_url']);

            if (!$author) {
                return null;
            }

            return [
                'id' => $author->ID,
                'name' => $author->display_name,
                'email' => $author->user_email,
                'url' => $author->user_url
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get featured image
     */
    private function getFeaturedImage($postId, $meta)
    {
        $thumbnailId = $meta['_thumbnail_id'] ?? null;
        
        if (!$thumbnailId) {
            return null;
        }

        try {
            $db = DB::connection('wordpress');
            
            $image = $db->table('posts')
                ->where('ID', $thumbnailId)
                ->where('post_type', 'attachment')
                ->first();

            if (!$image) {
                return null;
            }

            $imageMeta = $this->getPostMeta($thumbnailId);
            
            return [
                'id' => $image->ID,
                'url' => $image->guid,
                'title' => $image->post_title,
                'alt' => $imageMeta['_wp_attachment_image_alt'] ?? '',
                'sizes' => $this->getImageSizes($imageMeta)
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get image sizes
     */
    private function getImageSizes($imageMeta)
    {
        $sizes = [];
        
        if (isset($imageMeta['_wp_attachment_metadata'])) {
            $metadata = maybe_unserialize($imageMeta['_wp_attachment_metadata']);
            
            if (isset($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $size => $data) {
                    $sizes[$size] = [
                        'url' => $data['file'],
                        'width' => $data['width'],
                        'height' => $data['height']
                    ];
                }
            }
        }

        return $sizes;
    }

    /**
     * Get post categories
     */
    private function getPostCategories($postId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $categories = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->join('wp_term_relationships as tr', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->where('tr.object_id', $postId)
                ->where('tt.taxonomy', 'category')
                ->get(['t.term_id', 't.name', 't.slug', 't.description']);

            return $categories->map(function ($category) {
                return [
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'url' => "/category/{$category->slug}"
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get post tags
     */
    private function getPostTags($postId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $tags = $db->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->join('wp_term_relationships as tr', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->where('tr.object_id', $postId)
                ->where('tt.taxonomy', 'post_tag')
                ->get(['t.term_id', 't.name', 't.slug', 't.description']);

            return $tags->map(function ($tag) {
                return [
                    'id' => $tag->term_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'description' => $tag->description,
                    'url' => "/tag/{$tag->slug}"
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get comment count
     */
    private function getCommentCount($postId)
    {
        try {
            $db = DB::connection('wordpress');
            
            return $db->table('comments')
                ->where('comment_post_ID', $postId)
                ->where('comment_approved', '1')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get SEO data
     */
    private function getSEOData($meta)
    {
        return [
            'title' => $meta['_yoast_wpseo_title'] ?? null,
            'description' => $meta['_yoast_wpseo_metadesc'] ?? null,
            'keywords' => $meta['_yoast_wpseo_focuskw'] ?? null,
            'canonical' => $meta['_yoast_wpseo_canonical'] ?? null,
            'robots' => $meta['_yoast_wpseo_meta-robots-noindex'] ?? null
        ];
    }

    /**
     * Format menu items
     */
    private function formatMenuItems($menuItems)
    {
        return $menuItems->map(function ($item) {
            $meta = $this->getPostMeta($item->ID);
            
            return [
                'id' => $item->ID,
                'title' => $item->post_title,
                'url' => $meta['_menu_item_url'] ?? '',
                'target' => $meta['_menu_item_target'] ?? '',
                'classes' => $meta['_menu_item_classes'] ?? [],
                'order' => $item->menu_order,
                'children' => $this->getMenuChildren($item->ID)
            ];
        });
    }

    /**
     * Get menu children
     */
    private function getMenuChildren($parentId)
    {
        try {
            $db = DB::connection('wordpress');
            
            $children = $db->table('wp_posts as p')
                ->join('wp_postmeta as pm', 'p.ID', '=', 'pm.post_id')
                ->where('p.post_type', 'nav_menu_item')
                ->where('p.post_status', 'publish')
                ->where('pm.meta_key', '_menu_item_menu_item_parent')
                ->where('pm.meta_value', $parentId)
                ->orderBy('p.menu_order', 'asc')
                ->get();

            return $this->formatMenuItems($children);
        } catch (\Exception $e) {
            return collect();
        }
    }
} 