<?php
/**
 * User Registration Handler
 * Handles both GET (display form) and POST (process registration) requests
 */

require_once '../includes/config.php';
require_once '../includes/db_connect.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $role = sanitizeInput($_POST['role'] ?? 'adopter');
    $providerName = sanitizeInput($_POST['providerName'] ?? '');

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($phone) || empty($role)) {
        $error = "All fields are required";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 12) {
        $error = "Password must be at least 12 characters long";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Create database connection
        $conn = createConnection();

        if (!$conn) {
            $error = "Database connection failed. Please try again later.";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Email already registered";
                $stmt->close();
            } else {
                $stmt->close();

                // Handle provider registration
                $providerId = null;
                if ($role === 'provider') {
                    if (empty($providerName)) {
                        $error = "Provider name is required for provider role";
                    } else {
                        // Insert into providers table
                        $stmt = $conn->prepare("INSERT INTO providers (provider_name) VALUES (?)");
                        $stmt->bind_param("s", $providerName);

                        if ($stmt->execute()) {
                            $providerId = $conn->insert_id;
                            $stmt->close();
                        } else {
                            $error = "Error creating provider: " . $stmt->error;
                            $stmt->close();
                        }
                    }
                }

                // If no errors, create the user
                if (empty($error)) {
                    // Hash the password
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    // Insert user into database
                    $stmt = $conn->prepare(
                        "INSERT INTO users (first_name, last_name, user_email, password_hash, user_phone, user_role, provider_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?)"
                    );

                    $stmt->bind_param(
                        "ssssssi",
                        $firstName,
                        $lastName,
                        $email,
                        $passwordHash,
                        $phone,
                        $role,
                        $providerId
                    );

                    if ($stmt->execute()) {
                        $success = "Registration successful! You can now login.";
                        // Clear form data
                        $firstName = $lastName = $email = $phone = $providerName = '';
                    } else {
                        $error = "Registration failed: " . $stmt->error;
                    }

                    $stmt->close();
                }
            }

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
    <title>Register - <?php echo APP_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            color: #dc2626;
            font-size: 32px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .error-message {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .success-message {
            background: #d1fae5;
            border-left: 4px solid #059669;
            color: #065f46;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }

        .success-message a {
            color: #059669;
            font-weight: 600;
            text-decoration: none;
        }

        .success-message a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #dc2626;
            background: white;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23dc2626' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        .login-link a {
            color: #dc2626;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #b91c1c;
            text-decoration: underline;
        }

        #providerNameGroup {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 200px;
            }
        }

        .required {
            color: #dc2626;
        }

        input::placeholder {
            color: #9ca3af;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 26px;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>User Registration</h1>

        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; padding: 10px; margin: 10px 0; background: #ffebee; border-radius: 5px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message" style="color: blue; padding: 10px; margin: 10px 0; background: #e8f5e9; border-radius: 5px;">
                <?php echo htmlspecialchars($success); ?>
                <br><a href="login.php">Click here to login</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password * (minimum 12 characters)</label>
                <input type="password" id="password" name="password" minlength="12" required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password *</label>
                <input type="password" id="confirmPassword" name="confirmPassword" minlength="12" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="adopter" <?php echo (isset($role) && $role === 'adopter') ? 'selected' : ''; ?>>Adopter</option>
                    <option value="provider" <?php echo (isset($role) && $role === 'provider') ? 'selected' : ''; ?>>Provider</option>
                </select>
            </div>

            <div class="form-group" id="providerNameGroup" style="display: none;">
                <label for="providerName">Provider/Organization Name</label>
                <input type="text" id="providerName" name="providerName" value="<?php echo htmlspecialchars($providerName ?? ''); ?>">
            </div>

            <button type="submit" class="btn-submit">Register</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>

    <script>
        // Show/hide provider name field based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const providerGroup = document.getElementById('providerNameGroup');
            const providerInput = document.getElementById('providerName');

            if (this.value === 'provider') {
                providerGroup.style.display = 'block';
                providerInput.required = true;
            } else {
                providerGroup.style.display = 'none';
                providerInput.required = false;
            }
        });

        // Trigger on page load in case of form re-display with errors
        window.addEventListener('load', function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect.value === 'provider') {
                document.getElementById('providerNameGroup').style.display = 'block';
                document.getElementById('providerName').required = true;
            }
        });
    </script>
</body>
</html>
