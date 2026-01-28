#!/bin/bash
# =============================================================================
# Install MOHAA Stats Plugin to a running SMF container
# 
# Usage: ./install_to_container.sh [container_name]
# Default container: dev-smf
# =============================================================================

set -e

CONTAINER_NAME="${1:-dev-smf}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
MOHAA_DIR="$SCRIPT_DIR/smf-mohaa"

echo "=== Installing MOHAA Stats Plugin to container: $CONTAINER_NAME ==="

# Check container exists
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo "ERROR: Container '$CONTAINER_NAME' is not running"
    exit 1
fi

# Copy PHP Sources
echo "[1/4] Copying Sources..."
docker cp "$MOHAA_DIR/Sources/." "$CONTAINER_NAME:/var/www/html/Sources/"

# Copy Templates
echo "[2/4] Copying Themes..."
docker cp "$MOHAA_DIR/Themes/." "$CONTAINER_NAME:/var/www/html/Themes/"

# Fix permissions
echo "[3/4] Setting permissions..."
docker exec "$CONTAINER_NAME" chown -R www-data:www-data /var/www/html/Sources/MohaaStats /var/www/html/Themes/default/

# Verify
echo "[4/4] Verifying installation..."
MOHAA_FILES=$(docker exec "$CONTAINER_NAME" ls /var/www/html/Sources/MohaaStats/ 2>/dev/null | wc -l)
if [ "$MOHAA_FILES" -gt 0 ]; then
    echo "SUCCESS: $MOHAA_FILES files installed to Sources/MohaaStats/"
else
    echo "ERROR: No files found in Sources/MohaaStats/"
    exit 1
fi

echo ""
echo "=== Plugin files installed! ==="
echo ""
echo "NEXT STEPS:"
echo "1. Run install.sql in phpMyAdmin (or: ./run_sql.sh $CONTAINER_NAME)"
echo "2. Visit your forum and look for 'MOHAA Stats' menu"
echo "3. Configure API URL in Admin -> Configuration -> MOHAA Stats"
