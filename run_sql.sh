#!/bin/bash
# =============================================================================
# Run MOHAA Stats SQL on MySQL container
# 
# Usage: ./run_sql.sh [mysql_container] [db_user] [db_pass] [db_name]
# Defaults: dev-smf-mysql / smf / (prompt) / smf
# =============================================================================

set -e

MYSQL_CONTAINER="${1:-dev-smf-mysql}"
DB_USER="${2:-smf}"
DB_PASS="${3:-}"
DB_NAME="${4:-smf}"

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
SQL_FILE="$SCRIPT_DIR/smf-mohaa/install.sql"

if [ -z "$DB_PASS" ]; then
    echo -n "Enter MySQL password for '$DB_USER': "
    read -s DB_PASS
    echo ""
fi

echo "=== Running MOHAA Stats SQL ==="
echo "Container: $MYSQL_CONTAINER"
echo "Database: $DB_NAME"
echo ""

# Check container exists
if ! docker ps --format '{{.Names}}' | grep -q "^${MYSQL_CONTAINER}$"; then
    echo "ERROR: Container '$MYSQL_CONTAINER' is not running"
    exit 1
fi

# Run SQL
echo "Running install.sql..."
docker exec -i "$MYSQL_CONTAINER" mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE" 2>&1 | grep -v "Warning"

# Verify tables
echo ""
echo "Verifying tables..."
TABLE_COUNT=$(docker exec "$MYSQL_CONTAINER" mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES LIKE 'smf_mohaa%';" 2>/dev/null | grep -c "smf_mohaa" || echo "0")
echo "MOHAA tables found: $TABLE_COUNT"

if [ "$TABLE_COUNT" -ge 10 ]; then
    echo ""
    echo "=== SUCCESS! MOHAA Stats tables installed ==="
else
    echo ""
    echo "WARNING: Expected at least 10 tables, found $TABLE_COUNT"
fi
