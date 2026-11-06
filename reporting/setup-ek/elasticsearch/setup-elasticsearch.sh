#!/usr/bin/env bash
set -e -o pipefail

ELASTICSEARCH_URL="${1:-$ELASTICSEARCH_URL}"
ELASTICSEARCH_URL="${ELASTICSEARCH_URL%/}"

if [[ -z "$ELASTICSEARCH_URL" ]]; then
	echo "Usage: $0 ELASTICSEARCH_URL"
	echo "       Set NO_WAF=1 or NO_IAM=1 to setup EK for WAF/IAM only respectively"
	exit 42
fi

cd "$(dirname "$0")"

function curl_es() {
	curl  --silent \
		--insecure \
		--include \
		--show-error \
		--fail \
		--request "$1" \
		--header 'kbn-xsrf: true' \
		--header 'Content-Type: application/json' \
		--data "@$3" \
		--write-out '\n' \
		"$ELASTICSEARCH_URL$2"
}

set -x

curl_es 'PUT' '/_component_template/airlock-settings' './airlock-settings.json'

if ((NO_IAM != 1)); then
	curl_es 'PUT' '/_ingest/pipeline/airlock-iam-default-pipeline' './airlock-iam-ingest-pipeline.json'
	curl_es 'PUT' '/_component_template/airlock-iam-fields' './airlock-iam-fields.json'
	curl_es 'PUT' '/_component_template/airlock-iam-settings' './airlock-iam-settings.json'
	curl_es 'PUT' '/_index_template/airlock-iam' './airlock-iam-template.json'
fi

if ((NO_WAF != 1)); then
	curl_es 'PUT' '/_ingest/pipeline/airlock-gateway-default-pipeline' './airlock-gateway-ingest-pipeline.json'
	curl_es 'PUT' '/_component_template/airlock-gateway-fields' './airlock-gateway-fields.json'
	curl_es 'PUT' '/_component_template/airlock-gateway-mlsvc-fields' './airlock-gateway-mlsvc-fields.json'
	curl_es 'PUT' '/_component_template/airlock-gateway-settings' './airlock-gateway-settings.json'
	curl_es 'PUT' '/_index_template/airlock-gateway' './airlock-gateway-template.json'
fi

echo -e '\n\e[0;32mElasticsearch setup completed\e[0m\n'
