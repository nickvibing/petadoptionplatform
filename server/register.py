import os
from flask import Flask, request, render_template
from werkzeug.security import generate_password_hash, check_password_hash

from database.db_connect import create_connection

app = Flask(__name__, template_folder='../client', static_folder='../client')

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        first_name = request.form.get("firstName","").strip()
        last_name = request.form.get("lastName","").strip()
        email = request.form.get("email","").strip().lower()
        password = request.form.get("password","")
        confirm_password = request.form.get("confirmPassword","")
        phone = request.form.get("phone","").strip()
        role = request.form.get("role","adopter")
        provider_name = request.form.get("providerName","").strip()

        if not all([first_name, last_name, email, password, phone, role]):
            return "All fields are required", 400

        if password != confirm_password:
            return "Passwords do not match", 400

        if len(password) < 12:
            return "Password must be at least 12 characters long", 400
        
        #hash password
        password_hash = generate_password_hash(password)

        db = create_connection()
        cursor = db.cursor(dictionary=True)

        #check if email already exists
        cursor.execute("SELECT * FROM users WHERE user_email = %s", (email,))
        existing = cursor.fetchone()

        if existing:
            cursor.close()
            db.close()
            return render_template('userRegistration.html', error="Email already registered"), 400
        
        #provider logic
        provider_id = None
        if role == 'provider':
            if provider_name == "":
                return render_template('userRegistration.html', error="Provider name is required for provider role"), 400
            cursor.execute("INSERT INTO providers (provider_name) VALUES (%s)", (provider_name,))
            provider_id = cursor.lastrowid

        try:
            
            cursor.execute("""
                INSERT INTO users (first_name, last_name, user_email, password_hash, user_phone, user_role, provider_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s)
            """, (first_name, last_name, email, password_hash, phone if phone else None, role, provider_id))
            db.commit()
        except Exception as e:
            db.rollback()
            return f"An error occurred: {e}", 500
        finally:
            cursor.close()
            db.close()

        return "Registration successful", 200

    return render_template('userRegistration.html')