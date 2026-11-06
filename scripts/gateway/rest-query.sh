# Runs ON the Gateway: rest-lib.sh + this, args = configuration paths. Loads the ACTIVE
# configuration and prints one JSON object {"<path>": <response>, ...} on stdout.
rest_open
out='{}'
for p in "$@"; do
  body=$(r GET "configuration/$p")
  # All paths are queried for every check (gw.sh gw_query), so a path without a JSON answer
  # (e.g. no license yet) becomes null for the check that reads it instead of failing all.
  out=$(printf '%s' "$body" | jq -c --arg k "$p" --argjson o "$out" '$o + {($k): .}' 2>/dev/null) \
    || { echo "GET $p: no JSON (HTTP $REST_CODE), null" >&2; out=$(jq -cn --arg k "$p" --argjson o "$out" '$o + {($k): null}'); }
done
printf '%s\n' "$out"
