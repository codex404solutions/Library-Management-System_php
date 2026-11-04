#!/usr/bin/env bash
set -e

echo "=============================================================="
echo "  CodeX404 Solutions - Automated Project & Database Setup"
echo "=============================================================="
echo ""

# 1. Verify PHP
if ! command -v php &> /dev/null; then
    echo "[ERROR] PHP command not found. Please install PHP."
    exit 1
fi

# 2. Environment Setup
if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    echo "[i] Creating .env from .env.example..."
    cp .env.example .env
    echo "[OK] .env created."
fi

if [ -d "librarymanagement" ] && [ ! -f "librarymanagement/.env" ] && [ -f "librarymanagement/.env.example" ]; then
    echo "[i] Creating librarymanagement/.env from .env.example..."
    cp "librarymanagement/.env.example" "librarymanagement/.env"
    echo "[OK] librarymanagement/.env created."
fi

# 3. Run Migration
echo "[i] Provisioning database and executing migrations..."
php "librarymanagement/database/migrate.php"

echo ""
echo "=============================================================="
echo "[SUCCESS] Project and Database Setup Complete!"
echo "=============================================================="
