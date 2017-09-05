#!/bin/bash
composer install
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

