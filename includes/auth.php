<?php
// auth.php - Authentication Functions
require_once __DIR__ . '/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['username']);
}

function is_seller() {
    return is_logged_in() && $_SESSION['role'] === 'seller';
}

function is_buyer() {
    return is_logged_in() && $_SESSION['role'] === 'buyer';
}

function redirect($url) {
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit();
    }
    echo '<script>window.location.href="' . $url . '";</script>';
    exit();
}

function login_user($user_id, $username, $role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['last_activity'] = time();
    session_regenerate_id(true);
}

function logout_user() {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    session_destroy();
}

function check_session_timeout() {
    if (is_logged_in() && (time() - $_SESSION['last_activity'] > 3600)) {
        logout_user();
        redirect('login.php?timeout=1');
    }
    $_SESSION['last_activity'] = time();
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

// Auto checks on include
check_session_timeout();
generate_csrf_token();
