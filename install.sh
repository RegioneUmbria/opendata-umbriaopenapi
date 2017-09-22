#!/bin/bash
mkdir -p app/logs
mkdir -p app/cache
chown www-data:www-data app/logs/
chown www-data:www-data app/cache/
composer install
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

