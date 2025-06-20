#!/bin/bash

PHP_PATH=$(which php)
SCRIPT_PATH="$(cd "$(dirname "$0")" && pwd)/cron.php"
CRON_JOB="0 0 * * * $PHP_PATH $SCRIPT_PATH"

# Check if CRON job already exists
(crontab -l 2>/dev/null | grep -Fv "$SCRIPT_PATH"; echo "$CRON_JOB") | crontab -

echo "CRON job set to run daily at midnight:"
echo "$CRON_JOB"
