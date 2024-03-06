#!/bin/bash

# APP_DEBUG="false" 
# APP_URL="https://genesisbcscare.com" 
# DB_HOST="others.czcbp9bkktgs.ap-southeast-1.rds.amazonaws.com"
# DB_DATABASE="genesisbcscare"
# DB_USERNAME="bcscare-user"
# DB_PASSWORD="dfresfres3@#rt" 

PROJECT_DIR="/var/www/html/betagenesisedu" 
cd "${PROJECT_DIR}" && chown -R www-data:www-data .

# sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}| ; s|^APP_URL=.*|APP_URL=${APP_URL}| ; s|^DB_HOST=.*|DB_HOST=${DB_HOST}| ; s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}| ; s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}| ; s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}| " .env 

# composer install --no-interaction
# php artisan key:generate
# chown -R www-data:www-data .
