# Runs ON the Gateway: rest-lib.sh + this,
#   args = <wordpress back-end host> <wordpress lab ip> <port> <vhost host name>.
# LAB 02 solution, idempotent: back-end group "wordpress", medium security mapping
# "wordpress" (entry path /wordpress/, back-end path /, log only, integration mode) on the
# LAB 01 virtual host, the sample rewrites, one activation. Needs LAB 01's virtual host
# (solve-job.sh runs the LAB 01 solve first). Last line: "RESULT ok" or "RESULT failed ...".
WP=$1 WP_IP=$2 PORT=$3 HOST=$4
die() { echo "RESULT failed: $1"; exit 1; }
rest_open
changed=

vid=$(r GET configuration/virtual-hosts | jq -r --arg host "$HOST" '[.data[] | select(([.attributes.hostName // empty] + (.attributes.aliasNames // [])) | map(ascii_downcase) | any(. == $host))] | .[0].id // empty')
[ -n "$vid" ] || die "no virtual host $HOST (LAB 01)"

bid=$(r GET configuration/back-end-groups | jq -r --arg h "$WP" --arg ip "$WP_IP" '[.data[] | select((.attributes.backendHosts // []) | any((.hostName // "" | ascii_downcase) as $x | $x == $h or $x == $ip))] | .[0].id // empty')
if [ -z "$bid" ]; then
  body=$(jq -n --arg h "$WP" --argjson p "$PORT" '{data: {type: "back-end-group", attributes: {name: "wordpress", backendHosts: [{protocol: "HTTP", hostName: $h, port: $p}]}}}')
  bid=$(r POST configuration/back-end-groups "$body" | jq -r '.data.id // empty')
  [ -n "$bid" ] || die "POST back-end-groups HTTP $REST_CODE"
  changed=1
fi

maps=$(r GET configuration/mappings)
mid=$(printf '%s' "$maps" | jq -r --arg v "$vid" --arg b "$bid" 'def ids($k): [(.relationships // {})[$k].data // [] | if type == "array" then .[] else . end | select(. != null) | .id];
  [.data[] | select((ids("virtual-hosts") | index($v)) and (ids("back-end-groups") | index($b)))] | .[0].id // empty')
if [ -z "$mid" ]; then
  mid=$(printf '%s' "$maps" | jq -r '.data[] | select(.attributes.name == "wordpress") | .id' | head -1)
  if [ -z "$mid" ]; then
    tid=$(r GET configuration/templates/mappings | jq -r '.data[] | select(.attributes.application == "Medium Security Mapping") | .id' | head -1)
    [ -n "$tid" ] || die "template Medium Security Mapping not found"
    mid=$(r POST configuration/mappings/create-from-template "$(jq -n --arg t "$tid" '{data: {type: "create-mapping-from-template", attributes: {id: $t}}}')" | jq -r '.data.id // empty')
    [ -n "$mid" ] || die "create-from-template HTTP $REST_CODE"
  fi
  r PATCH "configuration/mappings/$mid/relationships/virtual-hosts" "$(jq -n --arg id "$vid" '{data: [{type: "virtual-host", id: $id}]}')" >/dev/null
  r PATCH "configuration/mappings/$mid/relationships/back-end-groups" "$(jq -n --arg id "$bid" '{data: [{type: "back-end-group", id: $id}]}')" >/dev/null
  changed=1
fi

# The rewrites of the Training Materials sample (Solutions/wordpress_rewrites.txt), as
# "Rewrite Response Body (any content)" for HTML responses: plain, JSON-escaped (\/) and
# URL-encoded back-end URLs, and the theme images hard-coded to wordpress.teacher.virtinc.com.
# The whole application object is sent back so a PATCH cannot reset its other settings.
m=$(r GET "configuration/mappings/$mid")
rules=$(jq -n \
  --arg p1 '%BACKENDPROTOCOL%://%BACKENDHOST%' --arg r1 '%ENTRYDIR%' \
  --arg p2 '%BACKENDPROTOCOL%:\\/\\/%BACKENDHOST%' --arg r2 '\\/%ENTRYPATH%' \
  --arg p3 '%BACKENDPROTOCOL%%3A%2F%2F%BACKENDHOSTNAME%%3A%BACKENDPORT%' --arg r3 '%2F%ENTRYPATH%' \
  --arg p4 'https://wordpress.teacher.virtinc.com' --arg r4 '%ENTRYDIR%' \
  '[[$p1, $r1], [$p2, $r2], [$p3, $r3], [$p4, $r4]] | map({enabled: true, contentType: "text/html.*",
    contentPattern: {pattern: .[0], caseIgnored: true}, replaceWith: .[1]})')
attrs=$(printf '%s' "$m" | jq -c --argjson rules "$rules" '.data.attributes as $a
  | ($a.application.response.body.rewrites // []) as $have
  | {name: "wordpress", entryPath: (($a.entryPath // {}) + {value: "/wordpress/"}), backendPath: "/",
     threatHandling: "NOTIFY", operationalMode: "INTEGRATION",
     application: (($a.application // {}) | .response.body.rewrites = ($have + [$rules[] | select(.contentPattern.pattern as $p | $have | all(.contentPattern.pattern != $p))]))}')
[ -n "$attrs" ] || die "GET mappings/$mid: no JSON (HTTP $REST_CODE)"
if ! printf '%s' "$m" | jq -e --argjson want "$attrs" '.data.attributes | .name == $want.name and .entryPath == $want.entryPath
    and .backendPath == $want.backendPath and .threatHandling == $want.threatHandling and .operationalMode == $want.operationalMode
    and .application == $want.application' >/dev/null; then
  r PATCH "configuration/mappings/$mid" "$(jq -n --arg id "$mid" --argjson a "$attrs" '{data: {type: "mapping", id: $id, attributes: $a}}')" >/dev/null
  case $REST_CODE in 2??) changed=1 ;; *) die "PATCH mappings/$mid HTTP $REST_CODE" ;; esac
fi

if [ -n "$changed" ]; then
  r POST configuration/configurations/activate '{"comment":"LAB 02 solved (skip)","options":{"ignoreOutdatedConfiguration":true}}' >/dev/null
  case $REST_CODE in 2??) ;; *) die "activate HTTP $REST_CODE" ;; esac
fi
echo "RESULT ok"
