# Rappel Project

## Structure by Antigravity

- **Frontend**: `client/` (React + Vite)
- **Backend**: `api/` (PHP Native)
- **Database**: `database/` (MariaDB Schema)

## Prerequisites

- **XAMPP** (PHP 8.2+, MariaDB, Apache)
- **Node.js** (v18+)

## Setup & Running

### 1. Database
1. Start **Apache** and **MySQL** in XAMPP Control Panel.
2. Edit `.env` in the root directory with your database credentials:
   ```ini
   DB_HOST=localhost
   DB_NAME=rappel
   DB_USER=root
   DB_PASS=
   DB_PORT=3307  <-- IMPORTANT: XAMPP MySQL is on 3307
   ```
3. Run the setup script to create the DB and import schema:
   ```bash
   php api/test_db.php
   ```
   *(Or import `database/schema.mariadb.sql` manually via phpMyAdmin)*

### 2. Backend (API)
The backend runs via XAMPP's Apache.
- URL: `http://localhost/rappel/api/`
- Test: Open `http://localhost/rappel/api/` in your browser. You should see `{"status":"online", ...}`.

### 3. Frontend (React)
1. Open a terminal in the `client/` folder:
   ```bash
   cd client
   ```
2. Install dependencies (first time only):
   ```bash
   npm install
   ```
3. Start the dev server:
   ```bash
   npm run dev
   ```
4. Access the app at the URL shown (usually `http://localhost:5173`).

## Configuration
- **Frontend API URL**: Configured in `client/.env` (or `.env` in root if symlinked/loaded).
  - Ensure `VITE_API_URL` points to your backend (e.g., `http://localhost/rappel/api`).