<?php
/**
 * Logout Handler
 * Destroys user session and redirects to homepage
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';

// Perform logout
logout();

// Redirect to homepage with logout success message
redirect('index.php?logged_out=1');
?>
