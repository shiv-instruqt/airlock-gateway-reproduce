#!/bin/sh
# Wait until slapd answers (replaces the former blind 'sleep 20'), then import.
i=0
until ldapsearch -x -H ldap://127.0.0.1 -s base -b "" namingContexts >/dev/null 2>&1; do
  i=$((i+1))
  if [ "$i" -ge 120 ]; then echo "slapd not ready after 120s, LDIF import skipped" >&2; exit 1; fi
  sleep 1
done
# OpenLDAP 2.5+ treats memberOf as operational (maintained by the memberof overlay
# only), so the explicit memberOf lines in the LDIF are rejected (constraint
# violation). Import without them, then rewrite each group's members once so the
# overlay sets memberOf on the users, as the explicit lines did on OpenLDAP 2.4.
sed '/^memberOf:/d' /etc/openldap/schema/00_iam_ous.ldif > /tmp/00_iam_ous.import.ldif
ldapadd -x -w secret -D "cn=Manager,dc=ergon,dc=ch" -f /tmp/00_iam_ous.import.ldif || exit 1
ldapsearch -x -LLL -o ldif-wrap=no -w secret -D "cn=Manager,dc=ergon,dc=ch" -b "dc=ergon,dc=ch" "(member=*)" member |
  awk '/^dn: /{if (dn) print "-\n"; dn=$0; print dn "\nchangetype: modify\nreplace: member"; next} /^member: /{print} END{if (dn) print "-\n"}' |
  ldapmodify -x -w secret -D "cn=Manager,dc=ergon,dc=ch"
