#!/bin/bash

echo "=> Wait for DB Connection (60s) ..."
sleep 60
echo "=> Configure wordpress ..."
sudo -E -u www-data wp config create --dbhost="$WORDPRESS_DB_HOST" --dbname="$WORDPRESS_DB_NAME" --dbuser="$WORDPRESS_DB_USER" --dbpass="$WORDPRESS_DB_PASSWORD"
echo "=> Install wordpress ..."
sudo -E -u www-data wp core install --path='/var/www/html' --url="$WP_URL" --title="$WP_TITLE" --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" --admin_email="$WP_ADMIN_EMAIL"
echo "=> Activate plugins ..."
sudo -E -u www-data wp plugin activate simple-jwt-login
sudo -E -u www-data wp plugin activate svg-support
sudo -E -u www-data wp plugin activate colibri-page-builder
echo "=> Import DB ..."
sudo -E -u www-data wp db import wordpress.sql
echo "=> Change Domain ..."
sudo -E -u www-data wp search-replace '192.168.228.133:8081' 'wordpress.airlock.academy:8081'
echo "=> Update Admin ..."
sudo -E -u www-data wp user update "$WP_ADMIN_USER" --display_name=admin --user_pass="$WP_ADMIN_PASSWORD" --user_email=admin@virtinc.com
echo "=> Upgrade DB ..."
sudo -E -u www-data wp core update-db
echo "=> Activate Theme..."
sudo -E -u www-data wp theme activate calliope 
echo "=> Starting apache ..."
apache2-foreground
