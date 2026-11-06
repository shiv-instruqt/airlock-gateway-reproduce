#!/usr/bin/env bash
set -e -o pipefail

if [ -z "$ELASTICSEARCH_URL" ]; then
	echo "Environment variable ELASTICSEARCH_URL not set"
	exit 42
fi

if [ -z "$KIBANA_URL" ]; then
	echo "Environment variable KIBANA_URL not set"
	exit 42
fi

function await() {
	n=0
	until [[ $n -ge 10 ]]; do
		if curl -fisS "$1"; then
			return 0
		fi
		n=$((n + 1))
		sleep 5
	done
	echo "Unable to reach service \"$1\". Configuration could not be applied."
	return 42
}

cd "$(dirname "$0")"

await "$ELASTICSEARCH_URL/_cat/nodes"
./elasticsearch/setup-elasticsearch.sh "$ELASTICSEARCH_URL"

await "$KIBANA_URL/status"
sleep 10
./kibana/setup-kibana.sh "$KIBANA_URL"
