#!/bin/sh
# Check functions for LAB 01. Each check script sources this file and calls one function;
# exit 0 = passed. They read the ACTIVE configuration only.
. /opt/lab/scripts/lib/gw.sh

# A skip runs the solve as a background job; give it a moment before checking.
wait_for_solve() {
  i=0
  while [ -d "$LAB_STATE/solve.lock" ] && [ $i -lt 20 ]; do i=$((i + 1)); sleep 1; done
}

vhosts() {
  [ -n "$VIRTINC_HOST" ] || fail "VIRTINC_HOST is not set."
  printf '%s' "$1" | jq -c --arg host "$VIRTINC_HOST" ".\"virtual-hosts\" | $JQ_VHOSTS"
}

# Back-end groups (JSON array) with the Virtinc back-end: name or lab IP, port 8080 (80 too).
virtinc_begs() {
  printf '%s' "$1" | jq -c --argjson hosts "[\"$VIRTINC_BACKEND\", \"$VIRTINC_IP\"]" --argjson ports '["8080", "80"]' \
    ".\"back-end-groups\" | $JQ_BEGS"
}

check_license() {
  wait_for_solve
  gw_query license | jq -e '.license.data.attributes.expiry // "" | length > 0' >/dev/null \
    || fail "No active license."
}

check_ntp() {
  wait_for_solve
  gw_query network-services | jq -e --arg ip "$NTP_SERVER" \
    '[."network-services".data.attributes.ntp | .. | strings | split(",")[] | gsub("\\s"; "")] | any(. == $ip)' >/dev/null \
    || fail "NTP server not in the active configuration."
}

check_vhost_exists() {
  wait_for_solve
  [ "$(vhosts "$(gw_query virtual-hosts)" | jq length)" -gt 0 ] || fail "No active virtual host with the host name $VIRTINC_HOST."
}

check_vhost_protocols() {
  wait_for_solve
  vhosts "$(gw_query virtual-hosts)" | jq -e 'any(.[]; .attributes.networkInterface
      | (.http.enabled == true) and (.https.enabled == true) and (.http.httpsRedirectEnforced == true))' >/dev/null \
    || fail "The virtual host does not have HTTP, HTTPS and the redirect to HTTPS enabled."
  vhosts "$(gw_query virtual-hosts)" | jq -e 'any(.[]; .attributes.strictlyMatchFullyQualifiedDomainName != true)' >/dev/null \
    || fail "Untick 'Strictly match FQDN and aliases' on the virtual host and activate."
}

check_backend_group() {
  wait_for_solve
  [ "$(virtinc_begs "$(gw_query back-end-groups)" | jq length)" -gt 0 ] \
    || fail "No active back-end group with the Virtinc back-end host $VIRTINC_BACKEND, port 8080."
}

check_mapping() {
  wait_for_solve
  q=$(gw_query virtual-hosts back-end-groups mappings)
  vids=$(vhosts "$q" | jq -c '[.[].id]')
  bids=$(virtinc_begs "$q" | jq -c '[.[].id]')
  printf '%s' "$q" | jq -e --argjson v "$vids" --argjson b "$bids" ".mappings | $JQ_LINKED | length > 0" >/dev/null \
    || fail "No active mapping connects the Virtinc virtual host and back-end group."
}

check_http_redirect() {
  wait_for_solve
  host=$VIRTINC_HOST
  ip=$(gateway_ip)
  out=$(curl -s -o /dev/null --max-time 10 -w '%{http_code} %{redirect_url}' --resolve "$host:80:$ip" "http://$host/")
  log "http://$host/ -> $out"
  case $out in 30?\ https://*) ;; *) fail "http://$host/ does not redirect to HTTPS ($out)." ;; esac
}

check_virtinc_page() {
  wait_for_solve
  host=$VIRTINC_HOST
  ip=$(gateway_ip)
  page=$(curl -sk -L --max-time 15 --resolve "$host:443:$ip" --resolve "$host:80:$ip" "https://$host/")
  printf '%s' "$page" | grep -qi '<title>Virtinc' || fail "https://$host/ does not show the Virtinc page."
}
