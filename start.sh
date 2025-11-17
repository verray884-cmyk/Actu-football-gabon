composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

