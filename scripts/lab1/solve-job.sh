#!/bin/sh
# Background job started by solve.sh: license (only if none is active; normally pasted by
# the participant), then the LAB 01 REST solve on the Gateway; frees the lock at the end.
. /opt/lab/scripts/lib/gw.sh
trap 'rmdir "$LAB_STATE/solve.lock" 2>/dev/null' EXIT
if [ -n "${GW_LICENSE:-}" ]; then
  GW_PREFIX="GW_LICENSE_B64='$(printf '%s' "$GW_LICENSE" | base64 | tr -d '\n')'"
  gw_run license
  GW_PREFIX=
fi
gw_run solve "$NTP_SERVER" "$VIRTINC_BACKEND" "$VIRTINC_IP" "$(gateway_ip)" "$VIRTINC_HOST" && touch "$LAB_STATE/solved"
