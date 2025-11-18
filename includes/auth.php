<?php
/**
 * Authentication Helper Functions
 * Provides session management and authentication utilities
 */

require_once __DIR__ . '/config.php';

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 * @param string $redirectTo URL to redirect to after login (optional)
 */
function requireLogin($redirectTo = '') {
    if (!isLoggedIn()) {
        // Store intended destination
        if (!empty($redirectTo)) {
            $_SESSION['redirect_after_login'] = $redirectTo;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }

        redirect('login.php');
    }

    // Check session timeout
    checkSessionTimeout();
}

/**
 * Require user to have a specific role
 * @param string $requiredRole Role required to access the page
 */
function requireRole($requiredRole) {
    requireLogin();

    $userRole = $_SESSION['user_role'] ?? '';

    if ($userRole !== $requiredRole) {
        // User doesn't have required role
        http_response_code(403);
        die("Access denied. You don't have permission to access this page.");
    }
}

/**
 * Check if session has timed out
 * Destroys session if timeout exceeded
 */
function checkSessionTimeout() {
    if (!isset($_SESSION['login_time'])) {
        return;
    }

    $loginTime = $_SESSION['login_time'];
    $currentTime = time();
    $elapsed = $currentTime - $loginTime;

    if ($elapsed > SESSION_LIFETIME) {
        // Session has expired
        logout();
        redirect('login.php?timeout=1');
    }

    // Update login time to extend session
    $_SESSION['login_time'] = $currentTime;
}

/**
 * Logout current user
 * Destroys session and clears session data
 */
function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();
}

/**
 * Get current user's full information from database
 * @return array|null User data or null if not found
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    require_once __DIR__ . '/db_connect.php';

    $userId = getCurrentUserId();
    $conn = createConnection();

    if (!$conn) {
        return null;
    }

    $stmt = $conn->prepare(
        "SELECT user_id, first_name, last_name, user_email, user_phone, user_role, provider_id, created_at
         FROM users
         WHERE user_id = ?"
    );

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->num_rows > 0 ? $result->fetch_assoc() : null;

    $stmt->close();
    closeConnection($conn);

    return $user;
}

/**
 * Check if current user is a provider
 * @return bool True if user is a provider
 */
function isProvider() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'provider';
}

/**
 * Check if current user is an adopter
 * @return bool True if user is an adopter
 */
function isAdopter() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'adopter';
}

/**
 * Get user's first name
 * @return string First name or 'Guest' if not logged in
 */
function getUserFirstName() {
    return $_SESSION['first_name'] ?? 'Guest';
}
?>
