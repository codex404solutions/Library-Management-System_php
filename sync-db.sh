#!/usr/bin/env bash
set -e
echo "=============================================================="
echo "  CodeX404 Solutions - Local Database Schema Sync Tool"
echo "=============================================================="
echo ""
php "librarymanagement/database/migrate.php" --export
echo "[OK] Schema updated in repository! You can now commit and push to Git."
