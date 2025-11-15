# server/login.py
from flask import Flask, render_template, request, redirect, make_response, jsonify
import jwt
import datetime
import os

from database.db_connect import create_connection
from config.login_config import SECRET_KEY

app = Flask(__name__)

@app.get('/login')
def login_page():
    return render_template('login.html')

@app.post('/login')
def login()
    username = request.form.get('email')
    password = request.form.get('password')

    db = create_connection()
    cursor = db.cursor(dictionary=True)

    cursor.execute("SELECT user_id, password_hash FROM users WHERE user_email = %s", (email,))
    user = cursor.fetchone()
    
    if not user:
        return "invalid email or password", 401
    
    if not bycrypt.checkpw(password.encode('utf-8'), user['password_hash'].encode('utf-8')):
        return "invalid email or password", 401

    token = generate_token(user['user_id'])
    response = make_response(redirect('/dashboard'))
    response.set_cookie('auth_token', token, httponly=True, samesite='Lax')
    return response

def dashboard():
    token = request.cookies.get('auth_token')
    if not token:
        return redirect('/login')
    try:
        decoded = jwt.decode(token, SECRET_KEY, algorithms=['HS256'])
    except:
        return redirect('/login')
    return f"Welcome User {decoded['user_id']} to your dashboard!"

def generate_token(user_id):
    payload = {
        'user_id': user_id,
        'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=1),
        'iat': datetime.datetime.utcnow()
    }
    token = jwt.encode(payload, SECRET_KEY, algorithm='HS256')
    return token

if __name__ == '__main__':
    app.run(debug=True)