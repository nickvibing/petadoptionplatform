<?php
/**
 * User Dashboard
 * Protected page - requires authentication
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';

// Require user to be logged in
requireLogin();

// Get current user data
$user = getCurrentUser();
$firstName = getUserFirstName();
$role = $_SESSION['user_role'] ?? 'adopter';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(189,36,36,1) 0%, rgba(235,160,191,1) 50%);
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header .user-info span {
            color: #666;
        }

        .header .user-info .role-badge {
            background: #4CAF50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .header .user-info .role-badge.provider {
            background: #2196F3;
        }

        .btn-logout {
            background: #f44336;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-logout:hover {
            background: #d32f2f;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .welcome-card p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .card p {
            color: #666;
            margin-bottom: 20px;
        }

        .card .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .card .btn:hover {
            background: #45a049;
        }

        .card .btn.secondary {
            background: #2196F3;
        }

        .card .btn.secondary:hover {
            background: #1976D2;
        }

        .user-details {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .user-details table {
            width: 100%;
        }

        .user-details td {
            padding: 8px 0;
        }

        .user-details td:first-child {
            font-weight: 600;
            color: #555;
            width: 150px;
        }

        .user-details td:last-child {
            color: #333;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 90px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
            padding: 40px 0;
        }

        .modal-content {
            background: #fff;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            width: 450px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .close {
            float:right;
            font-size: 28px;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95)}
            to { opacity: 1; transform: scale(1)}
        }
        
        .modal-content input,
        .modal-content select,
        .modal-content textarea {
            width: 100%;
            padding: 8px;
            margin: 6px 0 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .submit-btn {
            width: 100%;
            padding: 10px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo APP_NAME; ?></h1>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($firstName); ?></strong></span>
            <span class="role-badge <?php echo $role; ?>"><?php echo htmlspecialchars($role); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Welcome to Your Dashboard!</h2>
            <p>You've successfully logged in to the CSUF Pet Adoption Platform. From here, you can manage your account and
            <?php if ($role === 'provider'): ?>
                list pets for adoption, manage your organization's profile, and connect with potential adopters.
            <?php else: ?>
                browse available pets, save your favorites, and start the adoption process.
            <?php endif; ?>
            </p>

            <div class="user-details">
                <table>
                    <tr>
                        <td>Name:</td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td><?php echo htmlspecialchars($user['user_phone']); ?></td>
                    </tr>
                    <tr>
                        <td>Account Type:</td>
                        <td><?php echo htmlspecialchars(ucfirst($user['user_role'])); ?></td>
                    </tr>
                    <tr>
                        <td>Member Since:</td>
                        <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="dashboard-grid">
            <?php if ($role === 'provider'): ?>
                <!-- Provider Dashboard Cards -->
                <div class="card">
                    <h3>Add New Pet</h3>
                    <p>List a new pet for adoption on the platform.</p>
                    <a href='#' id='openAddPetModal' class="btn">Add Pet</a>
                </div>

                <div class="card">
                    <h3>Manage Pets</h3>
                    <p>View and manage all your listed pets.</p>
                    <a href="index.php" class="btn secondary">View Pets</a>
                </div>

                <div class="card">
                    <h3>Adoption Requests</h3>
                    <p>Review and respond to adoption applications.</p>
                    <a href="#" class="btn" onclick="alert('coming soon: adoption requests'); return false;">View Requests</a>
                </div>
            <?php else: ?>
                <!-- Adopter Dashboard Cards -->
                <div class="card">
                    <h3>Browse Pets</h3>
                    <p>Explore all available pets looking for a loving home.</p>
                    <a href="index.php" class="btn">Browse Now</a>
                </div>

                <div class="card">
                    <h3>My Favorites</h3>
                    <p>View pets you've saved to your favorites list.</p>
                    <a href="#" class="btn secondary" onclick="alert('coming soon: favorites functionality'); return false;">View Favorites</a>
                </div>

                <div class="card">
                    <h3>My Applications</h3>
                    <p>Track the status of your adoption applications.</p>
                    <a href="#" class="btn" onclick="alert('coming soon: applications tracking'); return false;">View Applications</a>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3>Profile Settings</h3>
                <p>Update your account information and preferences.</p>
                <a href="#" class="btn secondary" onclick="alert('coming soon: profile editing'); return false;">Edit Profile</a>
            </div>

            <!-- Pet Modal -->
            <div id="addPetModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>

                    <h2> Add a New Pet </h2>

                    <form action="../includes/addpet.php" method="POST">
                        <label> Pet Name: </label>
                        <input type="text" name="pet_name" required>

                        <label> Pet Species: </label>
                        <select name="pet_type" required>
                            <option value='dog'> Dog </option>
                            <option value='cat'> Cat </option>
                            <option value='rabbit'> Rabbit </option>
                            <option value='bird'> Bird </option>
                            <option value='other'> Other </option>
                        </select>

                        <label> Breed: </label>
                        <input type='text' name ='breed'>

                        <label> Age: </label>
                        <input type='number' name='age'>

                        <label>Gender: </label>
                        <select name='gender'>
                            <option value='male'> Male </option>
                            <option value='female'> Female </option>
                            <option value='unknown'> Unknown </option>
                        </select>

                        <label> Size: </label>
                        <select name='size'>
                            <option value='small'> Small </option>
                            <option value='medium'> Medium </option>
                            <option value='large'> Large </option>
                        </select>

                        <label> Description: </label>
                        <textarea name='description'></textarea>

                        <label> Image URL: </label>
                        <input type='text' name = 'image_url'>

                        <label> Provider ID: </label>
                        <input type='number' name='provider_id' required>

                        <button type='submit' class='submit-btn'> Add Pet </button>
                    </form>
            </div>
            <!-- End Pet modal -->

        </div>
    </div>
    <script>
        const modal = document.getElementById("addPetModal");
        const openBtn = document.getElementById("openAddPetModal");
        const closeBtn = document.querySelector(".close");

    if (openBtn) {
        openBtn.onclick = (e) => {
            e.preventDefault();
            modal.style.display = "block";
        };
    }   

    if (closeBtn) {
        closeBtn.onclick = () => {
            modal.style.display = "none";
        };
    }

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
    </script>
</body>
</html>
