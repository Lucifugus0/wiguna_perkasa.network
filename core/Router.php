<?php
// ==================== ROUTER CLASS ====================
// Handle modern URL routing
// Maps clean URLs to features

class Router {
    private $routes = [];
    private $currentRoute = '';

    public function __construct() {
        $this->currentRoute = $this->getCurrentRoute();
    }

    /**
     * Get current route from URL
     */
    private function getCurrentRoute() {
        $route = $_GET['route'] ?? '';

        // Remove trailing slash
        $route = rtrim($route, '/');

        // If empty, return dashboard
        if (empty($route)) {
            return 'dashboard';
        }

        return $route;
    }

    /**
     * Add a route
     */
    public function add($path, $callback) {
        $this->routes[$path] = $callback;
    }

    /**
     * Dispatch route
     */
    public function dispatch() {
        $route = $this->currentRoute;

        // Check exact match
        if (isset($this->routes[$route])) {
            return call_user_func($this->routes[$route]);
        }

        // Check pattern match (e.g., pelanggan/edit/123)
        foreach ($this->routes as $pattern => $callback) {
            if ($this->matchPattern($pattern, $route)) {
                return call_user_func($callback);
            }
        }

        // Route not found - return 404
        $this->notFound();
    }

    /**
     * Match route pattern
     */
    private function matchPattern($pattern, $route) {
        // Convert pattern to regex
        $regex = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        return preg_match($regex, $route);
    }

    /**
     * Get route parameters
     */
    public function getParams() {
        $parts = explode('/', $this->currentRoute);
        return $parts;
    }

    /**
     * 404 Not Found
     */
    private function notFound() {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Route: {$this->currentRoute}</p>";
        exit;
    }

    /**
     * Generate URL
     */
    public static function url($path = '') {
        $baseUrl = rtrim($_SERVER['SCRIPT_NAME'], 'index.php');
        return $baseUrl . ltrim($path, '/');
    }

    /**
     * Redirect to URL
     */
    public static function redirect($path) {
        header('Location: ' . self::url($path));
        exit;
    }
}
