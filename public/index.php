<?php
/**
 * Homepage - Main entry point for the application
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';

// Check if user is logged in (but don't require it for homepage)
$loggedIn = isLoggedIn();
$firstName = $loggedIn ? getUserFirstName() : '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo APP_NAME; ?> - Home</title>
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        .hero-section {
            background: #bd2440;
            background: linear-gradient(135 def, rgba(189,36,64,1) 0%, rgba(235,160,176,1) 50%, rgba(232,237,83,1) 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-section h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-buttons .btn {
            padding: 15px 40px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .hero-buttons .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: white;
            color: black;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }
    </style>
  </head>
  <body>
    <main class="container">
      <header class="page-header">
        <a href="index.php">Pet Care</a>
        <nav>
          <?php if ($loggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Sign Out</a>
          <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
          <?php endif; ?>
        </nav>
      </header>

      <?php if (!$loggedIn): ?>
      <!-- Hero Section for Non-Logged In Users -->
      <div class="hero-section">
        <h1>Find Your Perfect Pet Companion</h1>
        <p>Connect with loving pets in need of a home. Browse, adopt, and make a difference today.</p>
        <div class="hero-buttons">
          <a href="register.php" class="btn btn-primary">Get Started</a>
          <a href="login.php" class="btn btn-secondary">Sign In</a>
        </div>
      </div>
      <?php else: ?>
      <!-- Welcome Message for Logged In Users -->
      <div class="hero-section">
        <h1>Welcome back, <?php echo htmlspecialchars($firstName); ?>!</h1>
        <p>Ready to find your new best friend?</p>
        <div class="hero-buttons">
          <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        </div>
      </div>
      <?php endif; ?>

      <section>
        <h2 style="text-align: center; margin-bottom: 30px;">Browse Available Pets</h2>

        <div class="selector" role="tablist" aria-label="Choose animal type">
          <button type="button" data-type="dog" class="active">Dogs</button>
          <button type="button" data-type="cat">Cats</button>
          <button type="button" data-type="rabbit">Rabbits</button>
          <button type="button" data-type="bird">Birds</button>
          <button type="button" data-type="other">Others</button>
        </div>

        <div id="animals" class="animals" aria-live="polite">
          <!-- Pet listings will be loaded here via JavaScript -->
        </div>
      </section>
    </main>

    <script src="js/homepage.js"></script>
  </body>
</html>
