#!/bin/sh
# Check functions for LAB 02 (WordPress behind the Virtinc virtual host, rewrites). Each
# check script sources this file and calls one function; exit 0 = passed. They read the
# ACTIVE configuration only, except check_wordpress_page, which fetches the page through
# the Gateway as LAB 01's page check does.
. /opt/lab/scripts/lib/gw.sh

# A skip runs the solve as a background job (own lock, see solve.sh); wait for it.
wait_for_solve() {
  i=0
  while [ -d "$LAB_STATE/solve-lab2.lock" ] && [ $i -lt 20 ]; do i=$((i + 1)); sleep 1; done
}

# The Virtinc virtual hosts (LAB 01), JSON array.
vhosts() {
  [ -n "$VIRTINC_HOST" ] || fail "VIRTINC_HOST is not set."
  printf '%s' "$1" | jq -c --arg host "$VIRTINC_HOST" ".\"virtual-hosts\" | $JQ_VHOSTS"
}

# Back-end groups with the WordPress back-end: name or lab IP, port 8081.
wordpress_begs() {
  printf '%s' "$1" | jq -c --argjson hosts "[\"$WORDPRESS_BACKEND\", \"$WORDPRESS_IP\"]" --argjson ports "[\"$WORDPRESS_PORT\"]" \
    ".\"back-end-groups\" | $JQ_BEGS"
}

# Mappings connected to a Virtinc virtual host and a WordPress back-end group, JSON array.
wordpress_mappings() {
  q=$(gw_query virtual-hosts back-end-groups mappings)
  vids=$(vhosts "$q" | jq -c '[.[].id]')
  bids=$(wordpress_begs "$q" | jq -c '[.[].id]')
  printf '%s' "$q" | jq -c --argjson v "$vids" --argjson b "$bids" ".mappings | $JQ_LINKED"
}

check_backend_group() {
  wait_for_solve
  [ "$(wordpress_begs "$(gw_query back-end-groups)" | jq length)" -gt 0 ] \
    || fail "No active back-end group with the WordPress back-end host $WORDPRESS_BACKEND, port $WORDPRESS_PORT."
}

check_mapping() {
  wait_for_solve
  m=$(wordpress_mappings)
  [ "$(printf '%s' "$m" | jq length)" -gt 0 ] || fail "No active mapping connects the Virtinc virtual host and the WordPress back-end group."
  printf '%s' "$m" | jq -e "$JQ_WP_DEFS any(.[]; wp_entry)" >/dev/null \
    || fail "The WordPress mapping needs the entry path /wordpress/ and the back-end path /."
}

check_mapping_mode() {
  wait_for_solve
  wordpress_mappings | jq -e "$JQ_WP_DEFS any(.[]; wp_entry and wp_mode)" >/dev/null \
    || fail "The WordPress mapping is not set to log only and integration mode yet."
}

check_rewrites() {
  wait_for_solve
  wordpress_mappings | jq -e "$JQ_WP_DEFS any(.[]; wp_rewrites)" >/dev/null \
    || fail "The WordPress mapping has no active rewrite for the back-end host yet."
}

# The page through the Gateway must be WordPress and must no longer name the back-end
# (plain, JSON-escaped or URL-encoded: all contain the host name).
check_wordpress_page() {
  wait_for_solve
  host=$VIRTINC_HOST
  ip=$(gateway_ip)
  body=$(mktemp)
  out=$(curl -sk -L --max-redirs 5 --max-time 20 --resolve "$host:443:$ip" --resolve "$host:80:$ip" \
    -o "$body" -w '%{http_code} %{url_effective}' "https://$host/wordpress/")
  log "https://$host/wordpress/ -> $out"
  page=$(cat "$body"); rm -f "$body"
  case $out in
    "200 https://$host/"*) ;;
    "200 "*) fail "https://$host/wordpress/ redirects away from the Gateway (${out#200 }): rewrite the back-end address in redirects too." ;;
    *) fail "https://$host/wordpress/ does not answer with the WordPress page ($out). Check the mapping and activate." ;;
  esac
  printf '%s' "$page" | grep -q 'wp-content' || fail "https://$host/wordpress/ does not show the WordPress blog."
  n=$(printf '%s' "$page" | grep -oi "$WORDPRESS_BACKEND" | wc -l | tr -d ' ')
  [ "$n" -eq 0 ] || fail "The WordPress page still contains $n absolute URLs with $WORDPRESS_BACKEND. Add rewrites and activate."
}
