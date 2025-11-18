# CPSC 362: Foundations of Software Engineering - Team NaN

CSUF Pet Adoption Platform

## description

this platform helps connect pet-lovers with foster pets. you can find a pet to adopt by type of pet, or offer your pet for adoption.

## tech stack

- frontend: HTML, CSS, JavaScript
- backend: PHP 7.4+
- database: MySQL/MariaDB

## features

- user registration (adopters and providers)
- user login with session management
- protected dashboard
- role-based access control
- pet listings (coming soon)
- adoption applications (coming soon)

## prerequisites

before you begin, install the following:

1. XAMPP (includes PHP, MySQL, and Apache)
   - download from: https://www.apachefriends.org/

2. web browser (chrome, firefox, safari, etc.)

3. git (for version control)

## installation

### step 1: clone the repository

```bash
git clone https://github.com/nickvibing/petadoptionplatform.git
cd petadoptionplatform
```

### step 2: move project to XAMPP htdocs

on macOS:
```bash
cp -r /path/to/petadoptionplatform /Applications/XAMPP/xamppfiles/htdocs/
```

on windows:
```
copy the petadoptionplatform folder to: C:\xampp\htdocs\
```

### step 3: start XAMPP services

1. open XAMPP control panel
2. start apache (web server)
3. start MySQL (database)

### step 4: create database

using phpMyAdmin:

1. open your browser and go to: http://localhost/phpmyadmin
2. click "import" tab
3. click "choose file" and select `database/schema.sql` from the project
4. click "go" to import

using command line:

```bash
mysql -u root -p < database/schema.sql
```

when prompted for password, press enter (default XAMPP has no password)

### step 5: configure environment

the `.env` file is already created with default XAMPP settings:

```
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=pet_adoption_db
```

if your MySQL settings are different, edit the `.env` file in the project root.

### step 6: access the application

open your browser and go to:

```
http://localhost/petadoptionplatform/public/
```

you should see the homepage.

## project structure

```
petadoptionplatform/
├── public/              web-accessible files
│   ├── index.php       homepage
│   ├── login.php       login page and handler
│   ├── register.php    registration page and handler
│   ├── dashboard.php   user dashboard (protected)
│   ├── logout.php      logout handler
│   ├── css/            stylesheets
│   ├── js/             javascript files
│   └── images/         image assets
│
├── includes/           backend PHP files (not web-accessible)
│   ├── config.php      application configuration
│   ├── db_connect.php  database connection
│   └── auth.php        authentication helpers
│
├── database/           database files
│   └── schema.sql      database schema
│
├── .env                environment variables (do not commit)
├── .env.example        example environment file
└── README.md           this file
```

## usage

### register a new account

1. go to http://localhost/petadoptionplatform/public/register.php
2. fill in your information
3. choose role: adopter or provider
   - adopter: looking to adopt a pet
   - provider: shelter/organization listing pets
4. click "register"

### login

1. go to http://localhost/petadoptionplatform/public/login.php
2. enter your email and password
3. click "login"
4. you will be redirected to your dashboard

### dashboard

after logging in, you can:
- view your profile information
- access role-specific features
- update account settings (coming soon)
- browse pets (coming soon)

## database schema

the application uses the following tables:

users - stores user accounts (both adopters and providers)
- user_id, first_name, last_name, user_email, password_hash, user_phone, user_role, provider_id

providers - stores provider/organization information
- provider_id, provider_name

pets - stores pet listings (for future implementation)
- pet_id, pet_name, pet_type, breed, age, description, provider_id, is_available

adoption_applications - stores adoption applications (for future implementation)
- application_id, pet_id, user_id, status, message

## security features

- password hashing using PHP's password_hash() with bcrypt
- SQL injection prevention using prepared statements
- XSS prevention using htmlspecialchars() on all user input
- session management with secure timeout
- CSRF protection with session regeneration on login
- server-side input validation on all forms

## common issues

### database connection failed

solution:
- make sure MySQL is running in XAMPP control panel
- check that database pet_adoption_db exists
- verify .env file has correct credentials
- default XAMPP: user=root, password=(empty)

### page not found errors

solution:
- make sure project is in htdocs folder
- access via http://localhost/petadoptionplatform/public/
- check that apache is running in XAMPP

### session already started warnings

solution:
- this is usually harmless in development
- make sure you are not calling session_start() multiple times
- set APP_DEBUG=false in .env to hide warnings

### changes not showing up

solution:
- clear your browser cache
- do a hard refresh (ctrl+f5 or cmd+shift+r)
- check that you are editing files in the htdocs folder

## testing checklist

registration flow:
- register as adopter
- register as provider (with organization name)
- try duplicate email (should show error)
- try weak password less than 12 chars (should show error)

login flow:
- login with valid credentials
- try wrong password (should show error)
- try non-existent email (should show error)
- access dashboard after login
- logout and verify session cleared

protected pages:
- try accessing dashboard without login (should redirect)
- login and access dashboard (should work)
- verify session timeout after 1 hour

## future enhancements

- pet listing functionality
- pet search and filtering
- adoption application system
- user profile editing
- image upload for pets
- email notifications
- admin panel
- favorites/wishlist

## authors

- Neo Morrison - https://github.com/neomorrison
- Team NaN - CPSC 362, CSUF

## version history

0.2 (current)
- converted to PHP backend
- added complete authentication system
- implemented role-based access control
- created database schema

0.1
- initial release (Python/Flask)

## license

this project is licensed under the MIT License.
