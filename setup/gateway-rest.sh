#!/bin/bash
# Runs ON the Gateway as root, piped over SSH by gateway-setup.sh:
#   bash -s -- <es url> <es fallback url> <dns server>
# Same auth path as ACE (airlock-academy-manager, roles configure-waf/configure-bearer-token
# + configure-waf-license): a JWT from the local airlock-user-manager-tool, REST on
# localhost:8080, so no API key and no credential leaves the Gateway. Here the JWT belongs
# to the built-in airlock-configuration-cli user. `-J` prints a JSON object (not an array),
# so the token is searched recursively (the array form gave an empty JWT -> 403, lab run 30-10-2026).
# Prints progress; the last line is "RESULT ok url=..." or "RESULT failed ...".
set -u
ES_URL=$1
ES_FALLBACK_URL=$2
DNS=$3
B=http://localhost:8080/airlock/rest
J=$(mktemp)
trap 'rm -f "$J"' EXIT

jwt() {
  local U=airlock-configuration-cli T=/opt/airlock/base/bin/airlock-user-manager-tool tok
  tok=$($T -J -u $U -j | jq -r '[.. | objects | select(has("token")) | .token] | first // empty')
  if [ -z "$tok" ] || $T -J -u $U -j | jq -r '[.. | objects | select(has("tokenExpired")) | .tokenExpired | tostring] | .[]' | grep -q true; then
    $T -s -u $U -j -r airlock-rest >/dev/null
    tok=$($T -J -u $U -j | jq -r '[.. | objects | select(has("token")) | .token] | first // empty')
  fi
  printf '%s' "$tok"
}
JWT=$(jwt)
[ -n "$JWT" ] || { echo "RESULT failed: no JWT for airlock-configuration-cli"; exit 1; }
# c <expected status> <curl args...>: one REST call, fails on another status
c() {
  local want=$1; shift
  local got
  got=$(curl -s -o /tmp/rest-body.$$ -w '%{http_code}' -b "$J" -c "$J" \
    -H "Authorization: Bearer $JWT" -H 'Accept: application/json' "$@")
  if [ "$got" != "$want" ]; then
    echo "HTTP $got (want $want) for $*"; head -c 300 /tmp/rest-body.$$; echo
    rm -f /tmp/rest-body.$$; return 1
  fi
  rm -f /tmp/rest-body.$$
}

# The Config Center REST API answers some time after SSH; wait up to 10 min.
i=0
until c 200 -X POST $B/session/create >/dev/null 2>&1; do
  i=$((i + 1))
  [ "$i" -ge 60 ] && { echo "RESULT failed: REST API not ready after 600 s"; exit 1; }
  sleep 10
done
echo "REST ready after ~$((i * 10)) s"
c 204 -X POST $B/configuration/configurations/load-active || { echo "RESULT failed: load-active"; exit 1; }

# DNS: the lab router only. The Gateway writes resolv.conf from its Network Services DNS
# setting; with a second, public server ("options rotate") the lab's internal names
# (<resource>.container.sandbox.internal, the back-end hosts) failed at random.
# REST: network-services dns.ipAddresses[] (8.6 REST reference, NotebookLM 30-10-2026).
# dhcpInterface would take the servers from DHCP instead; cleared with "" (UNVERIFIED that
# "" is accepted, so retried without it; udhcpd also hands out only the router).
# A failure here is logged, not fatal: reporting falls back to the address below.
dns_state=failed
ns=$(curl -s -b "$J" -c "$J" -H "Authorization: Bearer $JWT" -H 'Accept: application/json' $B/configuration/network-services)
printf '%s' "$ns" | jq -e .data >/dev/null 2>&1 || ns='{}'
echo "DNS before:$(printf '%s' "$ns" | jq -c '.data.attributes.dns // {}')"
for drop in dhcp keep; do
  dns=$(printf '%s' "$ns" | jq -c --arg ip "$DNS" --arg drop "$drop" \
    '(.data.attributes.dns // {}) + {ipAddresses: [$ip]} | if $drop == "dhcp" then .dhcpInterface = "" else del(.dhcpInterface) end')
  if c 200 -X PATCH -H 'Content-Type: application/json' $B/configuration/network-services \
      -d "$(jq -nc --argjson d "$dns" '{data: {type: "network-services", attributes: {dns: $d}}}')"; then
    dns_state=ok; echo "DNS set ($drop): $dns"; break
  fi
done

# Remote reporting target: the DNS alias only if the Gateway resolves it to the lab
# container's address, else that fixed address. Resolving alone proves nothing:
# *.airlock.academy is a public wildcard record (checked 30-10-2026), so without the lab
# DNS the name points to a public host.
url=$ES_URL
host=${ES_URL#*://}; host=${host%%[:/]*}
want=${ES_FALLBACK_URL#*://}; want=${want%%[:/]*}
got=$(getent hosts "$host" | awk '{print $1; exit}')
if [ "$got" != "$want" ]; then
  echo "$host resolves to '${got:-nothing}' on the Gateway, not the lab address; using $ES_FALLBACK_URL"
  url=$ES_FALLBACK_URL
fi

c 200 -X PATCH -H 'Content-Type: application/json' $B/configuration/reporting \
  -d "{\"data\":{\"type\":\"reporting\",\"attributes\":{\"localEnabled\":false,\"remote\":{\"enabled\":true,\"serverUrls\":[\"$url\"]}}}}" \
  || { echo "RESULT failed: PATCH reporting"; exit 1; }

# No license here: in LAB 01 the participant pastes the license (from the team secret, shown
# on the instructions page) and activates it, as in the training guide (30-10-2026).

c 200 -X POST -H 'Content-Type: application/json' $B/configuration/configurations/activate \
  -d '{"comment":"Lab start: DNS server = lab router, Local reporting off, Remote reporting to the lab elasticsearch","options":{"ignoreOutdatedConfiguration":true}}' \
  || { echo "RESULT failed: activate"; exit 1; }
c 200 -X POST $B/session/terminate >/dev/null 2>&1 || true
sleep 15
echo "elasticsearch/kibana: $(systemctl is-active elasticsearch kibana | tr '\n' ' ')"
echo "resolv.conf: $(grep -E '^(nameserver|options)' /etc/resolv.conf | tr '\n' ' ')"
for h in virtinc wordpress mysql; do
  echo "$h.container.sandbox.internal -> $(getent hosts $h.container.sandbox.internal | awk '{print $1; exit}')"
done
echo "RESULT ok url=$url dns=$dns_state"
