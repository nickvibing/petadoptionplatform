<?php
/**
 * User Login Handler
 * Handles both GET (display form) and POST (process login) requests
 */

require_once '../includes/config.php';
require_once '../includes/db_connect.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

// Handle POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Create database connection
        $conn = createConnection();

        if (!$conn) {
            $error = "Database connection failed. Please try again later.";
        } else {
            // Query database for user
            $stmt = $conn->prepare("SELECT user_id, password_hash, first_name, user_role FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = "Invalid email or password";
            } else {
                $user = $result->fetch_assoc();

                // Verify password
                if (!password_verify($password, $user['password_hash'])) {
                    $error = "Invalid email or password";
                } else {
                    // Login successful!
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['user_role'] = $user['user_role'];
                    $_SESSION['login_time'] = time();

                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    // Redirect to dashboard
                    redirect('dashboard.php');
                }
            }

            $stmt->close();
            closeConnection($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-submit:hover {
            background: #45a049;
        }

        .error-message {
            color: #d32f2f;
            padding: 10px;
            margin: 10px 0;
            background: #ffebee;
            border-radius: 5px;
            text-align: center;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #4CAF50;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="index.php">Back to Home</a></p>
        </div>
    </div>
</body>
</html>
