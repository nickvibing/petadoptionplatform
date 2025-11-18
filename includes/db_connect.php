<?php
/**
 * Database Connection
 * Establishes connection to MySQL database using mysqli
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        die("Error: .env file not found. Please copy .env.example to .env and configure it.");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Set as environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Load environment variables
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);

/**
 * Create database connection
 * @return mysqli|null Database connection object or null on failure
 */
function createConnection() {
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $dbname = getenv('DB_NAME') ?: 'pet_adoption_db';

    // Create connection
    $conn = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }

    // Set charset to utf8mb4 for proper unicode support
    $conn->set_charset("utf8mb4");

    return $conn;
}

/**
 * Close database connection
 * @param mysqli $conn Database connection to close
 */
function closeConnection($conn) {
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}
?>
