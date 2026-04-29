<?php
/**
 * Socrates Blade Route Exporter
 * 
 * Dynamically extracts route definitions from Blogware/Scriptlog CMS
 * for security testing purposes.
 * 
 * Usage: php export_routes.php > routes.json
 * 
 * @version 2.0
 * @requires Blogware/Scriptlog installation with valid config.php
 */

if (php_sapi_name() !== 'cli' && !defined('SCRIPTLOG')) {
    defined('SCRIPTLOG') || die('Direct access not permitted');
}

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

define('SCRIPTLOG', true);

if (!function_exists('app_url')) {
    function app_url() {
        static $appUrl = null;
        
        if ($appUrl !== null) {
            return $appUrl;
        }
        
        $appUrl = '';
        
        $configPath = dirname(__DIR__) . '/config.php';
        if (file_exists($configPath)) {
            $config = @include $configPath;
            if (isset($config['app']['url'])) {
                $appUrl = $config['app']['url'];
            }
        }
        
        return $appUrl;
    }
}

/**
 * Route definitions for Blogware/Scriptlog CMS
 * Extracted from lib/core/Bootstrap.php, api/index.php, and admin/*.php files
 * Last Updated: April 28, 2026
 */
function getBlogwareRoutes() {
    $routes = [];
    
    // Frontend routes (from lib/core/Bootstrap.php)
    $frontendRoutes = [
        'home' => [
            'path' => '/',
            'method' => 'GET',
            'description' => 'Homepage - displays recent posts',
            'parameters' => [],
            'attack_vectors' => ['reflected_xss', 'parameter_pollution']
        ],
        'single' => [
            'path' => '/post/(?<id>\d+)/(?<slug>[\w\-]+)',
            'method' => 'GET',
            'description' => 'Single post view',
            'parameters' => ['id', 'slug'],
            'attack_vectors' => ['idor', 'reflected_xss']
        ],
        'category' => [
            'path' => '/category/(?<category>[\w\-]+)',
            'method' => 'GET',
            'description' => 'Category archive page',
            'parameters' => ['category'],
            'attack_vectors' => ['reflected_xss', 'sqli']
        ],
        'tag' => [
            'path' => '/tag/(?<tag>[\w\- ]+)',
            'method' => 'GET',
            'description' => 'Tag archive page',
            'parameters' => ['tag'],
            'attack_vectors' => ['reflected_xss', 'sqli']
        ],
        'archive' => [
            'path' => '/archive/(?<month>[0-9]{2})/(?<year>[0-9]{4})',
            'method' => 'GET',
            'description' => 'Monthly archive page',
            'parameters' => ['month', 'year'],
            'attack_vectors' => ['reflected_xss', 'sqli']
        ],
        'archives' => [
            'path' => '/archives',
            'method' => 'GET',
            'description' => 'Archive index page',
            'parameters' => [],
            'attack_vectors' => []
        ],
        'blog' => [
            'path' => '/blog([^/]*)',
            'method' => 'GET',
            'description' => 'Blog listing page',
            'parameters' => [],
            'attack_vectors' => ['reflected_xss']
        ],
        'search' => [
            'path' => '/',
            'method' => 'GET',
            'description' => 'Search functionality',
            'parameters' => ['search'],
            'query_param' => true,
            'attack_vectors' => ['reflected_xss', 'sqli']
        ],
        'page' => [
            'path' => '/page/(?<page>[^/]+)',
            'method' => 'GET',
            'description' => 'Static page view',
            'parameters' => ['page'],
            'attack_vectors' => ['idor', 'reflected_xss']
        ],
        'privacy' => [
            'path' => '/privacy',
            'method' => 'GET',
            'description' => 'Privacy policy page',
            'parameters' => [],
            'attack_vectors' => []
        ],
        'download' => [
            'path' => '/download/(?<identifier>[a-f0-9\-]+)',
            'method' => 'GET',
            'description' => 'Secure download page',
            'parameters' => ['identifier'],
            'attack_vectors' => ['idor']
        ],
        'download_file' => [
            'path' => '/download/(?<identifier>[a-f0-9\-]+)/file',
            'method' => 'GET',
            'description' => 'Secure file download',
            'parameters' => ['identifier'],
            'attack_vectors' => ['idor', 'path_traversal']
        ]
    ];
    
    // Admin routes (from admin/*.php files)
    $adminRoutes = [
        // Authentication
        'auth.login' => [
            'path' => '/admin/login.php',
            'method' => 'GET',
            'description' => 'Login page',
            'requires_auth' => false
        ],
        'auth.login_submit' => [
            'path' => '/admin/login.php?load=login',
            'method' => 'POST',
            'description' => 'Login form submission',
            'parameters' => ['username', 'password', 'login_form'],
            'attack_vectors' => ['sqli', 'brute_force', 'auth_bypass'],
            'csrf_protected' => true
        ],
        'auth.logout' => [
            'path' => '/admin/logout.php',
            'method' => 'GET',
            'description' => 'Logout action',
            'requires_auth' => true
        ],
        'auth.forgot_password' => [
            'path' => '/admin/forgot-password.php',
            'method' => 'GET',
            'description' => 'Forgot password page',
            'requires_auth' => false
        ],
        'auth.reset_password' => [
            'path' => '/admin/forgot-password.php?load=reset',
            'method' => 'POST',
            'description' => 'Password reset form',
            'attack_vectors' => ['auth_bypass'],
            'csrf_protected' => true
        ],
        'auth.recover_password' => [
            'path' => '/admin/recover-password.php',
            'method' => 'GET',
            'description' => 'Recover password page',
            'requires_auth' => false
        ],
        'auth.reset_password_alt' => [
            'path' => '/admin/reset-password.php',
            'method' => 'GET',
            'description' => 'Reset password page',
            'requires_auth' => false
        ],
        'auth.signup' => [
            'path' => '/admin/signup.php',
            'method' => 'GET',
            'description' => 'User registration page',
            'requires_auth' => false
        ],
        'auth.activate_user' => [
            'path' => '/admin/activate-user.php',
            'method' => 'GET',
            'description' => 'User activation',
            'requires_auth' => false
        ],
        
        // Dashboard
        'dashboard.index' => [
            'path' => '/admin/dashboard.php',
            'method' => 'GET',
            'description' => 'Admin dashboard',
            'requires_auth' => true
        ],
        
        // Posts
        'posts.list' => [
            'path' => '/admin/posts.php',
            'method' => 'GET',
            'description' => 'Posts management list',
            'requires_auth' => true,
            'attack_vectors' => ['idor']
        ],
        'posts.new' => [
            'path' => '/admin/posts.php?action=new',
            'method' => 'GET',
            'description' => 'New post form',
            'requires_auth' => true
        ],
        'posts.insert' => [
            'path' => '/admin/posts.php?load=insert',
            'method' => 'POST',
            'description' => 'Insert new post',
            'parameters' => ['post_title', 'post_content', 'post_tags', 'topic_id', 'post_status', 'login_form'],
            'attack_vectors' => ['stored_xss', 'csrf', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'posts.edit' => [
            'path' => '/admin/posts.php?action=edit&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Edit post form',
            'parameters' => ['id'],
            'attack_vectors' => ['idor'],
            'requires_auth' => true
        ],
        'posts.update' => [
            'path' => '/admin/posts.php?load=update',
            'method' => 'POST',
            'description' => 'Update existing post',
            'attack_vectors' => ['stored_xss', 'csrf', 'idor', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'posts.delete' => [
            'path' => '/admin/posts.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete post',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Pages
        'pages.list' => [
            'path' => '/admin/pages.php',
            'method' => 'GET',
            'description' => 'Pages management list',
            'requires_auth' => true,
            'attack_vectors' => ['idor']
        ],
        'pages.new' => [
            'path' => '/admin/pages.php?action=new',
            'method' => 'GET',
            'description' => 'New page form',
            'requires_auth' => true
        ],
        'pages.insert' => [
            'path' => '/admin/pages.php?load=insert',
            'method' => 'POST',
            'description' => 'Insert new page',
            'attack_vectors' => ['stored_xss', 'csrf', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'pages.edit' => [
            'path' => '/admin/pages.php?action=edit&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Edit page form',
            'parameters' => ['id'],
            'attack_vectors' => ['idor'],
            'requires_auth' => true
        ],
        'pages.update' => [
            'path' => '/admin/pages.php?load=update',
            'method' => 'POST',
            'description' => 'Update existing page',
            'attack_vectors' => ['stored_xss', 'csrf', 'idor', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'pages.delete' => [
            'path' => '/admin/pages.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete page',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Comments
        'comments.list' => [
            'path' => '/admin/comments.php',
            'method' => 'GET',
            'description' => 'Comments management list',
            'requires_auth' => true
        ],
        'comments.update' => [
            'path' => '/admin/comments.php?load=update',
            'method' => 'POST',
            'description' => 'Update comment status',
            'attack_vectors' => ['csrf', 'idor'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'comments.delete' => [
            'path' => '/admin/comments.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete comment',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Reply
        'reply.list' => [
            'path' => '/admin/reply.php',
            'method' => 'GET',
            'description' => 'Reply management list',
            'requires_auth' => true
        ],
        
        // Users
        'users.list' => [
            'path' => '/admin/users.php',
            'method' => 'GET',
            'description' => 'Users management list',
            'requires_auth' => true
        ],
        'users.new' => [
            'path' => '/admin/users.php?action=new',
            'method' => 'GET',
            'description' => 'New user form',
            'requires_auth' => true
        ],
        'users.insert' => [
            'path' => '/admin/users.php?load=insert',
            'method' => 'POST',
            'description' => 'Insert new user',
            'attack_vectors' => ['csrf', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'users.edit' => [
            'path' => '/admin/users.php?action=edit&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Edit user form',
            'attack_vectors' => ['idor'],
            'requires_auth' => true
        ],
        'users.update' => [
            'path' => '/admin/users.php?load=update',
            'method' => 'POST',
            'description' => 'Update user',
            'attack_vectors' => ['csrf', 'idor', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'users.delete' => [
            'path' => '/admin/users.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete user',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'users.request' => [
            'path' => '/admin/request.php',
            'method' => 'GET',
            'description' => 'User requests/registrations',
            'requires_auth' => true
        ],
        
        // Media
        'media.list' => [
            'path' => '/admin/medialib.php',
            'method' => 'GET',
            'description' => 'Media library',
            'requires_auth' => true
        ],
        'media.upload' => [
            'path' => '/admin/media.php?load=upload',
            'method' => 'POST',
            'description' => 'Upload media file',
            'attack_vectors' => ['upload_rce', 'path_traversal', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'media.delete' => [
            'path' => '/admin/medialib.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete media',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'media.upload_ajax' => [
            'path' => '/admin/media-upload.php',
            'method' => 'POST',
            'description' => 'AJAX media upload (SummerNote)',
            'attack_vectors' => ['upload_rce', 'path_traversal', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Topics
        'topics.list' => [
            'path' => '/admin/topics.php',
            'method' => 'GET',
            'description' => 'Topics/categories list',
            'requires_auth' => true
        ],
        'topics.insert' => [
            'path' => '/admin/topics.php?load=insert',
            'method' => 'POST',
            'description' => 'Insert new topic',
            'attack_vectors' => ['csrf', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'topics.update' => [
            'path' => '/admin/topics.php?load=update',
            'method' => 'POST',
            'description' => 'Update topic',
            'attack_vectors' => ['csrf', 'idor', 'sqli'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'topics.delete' => [
            'path' => '/admin/topics.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete topic',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Menu
        'menu.list' => [
            'path' => '/admin/menu.php',
            'method' => 'GET',
            'description' => 'Navigation menu management',
            'requires_auth' => true
        ],
        'menu.insert' => [
            'path' => '/admin/menu.php?load=insert',
            'method' => 'POST',
            'description' => 'Insert menu item',
            'attack_vectors' => ['csrf', 'sqli', 'xss'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'menu.delete' => [
            'path' => '/admin/menu.php?load=delete&Id=(?<id>\d+)',
            'method' => 'GET',
            'description' => 'Delete menu item',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Plugins
        'plugins.list' => [
            'path' => '/admin/plugins.php',
            'method' => 'GET',
            'description' => 'Plugins management',
            'requires_auth' => true
        ],
        'plugins.activate' => [
            'path' => '/admin/plugins.php?load=activate&plugin=(?<plugin>[^&]+)',
            'method' => 'GET',
            'description' => 'Activate plugin',
            'attack_vectors' => ['idor', 'csrf', 'rce'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'plugins.deactivate' => [
            'path' => '/admin/plugins.php?load=deactivate&plugin=(?<plugin>[^&]+)',
            'method' => 'GET',
            'description' => 'Deactivate plugin',
            'attack_vectors' => ['csrf', 'idor'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Themes
        'themes.list' => [
            'path' => '/admin/templates.php',
            'method' => 'GET',
            'description' => 'Themes management',
            'requires_auth' => true
        ],
        'themes.activate' => [
            'path' => '/admin/templates.php?load=activate&theme=(?<theme>[^&]+)',
            'method' => 'GET',
            'description' => 'Activate theme',
            'attack_vectors' => ['csrf', 'idor'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Import
        'import.page' => [
            'path' => '/admin/import.php',
            'method' => 'GET',
            'description' => 'Import content page',
            'requires_auth' => true
        ],
        'import.preview' => [
            'path' => '/admin/import.php?load=preview',
            'method' => 'POST',
            'description' => 'Preview import content',
            'attack_vectors' => ['xxe', 'stored_xss', 'path_traversal', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        'import.execute' => [
            'path' => '/admin/import.php?load=import',
            'method' => 'POST',
            'description' => 'Execute import',
            'attack_vectors' => ['xxe', 'stored_xss', 'sqli', 'csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Export
        'export.page' => [
            'path' => '/admin/export.php',
            'method' => 'GET',
            'description' => 'Export content page',
            'requires_auth' => true
        ],
        'export.execute' => [
            'path' => '/admin/export.php?load=export',
            'method' => 'POST',
            'description' => 'Execute export',
            'attack_vectors' => ['csrf'],
            'csrf_protected' => true,
            'requires_auth' => true
        ],
        
        // Downloads
        'downloads.list' => [
            'path' => '/admin/downloads.php',
            'method' => 'GET',
            'description' => 'Downloads management',
            'requires_auth' => true
        ],
        
        // Privacy
        'privacy.settings' => [
            'path' => '/admin/privacy.php',
            'method' => 'GET',
            'description' => 'Privacy settings',
            'requires_auth' => true
        ],
        'privacy.policy' => [
            'path' => '/admin/privacy-policy.php',
            'method' => 'GET',
            'description' => 'Privacy policy management',
            'requires_auth' => true
        ],
        
        // Languages & Translations
        'languages.list' => [
            'path' => '/admin/languages.php',
            'method' => 'GET',
            'description' => 'Languages management',
            'requires_auth' => true
        ],
        'translations.list' => [
            'path' => '/admin/translations.php',
            'method' => 'GET',
            'description' => 'Translation editor',
            'requires_auth' => true
        ],
        
        // Configuration Pages
        'config.general' => [
            'path' => '/admin/option-general.php',
            'method' => 'GET',
            'description' => 'General settings',
            'requires_auth' => true
        ],
        'config.reading' => [
            'path' => '/admin/option-reading.php',
            'method' => 'GET',
            'description' => 'Reading settings',
            'requires_auth' => true
        ],
        'config.permalink' => [
            'path' => '/admin/option-permalink.php',
            'method' => 'GET',
            'description' => 'Permalink settings',
            'requires_auth' => true
        ],
        'config.mail' => [
            'path' => '/admin/option-mail.php',
            'method' => 'GET',
            'description' => 'Mail/SMTP settings',
            'requires_auth' => true
        ],
        'config.membership' => [
            'path' => '/admin/option-memberships.php',
            'method' => 'GET',
            'description' => 'Membership settings',
            'requires_auth' => true
        ],
        'config.timezone' => [
            'path' => '/admin/option-timezone.php',
            'method' => 'GET',
            'description' => 'Timezone settings',
            'requires_auth' => true
        ],
        'config.sitemap' => [
            'path' => '/admin/option-sitemap.php',
            'method' => 'GET',
            'description' => 'Sitemap settings',
            'requires_auth' => true
        ],
        'config.downloads' => [
            'path' => '/admin/option-downloads.php',
            'method' => 'GET',
            'description' => 'Download settings',
            'requires_auth' => true
        ],
        'config.api' => [
            'path' => '/admin/option-api.php',
            'method' => 'GET',
            'description' => 'API settings',
            'requires_auth' => true
        ],
        'config.language' => [
            'path' => '/admin/option-language.php',
            'method' => 'GET',
            'description' => 'Language settings',
            'requires_auth' => true
        ],
        
        // Navigation
        'nav.sidebar' => [
            'path' => '/admin/sidebar-nav.php',
            'method' => 'GET',
            'description' => 'Sidebar navigation',
            'requires_auth' => true
        ],
        'nav.menu' => [
            'path' => '/admin/navigation.php',
            'method' => 'GET',
            'description' => 'Navigation menu',
            'requires_auth' => true
        ],
        
        // Error Pages
        'error.403' => [
            'path' => '/admin/403.php',
            'method' => 'GET',
            'description' => '403 Forbidden page',
            'requires_auth' => false
        ],
        'error.404' => [
            'path' => '/admin/404.php',
            'method' => 'GET',
            'description' => '404 Not Found page',
            'requires_auth' => false
        ],
        
        // Captcha
        'captcha.login' => [
            'path' => '/admin/captcha-login.php',
            'method' => 'GET',
            'description' => 'Login captcha',
            'requires_auth' => false
        ],
        'captcha.forgot' => [
            'path' => '/admin/captcha-forgot-pwd.php',
            'method' => 'GET',
            'description' => 'Forgot password captcha',
            'requires_auth' => false
        ],
        
        // Fetch
        'fetch.tags' => [
            'path' => '/admin/fetch-tags.php',
            'method' => 'GET',
            'description' => 'AJAX tags fetch',
            'requires_auth' => false
        ]
    ];
    
    // API routes (from api/index.php)
    $apiRoutes = [
        // API Info
        'api.info' => [
            'path' => '/api/v1',
            'method' => 'GET',
            'description' => 'API information endpoint',
            'requires_auth' => false
        ],
        
        // Posts API
        'api.posts' => [
            'path' => '/api/v1/posts',
            'method' => 'GET',
            'description' => 'API - Get posts',
            'parameters' => ['page', 'per_page', 'status'],
            'attack_vectors' => ['idor', 'sqli'],
            'requires_auth' => false
        ],
        'api.post_single' => [
            'path' => '/api/v1/posts/(?<id>\d+)',
            'method' => 'GET',
            'description' => 'API - Get single post',
            'parameters' => ['id'],
            'attack_vectors' => ['idor'],
            'requires_auth' => false
        ],
        'api.post_comments' => [
            'path' => '/api/v1/posts/(?<id>\d+)/comments',
            'method' => 'GET',
            'description' => 'API - Get post comments',
            'parameters' => ['id'],
            'attack_vectors' => ['sqli'],
            'requires_auth' => false
        ],
        'api.post_store' => [
            'path' => '/api/v1/posts',
            'method' => 'POST',
            'description' => 'API - Create post',
            'attack_vectors' => ['stored_xss', 'sqli'],
            'requires_auth' => true
        ],
        'api.post_update' => [
            'path' => '/api/v1/posts/(?<id>\d+)',
            'method' => 'PUT',
            'description' => 'API - Update post',
            'parameters' => ['id'],
            'attack_vectors' => ['stored_xss', 'sqli', 'idor'],
            'requires_auth' => true
        ],
        'api.post_destroy' => [
            'path' => '/api/v1/posts/(?<id>\d+)',
            'method' => 'DELETE',
            'description' => 'API - Delete post',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'requires_auth' => true
        ],
        
        // Protected Post API
        'api.post_unlock' => [
            'path' => '/api/v1/posts/(?<id>\d+)/unlock',
            'method' => 'POST',
            'description' => 'API - Unlock password-protected post',
            'parameters' => ['id', 'password'],
            'attack_vectors' => ['idor', 'brute_force'],
            'requires_auth' => false
        ],
        'api.post_verify' => [
            'path' => '/api/v1/posts/(?<id>\d+)/verify',
            'method' => 'POST',
            'description' => 'API - Verify password-protected post',
            'parameters' => ['id', 'password'],
            'attack_vectors' => ['brute_force'],
            'requires_auth' => false
        ],
        
        // Media API
        'api.media_upload' => [
            'path' => '/api/v1/media/upload',
            'method' => 'POST',
            'description' => 'API - Upload media file',
            'attack_vectors' => ['upload_rce', 'path_traversal', 'csrf'],
            'requires_auth' => true
        ],
        
        // Categories API
        'api.categories' => [
            'path' => '/api/v1/categories',
            'method' => 'GET',
            'description' => 'API - Get categories',
            'requires_auth' => false
        ],
        'api.category_single' => [
            'path' => '/api/v1/categories/(?<id>\d+)',
            'method' => 'GET',
            'description' => 'API - Get single category',
            'parameters' => ['id'],
            'attack_vectors' => ['idor'],
            'requires_auth' => false
        ],
        'api.category_posts' => [
            'path' => '/api/v1/categories/(?<id>\d+)/posts',
            'method' => 'GET',
            'description' => 'API - Get category posts',
            'parameters' => ['id'],
            'attack_vectors' => ['sqli'],
            'requires_auth' => false
        ],
        'api.category_store' => [
            'path' => '/api/v1/categories',
            'method' => 'POST',
            'description' => 'API - Create category',
            'attack_vectors' => ['stored_xss', 'sqli'],
            'requires_auth' => true
        ],
        'api.category_update' => [
            'path' => '/api/v1/categories/(?<id>\d+)',
            'method' => 'PUT',
            'description' => 'API - Update category',
            'parameters' => ['id'],
            'attack_vectors' => ['stored_xss', 'sqli', 'idor'],
            'requires_auth' => true
        ],
        'api.category_destroy' => [
            'path' => '/api/v1/categories/(?<id>\d+)',
            'method' => 'DELETE',
            'description' => 'API - Delete category',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'requires_auth' => true
        ],
        
        // Comments API
        'api.comments' => [
            'path' => '/api/v1/comments',
            'method' => 'GET',
            'description' => 'API - Get comments',
            'parameters' => ['post_id', 'status'],
            'attack_vectors' => ['sqli'],
            'requires_auth' => false
        ],
        'api.comment_single' => [
            'path' => '/api/v1/comments/(?<id>\d+)',
            'method' => 'GET',
            'description' => 'API - Get single comment',
            'parameters' => ['id'],
            'attack_vectors' => ['idor'],
            'requires_auth' => false
        ],
        'api.comment_store' => [
            'path' => '/api/v1/comments',
            'method' => 'POST',
            'description' => 'API - Create comment',
            'attack_vectors' => ['stored_xss', 'sqli', 'spam'],
            'requires_auth' => false
        ],
        'api.comment_update' => [
            'path' => '/api/v1/comments/(?<id>\d+)',
            'method' => 'PUT',
            'description' => 'API - Update comment',
            'parameters' => ['id'],
            'attack_vectors' => ['stored_xss', 'sqli', 'idor'],
            'requires_auth' => true
        ],
        'api.comment_destroy' => [
            'path' => '/api/v1/comments/(?<id>\d+)',
            'method' => 'DELETE',
            'description' => 'API - Delete comment',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'requires_auth' => true
        ],
        
        // Archives API
        'api.archives' => [
            'path' => '/api/v1/archives',
            'method' => 'GET',
            'description' => 'API - Get archives',
            'requires_auth' => false
        ],
        'api.archive_year' => [
            'path' => '/api/v1/archives/(?<year>[0-9]{4})',
            'method' => 'GET',
            'description' => 'API - Get archives by year',
            'parameters' => ['year'],
            'attack_vectors' => ['sqli'],
            'requires_auth' => false
        ],
        'api.archive_month' => [
            'path' => '/api/v1/archives/(?<year>[0-9]{4})/(?<month>[0-9]{2})',
            'method' => 'GET',
            'description' => 'API - Get archives by month',
            'parameters' => ['year', 'month'],
            'attack_vectors' => ['sqli'],
            'requires_auth' => false
        ],
        
        // Search API
        'api.search' => [
            'path' => '/api/v1/search',
            'method' => 'GET',
            'description' => 'API - Search all content',
            'parameters' => ['q', 'type'],
            'attack_vectors' => ['sqli', 'reflected_xss'],
            'requires_auth' => false
        ],
        'api.search_posts' => [
            'path' => '/api/v1/search/posts',
            'method' => 'GET',
            'description' => 'API - Search posts',
            'parameters' => ['q'],
            'attack_vectors' => ['sqli', 'reflected_xss'],
            'requires_auth' => false
        ],
        'api.search_pages' => [
            'path' => '/api/v1/search/pages',
            'method' => 'GET',
            'description' => 'API - Search pages',
            'parameters' => ['q'],
            'attack_vectors' => ['sqli', 'reflected_xss'],
            'requires_auth' => false
        ],
        
        // GDPR API
        'api.gdpr_consent' => [
            'path' => '/api/v1/gdpr/consent',
            'method' => 'POST',
            'description' => 'API - GDPR consent',
            'parameters' => ['consent_type', 'granted'],
            'attack_vectors' => ['csrf', 'sqli'],
            'requires_auth' => false
        ],
        'api.gdpr_status' => [
            'path' => '/api/v1/gdpr/consent',
            'method' => 'GET',
            'description' => 'API - Get GDPR consent status',
            'requires_auth' => false
        ],
        
        // Languages API
        'api.languages' => [
            'path' => '/api/v1/languages',
            'method' => 'GET',
            'description' => 'API - Get languages',
            'requires_auth' => false
        ],
        'api.languages_active' => [
            'path' => '/api/v1/languages/active',
            'method' => 'GET',
            'description' => 'API - Get active languages',
            'requires_auth' => false
        ],
        'api.languages_default' => [
            'path' => '/api/v1/languages/default',
            'method' => 'GET',
            'description' => 'API - Get default language',
            'requires_auth' => false
        ],
        'api.language_single' => [
            'path' => '/api/v1/languages/(?<code>[a-z]{2})',
            'method' => 'GET',
            'description' => 'API - Get single language',
            'parameters' => ['code'],
            'requires_auth' => false
        ],
        'api.language_store' => [
            'path' => '/api/v1/languages',
            'method' => 'POST',
            'description' => 'API - Create language',
            'attack_vectors' => ['sqli'],
            'requires_auth' => true
        ],
        'api.language_update' => [
            'path' => '/api/v1/languages/(?<code>[a-z]{2})',
            'method' => 'PUT',
            'description' => 'API - Update language',
            'parameters' => ['code'],
            'attack_vectors' => ['sqli'],
            'requires_auth' => true
        ],
        'api.language_destroy' => [
            'path' => '/api/v1/languages/(?<code>[a-z]{2})',
            'method' => 'DELETE',
            'description' => 'API - Delete language',
            'parameters' => ['code'],
            'attack_vectors' => ['idor'],
            'requires_auth' => true
        ],
        'api.language_set_default' => [
            'path' => '/api/v1/languages/(?<code>[a-z]{2})/default',
            'method' => 'PUT',
            'description' => 'API - Set default language',
            'parameters' => ['code'],
            'attack_vectors' => ['idor'],
            'requires_auth' => true
        ],
        
        // Translations API
        'api.translations' => [
            'path' => '/api/v1/translations/(?<code>[a-z]{2})',
            'method' => 'GET',
            'description' => 'API - Get translations',
            'parameters' => ['code'],
            'requires_auth' => false
        ],
        'api.translation_single' => [
            'path' => '/api/v1/translations/(?<code>[a-z]{2})/(?<key>[a-zA-Z0-9._-]+)',
            'method' => 'GET',
            'description' => 'API - Get single translation',
            'parameters' => ['code', 'key'],
            'requires_auth' => false
        ],
        'api.translation_store' => [
            'path' => '/api/v1/translations/(?<code>[a-z]{2})',
            'method' => 'POST',
            'description' => 'API - Create translation',
            'parameters' => ['code'],
            'attack_vectors' => ['stored_xss', 'sqli'],
            'requires_auth' => true
        ],
        'api.translation_update' => [
            'path' => '/api/v1/translations/(?<id>\d+)',
            'method' => 'PUT',
            'description' => 'API - Update translation',
            'parameters' => ['id'],
            'attack_vectors' => ['stored_xss', 'sqli', 'idor'],
            'requires_auth' => true
        ],
        'api.translation_destroy' => [
            'path' => '/api/v1/translations/(?<id>\d+)',
            'method' => 'DELETE',
            'description' => 'API - Delete translation',
            'parameters' => ['id'],
            'attack_vectors' => ['idor', 'csrf'],
            'requires_auth' => true
        ],
        'api.translation_export' => [
            'path' => '/api/v1/translations/(?<code>[a-z]{2})/export',
            'method' => 'GET',
            'description' => 'API - Export translations',
            'parameters' => ['code'],
            'requires_auth' => true
        ],
        'api.translation_import' => [
            'path' => '/api/v1/translations/(?<code>[a-z]{2})/import',
            'method' => 'POST',
            'description' => 'API - Import translations',
            'parameters' => ['code'],
            'attack_vectors' => ['xxe', 'sqli'],
            'requires_auth' => true
        ],
        'api.translation_cache' => [
            'path' => '/api/v1/translations/(?<code>[a-z]{2})/cache',
            'method' => 'POST',
            'description' => 'API - Regenerate translation cache',
            'parameters' => ['code'],
            'requires_auth' => true
        ]
    ];
    
    // Public forms
    $publicRoutes = [
        'public.comment_submit' => [
            'path' => '/comment-submit',
            'method' => 'POST',
            'description' => 'Public comment submission',
            'parameters' => ['post_id', 'author_name', 'author_email', 'comment_content'],
            'attack_vectors' => ['stored_xss', 'sqli', 'spam'],
            'csrf_protected' => false
        ],
        'public.contact' => [
            'path' => '/contact',
            'method' => 'POST',
            'description' => 'Contact form submission',
            'parameters' => ['name', 'email', 'subject', 'message'],
            'attack_vectors' => ['stored_xss', 'sqli', 'email_injection'],
            'csrf_protected' => false
        ],
        'public.subscribe' => [
            'path' => '/subscribe',
            'method' => 'POST',
            'description' => 'Newsletter subscription',
            'parameters' => ['email'],
            'attack_vectors' => ['sqli', 'email_injection'],
            'csrf_protected' => false
        ]
    ];
    
    // Sensitive endpoints
    $sensitiveRoutes = [
        'sensitive.install' => [
            'path' => '/install/',
            'method' => 'GET',
            'description' => 'Installation wizard',
            'attack_vectors' => ['installer_takeover'],
            'expected_after_install' => '404_or_redirect'
        ],
        'sensitive.setup_db' => [
            'path' => '/install/setup-db.php',
            'method' => 'POST',
            'description' => 'Database setup',
            'attack_vectors' => ['sqli', 'config_tampering'],
            'expected_after_install' => '404_or_redirect'
        ],
        'sensitive.finish' => [
            'path' => '/install/finish.php',
            'method' => 'GET',
            'description' => 'Installation finish',
            'attack_vectors' => ['config_tampering'],
            'expected_after_install' => '404_or_redirect'
        ],
        'sensitive.config' => [
            'path' => '/config.php',
            'method' => 'GET',
            'description' => 'Configuration file access',
            'attack_vectors' => ['info_disclosure'],
            'expected_response' => '403_or_empty'
        ],
        'sensitive.readme' => [
            'path' => '/README.md',
            'method' => 'GET',
            'description' => 'README file access',
            'attack_vectors' => ['info_disclosure'],
            'expected_response' => '403_or_empty'
        ],
        'sensitive.env' => [
            'path' => '/.env',
            'method' => 'GET',
            'description' => 'Environment file access',
            'attack_vectors' => ['info_disclosure'],
            'expected_response' => '403_or_empty'
        ]
    ];
    
    // Merge all routes
    $routes = array_merge(
        ['frontend' => $frontendRoutes],
        ['admin' => $adminRoutes],
        ['api' => $apiRoutes],
        ['public' => $publicRoutes],
        ['sensitive' => $sensitiveRoutes]
    );
    
    return $routes;
}

/**
 * Add metadata to the routes export
 */
function getExportMetadata() {
    return [
        'meta' => [
            'name' => 'Blogware/Scriptlog CMS Routes',
            'version' => '2.0',
            'description' => 'Comprehensive route definitions for security testing',
            'generated' => date('Y-m-d H:i:s'),
            'generator' => 'Socrates Blade Route Exporter v2.0',
            'php_version' => PHP_VERSION,
            'url' => function_exists('app_url') ? app_url() : 'unknown'
        ]
    ];
}

// Main execution
try {
    $output = [
        'metadata' => getExportMetadata(),
        'routes' => getBlogwareRoutes()
    ];
    
    // Output as JSON with pretty print
    header('Content-Type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to export routes: ' . $e->getMessage()
    ]);
    exit(1);
}