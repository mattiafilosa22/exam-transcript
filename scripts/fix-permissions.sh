#!/bin/bash
# Fix dei permessi
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html
