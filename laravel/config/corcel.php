<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Connection name
    |--------------------------------------------------------------------------
    |
    | Here you may define which of the database connections below you wish
    | to use as your default connection for all Corcel work. Of course
    | you may use many connections at once using the Corcel library.
    |
    */

    'connection' => env('CORCEL_CONNECTION', 'wordpress'),

    /*
    |--------------------------------------------------------------------------
    | WordPress Database Tables
    |--------------------------------------------------------------------------
    |
    | Here you can define the table names used in your WordPress database.
    |
    */

    'tables' => [
        'posts' => env('CORCEL_TABLE_POSTS', 'wp_posts'),
        'postmeta' => env('CORCEL_TABLE_POSTMETA', 'wp_postmeta'),
        'comments' => env('CORCEL_TABLE_COMMENTS', 'wp_comments'),
        'commentmeta' => env('CORCEL_TABLE_COMMENTMETA', 'wp_commentmeta'),
        'terms' => env('CORCEL_TABLE_TERMS', 'wp_terms'),
        'termmeta' => env('CORCEL_TABLE_TERMMETA', 'wp_termmeta'),
        'term_relationships' => env('CORCEL_TABLE_TERM_RELATIONSHIPS', 'wp_term_relationships'),
        'term_taxonomy' => env('CORCEL_TABLE_TERM_TAXONOMY', 'wp_term_taxonomy'),
        'users' => env('CORCEL_TABLE_USERS', 'wp_users'),
        'usermeta' => env('CORCEL_TABLE_USERMETA', 'wp_usermeta'),
        'options' => env('CORCEL_TABLE_OPTIONS', 'wp_options'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WordPress Database Prefix
    |--------------------------------------------------------------------------
    |
    | Here you can define the table prefix used in your WordPress database.
    |
    */

    'prefix' => env('CORCEL_PREFIX', 'wp_'),

    /*
    |--------------------------------------------------------------------------
    | WordPress Site URL
    |--------------------------------------------------------------------------
    |
    | Here you can define the WordPress site URL.
    |
    */

    'site_url' => env('CORCEL_SITE_URL', 'http://localhost:8080'),

    /*
    |--------------------------------------------------------------------------
    | WordPress Admin URL
    |--------------------------------------------------------------------------
    |
    | Here you can define the WordPress admin URL.
    |
    */

    'admin_url' => env('CORCEL_ADMIN_URL', 'http://localhost:8080/wp-admin'),

    /*
    |--------------------------------------------------------------------------
    | WordPress Upload URL
    |--------------------------------------------------------------------------
    |
    | Here you can define the WordPress upload URL.
    |
    */

    'upload_url' => env('CORCEL_UPLOAD_URL', 'http://localhost:8080/wp-content/uploads'),

    /*
    |--------------------------------------------------------------------------
    | WordPress Upload Path
    |--------------------------------------------------------------------------
    |
    | Here you can define the WordPress upload path.
    |
    */

    'upload_path' => env('CORCEL_UPLOAD_PATH', '/var/www/html/wp-content/uploads'),

    /*
    |--------------------------------------------------------------------------
    | WordPress Post Types
    |--------------------------------------------------------------------------
    |
    | Here you can define the post types you want to use.
    |
    */

    'post_types' => [
        'post' => \Corcel\Model\Post::class,
        'page' => \Corcel\Model\Page::class,
        'product' => \Corcel\Model\Post::class,
        'attachment' => \Corcel\Model\Attachment::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | WordPress Taxonomies
    |--------------------------------------------------------------------------
    |
    | Here you can define the taxonomies you want to use.
    |
    */

    'taxonomies' => [
        'category' => \Corcel\Model\Taxonomy::class,
        'post_tag' => \Corcel\Model\Taxonomy::class,
        'product_cat' => \Corcel\Model\Taxonomy::class,
        'product_tag' => \Corcel\Model\Taxonomy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | WordPress Options
    |--------------------------------------------------------------------------
    |
    | Here you can define the options you want to use.
    |
    */

    'options' => [
        'blogname' => 'Blog Name',
        'blogdescription' => 'Blog Description',
        'siteurl' => 'Site URL',
        'home' => 'Home URL',
        'admin_email' => 'Admin Email',
        'users_can_register' => 'Users Can Register',
        'default_role' => 'Default Role',
    ],

]; 