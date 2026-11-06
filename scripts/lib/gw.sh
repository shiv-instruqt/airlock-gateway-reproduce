#!/bin/sh
# Shared helpers for the LAB 01 and LAB 02 check and solve scripts. They run in the tools container
# (mounted at /opt/lab/scripts), SSH to the Gateway as root (password from the team
# secret ACADEMY_GATEWAY_ROOT_PASSWORD, env GW_ROOT_PASSWORD) and run the REST calls
# there (scripts/gateway/rest-*.sh), as setup/gateway-rest.sh does. Sourced only.

GW_HOST=${GW_HOST:-gateway}
LAB_STATE=/var/lib/lab
TASK_LOG=/var/log/lab-tasks.log
GW_SCRIPTS=/opt/lab/scripts/gateway
# Host name of the Virtinc virtual host = public host of the "Virtinc (via Gateway)" tab
# (tabs.hcl local.virtinc_host), from the task environment.
VIRTINC_HOST=$(printf '%s' "${VIRTINC_HOST:-}" | tr 'A-Z' 'a-z')
NTP_SERVER=169.254.169.123
# Back-ends by their Instruqt internal name (convention in backends.hcl); setup.hcl makes
# the lab router the Gateway's only DNS server. The checks also accept the fixed lab IP
# (backends.hcl ip_address), in case a participant types that.
VIRTINC_BACKEND=virtinc.container.sandbox.internal
VIRTINC_IP=10.60.0.21
WORDPRESS_BACKEND=wordpress.container.sandbox.internal
WORDPRESS_IP=10.60.0.22
WORDPRESS_PORT=8081

mkdir -p "$LAB_STATE"
task_name=${0##*/}

log() {
  printf '%s %s %s\n' "$(date +%H:%M:%S)" "$task_name" "$1" >> "$TASK_LOG" 2>/dev/null || true
}

fail() {
  log "fail: $1"
  echo "$1" >&2
  exit 1
}

# The container command installs the tools at start; wait for it, install if missing.
need_tools() {
  n=0
  while :; do
    missing=
    for t in curl jq ssh sshpass; do
      command -v "$t" >/dev/null 2>&1 || missing=1
    done
    [ -z "$missing" ] && return 0
    [ $n -ge 15 ] && fail "curl, jq, ssh or sshpass is missing in the tools container."
    [ $((n % 5)) -eq 4 ] && apk add --no-cache curl jq openssh-client sshpass >/dev/null 2>&1
    n=$((n + 1))
    sleep 2
  done
}

gateway_ip() {
  ip=$(getent hosts "$GW_HOST" 2>/dev/null | awk '{print $1; exit}')
  [ -n "$ip" ] || ip=$(nslookup "$GW_HOST" 2>/dev/null | awk '/^Address/ && $NF !~ /#/ {a=$NF} END {print a}')
  printf '%s' "$ip"
}

# gw_run <rest-script> [args...]: runs rest-lib.sh + rest-<script>.sh on the Gateway.
gw_run() {
  need_tools
  [ -n "${GW_ROOT_PASSWORD:-}" ] || fail "GW_ROOT_PASSWORD is not set (team secret ACADEMY_GATEWAY_ROOT_PASSWORD)."
  s=$1
  shift
  # Anything but a query changes the configuration (solve): drop the cached query.
  [ "$s" = query ] || rm -f "$QUERY_CACHE"
  # GW_PREFIX: optional shell line sent ahead of the scripts on stdin (secrets stay out of argv)
  { [ -n "${GW_PREFIX:-}" ] && printf '%s\n' "$GW_PREFIX"; cat "$GW_SCRIPTS/rest-lib.sh" "$GW_SCRIPTS/rest-$s.sh"; } | SSHPASS=$GW_ROOT_PASSWORD sshpass -e ssh \
    -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o ConnectTimeout=8 -o LogLevel=ERROR \
    "root@$GW_HOST" bash -s -- "$@" 2>> "$TASK_LOG"
}

# gw_query <path>...: the ACTIVE configuration, one JSON object keyed by path.
# One Check click runs every check script, each of which used to open its own SSH and REST
# session. The first one now queries every path the checks read (QUERY_PATHS) and caches
# it for QUERY_TTL s; checks running alongside wait for it under a lock. The arguments only
# document what the caller reads; all of it is in the cache.
QUERY_CACHE=$LAB_STATE/query.json
QUERY_LOCK=$LAB_STATE/query.lock
QUERY_TTL=15
QUERY_PATHS="license network-services virtual-hosts back-end-groups mappings"
gw_query() {
  until mkdir "$QUERY_LOCK" 2>/dev/null; do
    # A lock older than 90 s is left over from a killed check (by its age, not by how long
    # this check waited, so a slow live query keeps it).
    age=$(($(date +%s) - $(stat -c %Y "$QUERY_LOCK" 2>/dev/null || date +%s)))
    [ "$age" -ge 90 ] && rm -rf "$QUERY_LOCK"
    sleep 1
  done
  if [ ! -s "$QUERY_CACHE" ] || [ $(($(date +%s) - $(stat -c %Y "$QUERY_CACHE"))) -ge $QUERY_TTL ]; then
    # shellcheck disable=SC2086
    out=$(gw_run query $QUERY_PATHS) || { rmdir "$QUERY_LOCK" 2>/dev/null; fail "The Gateway REST API did not answer (see $TASK_LOG)."; }
    printf '%s\n' "$out" | tail -1 > "$QUERY_CACHE.$$" && mv "$QUERY_CACHE.$$" "$QUERY_CACHE"
  fi
  rmdir "$QUERY_LOCK" 2>/dev/null
  cat "$QUERY_CACHE"
}

# jq filters ------------------------------------------------------------------

# Virtual hosts whose host name or an alias is $host (the Virtinc tab's host).
JQ_VHOSTS='[.data[] | select(([.attributes.hostName // empty] + (.attributes.aliasNames // [])) | map(ascii_downcase) | any(. == $host))]'

# Back-end groups with an HTTP back-end host whose name is one of $hosts (lower case) and
# whose port is one of $ports (JSON arrays of strings).
JQ_BEGS='[.data[] | select((.attributes.backendHosts // []) | any(((.hostName // "") | ascii_downcase) as $h
  | ($hosts | any(. == $h)) and ((.port | tostring) as $p | $ports | any(. == $p)) and ((.protocol // "HTTP") | ascii_upcase) == "HTTP"))]'

# Mappings connected to one of the virtual hosts $v and one of the back-end groups $b.
JQ_LINKED='def ids($k): [(.relationships // {})[$k].data // [] | if type == "array" then .[] else . end | select(. != null) | .id];
[.data[] | select((ids("virtual-hosts") | any(. as $i | $v | index($i))) and (ids("back-end-groups") | any(. as $i | $b | index($i))))]'

# LAB 02, per mapping: entry path /wordpress/ with back-end path /; log only (threatHandling
# NOTIFY) and integration mode (operationalMode INTEGRATION), values from the 8.6 REST
# reference; an enabled rewrite (any content or HTML only) for the back-end host.
JQ_WP_DEFS='def wp_entry: ((.attributes.entryPath.value // "") | ascii_downcase | test("^\\^?/wordpress/?\\$?$"))
    and ((.attributes.backendPath // "/") | . == "/" or . == "");
  def wp_mode: .attributes.threatHandling == "NOTIFY" and .attributes.operationalMode == "INTEGRATION";
  def wp_rewrites: [((.attributes.application.response.body.rewrites // [])[] | select(.enabled != false) | .contentPattern.pattern // ""),
      ((.attributes.application.response.html.rewrites // [])[] | select(.enabled != false) | .urlPattern.pattern // "")]
    | any(test("BACKENDHOST|wordpress"; "i"));'
