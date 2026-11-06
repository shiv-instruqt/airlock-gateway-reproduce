#!/bin/sh
# Background job started by solve.sh: LAB 01 first (license if none is active, then the
# idempotent LAB 01 solve: LAB 02 needs its virtual host), then the LAB 02 REST solve on
# the Gateway; frees the lock at the end.
. /opt/lab/scripts/lib/gw.sh
trap 'rmdir "$LAB_STATE/solve-lab2.lock" 2>/dev/null' EXIT
if [ -n "${GW_LICENSE:-}" ]; then
  GW_PREFIX="GW_LICENSE_B64='$(printf '%s' "$GW_LICENSE" | base64 | tr -d '\n')'"
  gw_run license
  GW_PREFIX=
fi
gw_run solve "$NTP_SERVER" "$VIRTINC_BACKEND" "$VIRTINC_IP" "$(gateway_ip)" "$VIRTINC_HOST" || exit 1
gw_run solve-lab2 "$WORDPRESS_BACKEND" "$WORDPRESS_IP" "$WORDPRESS_PORT" "$VIRTINC_HOST" && touch "$LAB_STATE/solved-lab2"
