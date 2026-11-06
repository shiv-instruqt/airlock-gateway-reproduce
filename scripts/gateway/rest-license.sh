# Runs ON the Gateway: GW_LICENSE_B64=... line + rest-lib.sh + this (see solve-job.sh).
# Skip path only (the participant pastes the license in LAB 01). Applies the license if
# none is active and activates (same calls as ACE configure-waf-license).
lic=$(printf '%s' "${GW_LICENSE_B64:-}" | base64 -d 2>/dev/null)
case $lic in *LICENSE*) ;; *) d=$(printf '%s' "$lic" | base64 -d 2>/dev/null); case $d in *LICENSE*) lic=$d ;; esac ;; esac
[ -n "$lic" ] || { echo "RESULT failed: no license given"; exit 1; }
rest_open
expiry=$(r GET configuration/license | jq -r '.data.attributes.expiry // empty')
[ -n "$expiry" ] && { echo "RESULT ok (license already active, expiry $expiry)"; exit 0; }
r PATCH configuration/license "$(jq -n --arg l "$lic" '{data: {type: "license", id: "2", attributes: {license: $l}}}')" >/dev/null
case $REST_CODE in 2??) ;; *) echo "RESULT failed: PATCH license HTTP $REST_CODE"; exit 1 ;; esac
r POST configuration/configurations/activate '{"comment":"LAB 01: Gateway license (skip)","options":{"ignoreOutdatedConfiguration":true}}' >/dev/null
case $REST_CODE in 2??) echo "RESULT ok" ;; *) echo "RESULT failed: activate HTTP $REST_CODE"; exit 1 ;; esac
