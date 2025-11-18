import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
import os
import sys

load_dotenv()

def create_connection():
    try:
        conn = mysql.connector.connect(
            host    =os.getenv("DB_HOST"),
            user    =os.getenv("DB_USER"),
            password=os.getenv("DB_PASS"),
            database=os.getenv("DB_NAME")
        )
        if conn.is_connected():
            print("Connection to the database was successful.")
            return conn
        else:
            print("Failed to connect to the database.")
            return None
    except Error as e:
        print(f"Error while connecting to MySQL: {e}")
        return None

if __name__ == "__main__":
    create_connection()