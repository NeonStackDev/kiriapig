#!/bin/bash
#run this with chmod +x  artisan-commands.sh
#then run  ./artisan-commands.sh this will fix the initial setup for new user
# Create necessary directories if they don't exist
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set correct permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Clear all Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "Cache directories created and permissions set"
