#!/bin/bash
# Init container for the external reporting stack: replaces the bundle's init_ek_stack
# image (no build, no registry). Runs the unchanged bundle script setup-ek.sh (mounted
# read-only at /setup-ek) once elasticsearch and kibana answer, then stays up.
#
# The lab network is attached shortly after the container starts, and elasticsearch and
# kibana need 1-2 min to come up, while setup-ek.sh itself only retries for 50 s. So
# this script does the waiting (up to 10 min), and it never exits: an exited container
# could count as failed. Result in /tmp/ek-status (ok | failed), log in /tmp/ek-setup.log.
ES=${ELASTICSEARCH_URL:?}
KB=${KIBANA_URL:?}
STATUS=/tmp/ek-status
LOG=/tmp/ek-setup.log

wait_for() {  # wait_for <description> <url> <grep pattern>
  local i=0
  until curl -fsS -m 5 "$2" 2>/dev/null | grep -q "$3"; do
    i=$((i + 1))
    if [ "$i" -ge 120 ]; then
      echo "[init-ek] $1 not ready after 600 s" | tee -a $LOG >&2
      return 1
    fi
    sleep 5
  done
  echo "[init-ek] $1 ready after ~$((i * 5)) s" | tee -a $LOG
}

echo running > $STATUS
if wait_for elasticsearch "$ES/_cluster/health" '"status":"\(green\|yellow\)"' &&
  wait_for kibana "$KB/api/status" '"level":"available"' &&
  /setup-ek/setup-ek.sh >> $LOG 2>&1; then
  echo ok > $STATUS
else
  echo failed > $STATUS
fi
echo "[init-ek] setup-ek: $(cat $STATUS)"
exec sleep infinity
