# Refresh central and all tenants in one command
php artisan migrate:fresh && php artisan tenants:migrate-fresh