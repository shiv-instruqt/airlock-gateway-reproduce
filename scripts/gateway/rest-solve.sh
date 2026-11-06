# Runs ON the Gateway: rest-lib.sh + this,
#   args = <ntp server> <virtinc back-end host> <virtinc lab ip> <gateway ip> <vhost host name>.
# LAB 01 solution, idempotent: NTP server, virtual host, back-end group, medium security
# mapping, one activation. Last line: "RESULT ok" or "RESULT failed ...".
NTP=$1 VIRTINC=$2 VIRTINC_IP=$3 GW_IP=$4 HOST=$5
die() { echo "RESULT failed: $1"; exit 1; }
rest_open
changed=

ns=$(r GET configuration/network-services)
if ! printf '%s' "$ns" | jq -e --arg ip "$NTP" '[.data.attributes.ntp | .. | strings | split(",")[] | gsub("\\s"; "")] | any(. == $ip)' >/dev/null; then
  servers=$(printf '%s' "$ns" | jq -c --arg ip "$NTP" '[(.data.attributes.ntp.servers // [])[] | select(. != "")] + [$ip]')
  r PATCH configuration/network-services "$(jq -n --argjson s "$servers" '{data: {type: "network-services", attributes: {ntp: {servers: $s}}}}')" >/dev/null
  case $REST_CODE in 2??) changed=1 ;; *) die "PATCH network-services HTTP $REST_CODE" ;; esac
fi

vh=$(r GET configuration/virtual-hosts | jq -c --arg host "$HOST" '[.data[] | select(([.attributes.hostName // empty] + (.attributes.aliasNames // [])) | map(ascii_downcase) | any(. == $host))]')
vid=$(printf '%s' "$vh" | jq -r '.[0].id // empty')
net='{http: {enabled: true, port: 80, httpsRedirectEnforced: true}, https: {enabled: true, port: 443}}'
if [ -z "$vid" ]; then
  body=$(jq -n --arg ip "$GW_IP/24" --arg host "$HOST" "{data: {type: \"virtual-host\", attributes: {name: \"virtinc\",
    hostName: \$host, aliasNames: [], strictlyMatchFullyQualifiedDomainName: false,
    networkInterface: ($net + {externalLogicalInterfaceName: \"INTERNAL\", ipV4Address: \$ip})}}}")
  vid=$(r POST configuration/virtual-hosts "$body" | jq -r '.data.id // empty')
  [ -n "$vid" ] || die "POST virtual-hosts HTTP $REST_CODE"
  changed=1
# Strict FQDN matching off: the Instruqt proxy sends SNI <vm>.vm.sandbox.internal, not the tab host.
elif ! printf '%s' "$vh" | jq -e '.[0].attributes | (.networkInterface | .http.enabled and .https.enabled and .http.httpsRedirectEnforced)
    and (.strictlyMatchFullyQualifiedDomainName != true)' >/dev/null; then
  ni=$(printf '%s' "$vh" | jq -c ".[0].attributes.networkInterface * $net")
  r PATCH "configuration/virtual-hosts/$vid" "$(jq -n --arg id "$vid" --argjson ni "$ni" '{data: {type: "virtual-host", id: $id, attributes: {networkInterface: $ni, strictlyMatchFullyQualifiedDomainName: false}}}')" >/dev/null
  changed=1
fi
# test.certificate by name, then by the PEM's CN; else the first certificate. The imported
# lab image has only the built-in default (id -1000, name null, verified 04-11-2026).
certs=$(r GET configuration/ssl-certificates)
cid=$(printf '%s' "$certs" | jq -r '[.data[] | select(.attributes.name == "test.certificate")] | .[0].id // empty')
[ -n "$cid" ] || cid=$(printf '%s' "$certs" | jq -r '.data[] | "\(.id) \(.attributes.certificate // "" | @base64)"' | while read -r id pem; do
  printf '%s' "$pem" | base64 -d 2>/dev/null | openssl x509 -noout -subject 2>/dev/null | grep -q 'CN *= *test.certificate' && { echo "$id"; break; }
done)
[ -n "$cid" ] || cid=$(printf '%s' "$certs" | jq -r '.data[0].id // empty')
[ -n "$cid" ] || die "no SSL certificate on the Gateway"
r PATCH "configuration/virtual-hosts/$vid/relationships/ssl-certificate" "$(jq -n --arg id "$cid" '{data: {type: "ssl-certificate", id: $id}}')" >/dev/null
case $REST_CODE in 2??) changed=1 ;; *) die "PATCH ssl-certificate HTTP $REST_CODE" ;; esac

# An existing group on the name or (older runs) the lab IP counts; a new one uses the name.
bid=$(r GET configuration/back-end-groups | jq -r --arg h "$VIRTINC" --arg ip "$VIRTINC_IP" '[.data[] | select((.attributes.backendHosts // []) | any((.hostName // "" | ascii_downcase) as $x | $x == $h or $x == $ip))] | .[0].id // empty')
if [ -z "$bid" ]; then
  body=$(jq -n --arg h "$VIRTINC" '{data: {type: "back-end-group", attributes: {name: "virtinc", backendHosts: [{protocol: "HTTP", hostName: $h, port: 8080}]}}}')
  bid=$(r POST configuration/back-end-groups "$body" | jq -r '.data.id // empty')
  [ -n "$bid" ] || die "POST back-end-groups HTTP $REST_CODE"
  changed=1
fi

maps=$(r GET configuration/mappings)
linked=$(printf '%s' "$maps" | jq -r --arg v "$vid" --arg b "$bid" 'def ids($k): [(.relationships // {})[$k].data // [] | if type == "array" then .[] else . end | select(. != null) | .id];
  [.data[] | select((ids("virtual-hosts") | index($v)) and (ids("back-end-groups") | index($b)))] | .[0].id // empty')
if [ -z "$linked" ]; then
  mid=$(printf '%s' "$maps" | jq -r '.data[] | select(.attributes.name == "virtinc") | .id' | head -1)
  if [ -z "$mid" ]; then
    tid=$(r GET configuration/templates/mappings | jq -r '.data[] | select(.attributes.application == "Medium Security Mapping") | .id' | head -1)
    [ -n "$tid" ] || die "template Medium Security Mapping not found"
    mid=$(r POST configuration/mappings/create-from-template "$(jq -n --arg t "$tid" '{data: {type: "create-mapping-from-template", attributes: {id: $t}}}')" | jq -r '.data.id // empty')
    [ -n "$mid" ] || die "create-from-template HTTP $REST_CODE"
    r PATCH "configuration/mappings/$mid" "$(jq -n --arg id "$mid" '{data: {type: "mapping", id: $id, attributes: {name: "virtinc", entryPath: {value: "/"}}}}')" >/dev/null
  fi
  r PATCH "configuration/mappings/$mid/relationships/virtual-hosts" "$(jq -n --arg id "$vid" '{data: [{type: "virtual-host", id: $id}]}')" >/dev/null
  r PATCH "configuration/mappings/$mid/relationships/back-end-groups" "$(jq -n --arg id "$bid" '{data: [{type: "back-end-group", id: $id}]}')" >/dev/null
  changed=1
fi

if [ -n "$changed" ]; then
  out=$(r POST configuration/configurations/activate '{"comment":"LAB 01 solved (skip)","options":{"ignoreOutdatedConfiguration":true}}')
  case $REST_CODE in 2??) ;; *) echo "$out"; die "activate HTTP $REST_CODE" ;; esac
fi
echo "RESULT ok"
