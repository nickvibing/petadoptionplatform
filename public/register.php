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
    <link rel="stylesheet" href="css/styles.css">
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
            <div class="success-message" style="color: green; padding: 10px; margin: 10px 0; background: #e8f5e9; border-radius: 5px;">
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
