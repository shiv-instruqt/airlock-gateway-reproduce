#!/bin/sh
# Lab-start step in the gateway_setup container (alpine): waits for the network and the
# Gateway's SSH, then runs gateway-rest.sh on the Gateway as root (password from a team
# secret, see setup.hcl). Status in /tmp/gateway-setup-status (running | ok | failed),
# log in /tmp/gateway-setup.log, copied to the Gateway at the end. Never exits, so the container does not count as failed.
STATUS=/tmp/gateway-setup-status
LOG=/tmp/gateway-setup.log
: "${GW_HOST:?}" "${ES_URL:?}" "${ES_FALLBACK_URL:?}" "${GW_DNS:?}" "${GW_ROOT_PASSWORD:?}"
echo running > $STATUS
log() { echo "[gateway-setup $(date +%T)] $*" | tee -a $LOG; }
# The container has no tab: copy the log onto the Gateway (Gateway Shell: /var/log/lab-gateway-setup.log).
finish() {
  echo "$1" > $STATUS; log "status: $1"
  [ -n "${SSH:-}" ] && $SSH 'cat > /var/log/lab-gateway-setup.log' < $LOG 2>/dev/null
  exec sleep infinity
}

# The lab network is attached shortly after the container starts; apk needs it.
i=0
until nslookup dl-cdn.alpinelinux.org >/dev/null 2>&1; do
  i=$((i + 1)); [ "$i" -ge 60 ] && { log "no network after 120 s"; finish failed; }
  sleep 2
done
apk add --no-cache openssh-client sshpass >/dev/null 2>&1 || { log "apk add failed"; finish failed; }

export SSHPASS="$GW_ROOT_PASSWORD"
SSH="sshpass -e ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o ConnectTimeout=8 -o LogLevel=ERROR root@$GW_HOST"
# Cold image: first boot takes a few minutes (DHCP, installation config, tomcat). Wait up to 20 min.
i=0
until $SSH true 2>/dev/null; do
  i=$((i + 1)); [ "$i" -ge 120 ] && { log "no SSH to $GW_HOST after 20 min"; finish failed; }
  sleep 10
done
log "SSH to $GW_HOST ok after ~$((i * 10)) s"

$SSH bash -s -- "$ES_URL" "$ES_FALLBACK_URL" "$GW_DNS" < /opt/setup/gateway-rest.sh >> $LOG 2>&1
if tail -1 $LOG | grep -q '^RESULT ok'; then finish ok; else finish failed; fi
