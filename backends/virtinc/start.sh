#!/bin/sh
# Virtinc on the public httpd:2.4-alpine image; htdocs is mounted read-only at
# /usr/local/apache2/htdocs. In production (ECS) virtinc was published on host
# port 8080, so the lab guides address it as :8080; listen on 80 and 8080.
grep -q '^Listen 8080' /usr/local/apache2/conf/httpd.conf || echo 'Listen 8080' >> /usr/local/apache2/conf/httpd.conf
exec httpd-foreground
