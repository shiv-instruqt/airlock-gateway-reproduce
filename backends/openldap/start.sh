#!/bin/sh
# OpenLDAP 2.6 on the public alpine image; config files are mounted read-only at
# /opt/backend. Recreates the production Dockerfile steps, then starts like
# startscript.sh: LDIF import in the background (waits for slapd), slapd in front.
set -e
SRC=/opt/backend
# The lab network is attached shortly after the container starts; if apk runs first
# it fails and the container dies before the attach. Wait up to 120 s.
i=0
until nslookup dl-cdn.alpinelinux.org >/dev/null 2>&1; do
  i=$((i + 1))
  [ "$i" -ge 60 ] && { echo "no network after 120 s" >&2; exit 1; }
  sleep 2
done
apk add --no-cache openldap openldap-back-mdb openldap-overlay-memberof openldap-clients openldap-overlay-ppolicy >/dev/null
mkdir -p /run/openldap /var/lib/openldap/openldap-data
cp "$SRC/slapd.conf" /etc/openldap/slapd.conf
cp "$SRC/airlock-iam.schema" "$SRC/00_iam_ous.ldif" /etc/openldap/schema/
/usr/sbin/slaptest -u
sh "$SRC/startLDIFimport.sh" &
exec /usr/sbin/slapd -d -1
