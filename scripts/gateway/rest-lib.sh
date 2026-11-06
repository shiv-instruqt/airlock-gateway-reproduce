# Sourced by the rest-*.sh scripts, which run ON the Gateway as root (piped over SSH,
# concatenated: cat rest-lib.sh rest-<x>.sh | ssh root@gateway bash -s -- args).
# Auth as in setup/gateway-rest.sh: JWT of airlock-configuration-cli from the local
# airlock-user-manager-tool, REST on localhost:8080. No credential leaves the Gateway.
set -u
B=http://localhost:8080/airlock/rest
J=$(mktemp)
JWT=

jwt() {
  local U=airlock-configuration-cli T=/opt/airlock/base/bin/airlock-user-manager-tool tok
  tok=$($T -J -u $U -j | jq -r '[.. | objects | select(has("token")) | .token] | first // empty')
  if [ -z "$tok" ] || $T -J -u $U -j | jq -r '[.. | objects | select(has("tokenExpired")) | .tokenExpired | tostring] | .[]' | grep -q true; then
    $T -s -u $U -j -r airlock-rest >/dev/null
    tok=$($T -J -u $U -j | jq -r '[.. | objects | select(has("token")) | .token] | first // empty')
  fi
  printf '%s' "$tok"
}

# r METHOD PATH [JSON]: prints the body, REST_CODE holds the status (stderr log line)
r() {
  local body=/tmp/rest-body.$$
  if [ -n "${3:-}" ]; then
    REST_CODE=$(curl -s -o $body -w '%{http_code}' -b "$J" -c "$J" -X "$1" -H "Authorization: Bearer $JWT" \
      -H 'Accept: application/json' -H 'Content-Type: application/json' --data "$3" "$B/$2")
  else
    REST_CODE=$(curl -s -o $body -w '%{http_code}' -b "$J" -c "$J" -X "$1" -H "Authorization: Bearer $JWT" \
      -H 'Accept: application/json' "$B/$2")
  fi
  echo "$(date +%T) $1 $2 -> $REST_CODE" >&2
  cat $body
  rm -f $body
}

rest_open() {
  JWT=$(jwt)
  [ -n "$JWT" ] || { echo "ERROR no JWT for airlock-configuration-cli" >&2; exit 2; }
  trap 'r POST session/terminate >/dev/null 2>&1; rm -f "$J"' EXIT
  r POST session/create >/dev/null
  [ "$REST_CODE" = 200 ] || { echo "ERROR session/create HTTP $REST_CODE" >&2; exit 2; }
  r POST configuration/configurations/load-active >/dev/null
  [ "$REST_CODE" = 204 ] || { echo "ERROR load-active HTTP $REST_CODE" >&2; exit 2; }
}
