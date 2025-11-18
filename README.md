# CPSC 362: Foundations of Software Engineering - Team NaN

CSUF Pet Adoption Platform

## Description

This platform serves to help connect pet-lovers with foster pets. You can find a pet to adopt by type of pet, or offer your pet for adoption.

**Tech Stack:**
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB

## Features

- ✅ User Registration (Adopters & Providers)
- ✅ User Login with Session Management
- ✅ Protected Dashboard
- ✅ Role-Based Access Control
- 🚧 Pet Listings (Coming Soon)
- 🚧 Adoption Applications (Coming Soon)

---

## Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

1. **XAMPP** (includes PHP, MySQL, and Apache)
   - Download from: https://www.apachefriends.org/
   - Includes everything you need!

2. **Web Browser** (Chrome, Firefox, Safari, etc.)

3. **Git** (for version control)

---

### Installation & Setup

#### Step 1: Clone the Repository

```bash
git clone https://github.com/nickvibing/petadoptionplatform.git
cd petadoptionplatform
```

#### Step 2: Move Project to XAMPP htdocs

Copy or move your project folder to XAMPP's `htdocs` directory:

**On macOS:**
```bash
cp -r /path/to/petadoptionplatform /Applications/XAMPP/xamppfiles/htdocs/
```

**On Windows:**
```
Copy the petadoptionplatform folder to: C:\xampp\htdocs\
```

#### Step 3: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Start **Apache** (web server)
3. Start **MySQL** (database)

#### Step 4: Create Database

**Option A: Using phpMyAdmin (Recommended)**

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Click "Choose File" and select `database/schema.sql` from the project
4. Click "Go" to import

**Option B: Using MySQL Command Line**

```bash
# From the XAMPP MySQL command line or terminal:
mysql -u root -p < database/schema.sql
```

When prompted for password, press Enter (default XAMPP has no password)

#### Step 5: Configure Environment Variables

The `.env` file is already created with default XAMPP settings:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=pet_adoption_db
```

**If your MySQL settings are different**, edit the `.env` file in the project root.

#### Step 6: Access the Application

Open your browser and navigate to:

```
http://localhost/petadoptionplatform/public/
```

**That's it!** You should see the homepage.

---

## Project Structure

```
petadoptionplatform/
├── public/                   # Web-accessible files (DOCUMENT ROOT)
│   ├── index.php            # Homepage
│   ├── login.php            # Login page & handler
│   ├── register.php         # Registration page & handler
│   ├── dashboard.php        # User dashboard (protected)
│   ├── logout.php           # Logout handler
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── images/              # Image assets
│
├── includes/                # Backend PHP files (not web-accessible)
│   ├── config.php          # Application configuration
│   ├── db_connect.php      # Database connection
│   └── auth.php            # Authentication helpers
│
├── templates/               # HTML templates
│   ├── homepage.html
│   ├── login.html
│   └── userRegistration.html
│
├── database/                # Database files
│   ├── schema.sql          # Database schema (IMPORT THIS!)
│   └── db_connect.py       # Old Python connection (deprecated)
│
├── .env                     # Environment variables (DO NOT commit to git)
├── .env.example            # Example environment file
└── README.md               # This file
```

---

## Usage

### Register a New Account

1. Go to `http://localhost/petadoptionplatform/public/register.php`
2. Fill in your information
3. Choose role: **Adopter** or **Provider**
   - **Adopter:** Looking to adopt a pet
   - **Provider:** Shelter/organization listing pets
4. Click "Register"

### Login

1. Go to `http://localhost/petadoptionplatform/public/login.php`
2. Enter your email and password
3. Click "Login"
4. You'll be redirected to your dashboard

### Dashboard

After logging in, you can:
- View your profile information
- Access role-specific features
- Update account settings (coming soon)
- Browse pets (coming soon)

---

## Database Schema

The application uses the following tables:

**`users`** - Stores user accounts (both adopters and providers)
- user_id, first_name, last_name, user_email, password_hash, user_phone, user_role, provider_id

**`providers`** - Stores provider/organization information
- provider_id, provider_name

**`pets`** - Stores pet listings (for future implementation)
- pet_id, pet_name, pet_type, breed, age, description, provider_id, is_available

**`adoption_applications`** - Stores adoption applications (for future implementation)
- application_id, pet_id, user_id, status, message

---

## Development

### Key Files to Know

**Authentication:**
- `includes/auth.php` - Authentication helper functions
- `includes/config.php` - Global configuration and helper functions

**Database:**
- `includes/db_connect.php` - Database connection using mysqli

**Pages:**
- `public/index.php` - Homepage
- `public/register.php` - User registration
- `public/login.php` - User login
- `public/dashboard.php` - User dashboard (protected)

### Adding New Protected Pages

To create a new page that requires login:

```php
<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Require user to be logged in
requireLogin();

// Your page content here
?>
```

### Checking User Role

```php
<?php
if (isProvider()) {
    // Provider-specific code
} elseif (isAdopter()) {
    // Adopter-specific code
}
?>
```

---

## Security Features

✅ **Password Hashing** - Uses PHP's `password_hash()` (bcrypt)
✅ **SQL Injection Prevention** - Prepared statements with mysqli
✅ **XSS Prevention** - `htmlspecialchars()` on all user input
✅ **Session Management** - Secure session handling with timeout
✅ **CSRF Protection** - Session regeneration on login
✅ **Input Validation** - Server-side validation on all forms

---

## Common Issues & Solutions

### 1. "Database connection failed"

**Solution:**
- Make sure MySQL is running in XAMPP Control Panel
- Check that database `pet_adoption_db` exists
- Verify `.env` file has correct credentials
- Default XAMPP: user=`root`, password=`(empty)`

### 2. "Page not found" errors

**Solution:**
- Make sure project is in `htdocs` folder
- Access via `http://localhost/petadoptionplatform/public/`
- Check that Apache is running in XAMPP

### 3. "Session already started" warnings

**Solution:**
- This is usually harmless in development
- Make sure you're not calling `session_start()` multiple times
- Set `APP_DEBUG=false` in `.env` to hide warnings

### 4. Changes not showing up

**Solution:**
- Clear your browser cache (Ctrl+Shift+Delete)
- Do a hard refresh (Ctrl+F5 or Cmd+Shift+R)
- Check that you're editing files in the `htdocs` folder

---

## Testing

### Manual Testing Checklist

**Registration Flow:**
- [ ] Register as Adopter
- [ ] Register as Provider (with organization name)
- [ ] Try duplicate email (should show error)
- [ ] Try weak password < 12 chars (should show error)

**Login Flow:**
- [ ] Login with valid credentials
- [ ] Try wrong password (should show error)
- [ ] Try non-existent email (should show error)
- [ ] Access dashboard after login
- [ ] Logout and verify session cleared

**Protected Pages:**
- [ ] Try accessing dashboard without login (should redirect)
- [ ] Login and access dashboard (should work)
- [ ] Verify session timeout after 1 hour

---

## Future Enhancements

- [ ] Pet listing functionality
- [ ] Pet search and filtering
- [ ] Adoption application system
- [ ] User profile editing
- [ ] Image upload for pets
- [ ] Email notifications
- [ ] Admin panel
- [ ] Favorites/wishlist

---

## Authors

- **Neo Morrison** - [GitHub](https://github.com/neomorrison)
- **Team NaN** - CPSC 362, CSUF

---

## Version History

* **0.2** (Current)
    * Converted to PHP backend
    * Added complete authentication system
    * Implemented role-based access control
    * Created database schema

* **0.1**
    * Initial Release (Python/Flask)

---

## License

This project is licensed under the MIT License.

---

## Acknowledgments

- CSUF Computer Science Department
- XAMPP Development Team
- PHP Community

---

## Need Help?

If you encounter issues:

1. Check the **Common Issues** section above
2. Review your XAMPP and database setup
3. Check browser console for JavaScript errors
4. Verify all files are in the correct locations
5. Make sure Apache and MySQL are running

**For development questions, consult your project documentation or reach out to your team members.**
