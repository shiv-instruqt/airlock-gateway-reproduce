#!/usr/bin/env bash
set -e -o pipefail

KIBANA_URL="${1:-$KIBANA_URL}"
KIBANA_URL="${KIBANA_URL%/}"

if [[ -z "$KIBANA_URL" ]]; then
	echo "Usage: $0 KIBANA_URL"
	echo "       Set NO_WAF=1 or NO_IAM=1 to setup EK for WAF/IAM only respectively"
	exit 42
fi

cd "$(dirname "$0")"

function kibana_import() {
	curl  --silent \
		--insecure \
		--include \
		--show-error \
		--fail \
		--request 'POST' \
		--header 'kbn-xsrf: true' \
		--write-out '\n' \
		--form "file=@$1" \
		"$KIBANA_URL/api/saved_objects/_import?overwrite=true"
}

set -x

if ((NO_IAM != 1)); then
	kibana_import './airlock-iam-kibana.ndjson'
fi

if ((NO_WAF != 1)); then
	kibana_import './airlock-gateway-kibana.ndjson'
fi

echo -e '\n\e[0;32mKibana setup completed\e[0m\n'
