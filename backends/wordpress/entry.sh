#!/bin/sh
# WordPress on the public wordpress:php7.4-apache image. Recreates at start what the
# production Dockerfile did at build time, then runs the unchanged wp-start.sh.
# Files are mounted read-only at /opt/backend (WordPress 5.3.2 tree, wordpress.sql,
# wp-start.sh); PHP 7.4 / WordPress 5.3.2 behaviour stays as in production.
set -e
SRC=/opt/backend
WP_CLI_VERSION=2.12.0
WP_CLI_SHA256=ce34ddd838f7351d6759068d09793f26755463b4a4610a5a5c0a97b68220d85c

if [ ! -x /bin/wp ]; then
  # The lab network is attached shortly after the container starts; if apt runs first
  # it fails (exit 100) and the container dies before the attach. Wait up to 120 s.
  echo "=> Wait for the network ..."
  i=0
  until getent hosts archive.debian.org >/dev/null 2>&1; do
    i=$((i + 1))
    [ "$i" -ge 60 ] && { echo "no network after 120 s" >&2; exit 1; }
    sleep 2
  done
  echo "=> Install sudo, less, mysql client (bullseye is on archive.debian.org) ..."
  sed -i -e 's|deb.debian.org|archive.debian.org|g' -e '/bullseye-updates/d' /etc/apt/sources.list
  for n in 1 2 3; do apt-get update -qq && break; [ "$n" -eq 3 ] && exit 1; sleep 5; done
  DEBIAN_FRONTEND=noninteractive apt-get install -y -qq sudo less default-mysql-client >/dev/null
  rm -rf /var/lib/apt/lists/*
  echo "=> Install WP-CLI $WP_CLI_VERSION (pinned) ..."
  curl -fsSL -o /tmp/wp.phar "https://github.com/wp-cli/wp-cli/releases/download/v$WP_CLI_VERSION/wp-cli-$WP_CLI_VERSION.phar"
  echo "$WP_CLI_SHA256  /tmp/wp.phar" | sha256sum -c -
  install -m 0755 /tmp/wp.phar /bin/wp && rm -f /tmp/wp.phar
fi

echo "=> Copy WordPress tree ..."
cp -a "$SRC/Wordpress/." /var/www/html/
cp "$SRC/wordpress.sql" /var/www/html/wordpress.sql
mkdir -p /var/www/html/wp-content/mu-plugins
cp "$SRC/mu-plugins/academy-no-auto-update.php" /var/www/html/wp-content/mu-plugins/
chown -R www-data /var/www/html
# wp-start.sh and the dump carry the production site address wordpress.airlock.academy:8081
# (wp db import overwrites the --url of wp core install). One extra search-replace right
# after wp-start.sh's own moves the content to WP_URL (backends.hcl); wp-start.sh stays as is.
if [ -n "${WP_URL:-}" ] && [ "$WP_URL" != wordpress.airlock.academy:8081 ]; then
  awk '{ print } /^sudo -E -u www-data wp search-replace / {
    print "sudo -E -u www-data wp search-replace '"'"'wordpress.airlock.academy:8081'"'"' \"$WP_URL\"" }' \
    "$SRC/wp-start.sh" > /usr/bin/wp-start.sh
  chmod 0755 /usr/bin/wp-start.sh
else
  install -m 0755 "$SRC/wp-start.sh" /usr/bin/wp-start.sh
fi

# Production published WordPress on host port 8081 (siteurl ...:8081): listen on 80 and 8081.
grep -q '^Listen 8081' /etc/apache2/ports.conf || echo 'Listen 8081' >> /etc/apache2/ports.conf
sed -i 's|^<VirtualHost \*:80>$|<VirtualHost *:80 *:8081>|' /etc/apache2/sites-enabled/000-default.conf

cd /var/www/html
# Same as the image CMD "wp-start.sh" (shell form) of the production image.
exec /bin/sh -c wp-start.sh
