# Airlock Reporting Bundle

This bundle contains configuration templates and scripts that can be used to set up an EK stack
(Elasticsearch and Kibana) for Airlock IAM/WAF reporting.
The scripts assume that Elasticsearch and Kibana habe been deployed and are running.

The provided `docker-compose.yml` file is intended to be used for testing and demonstration purposes only. 

## Setup

Separate scripts are provided for Elasticsearch and Kibana.

By default EK will be set up for IAM and WAF. To disable one or the other set `NO_IAM=1` or `NO_WAF=1` in the
envrionment. E.g. `export NO_WAF=1`.

Using the Elastisearch script is strongly recommended.

```sh
./setup-ek/elasticsearch/setup-elasticsearch.sh 'http://elasticsearch.example.com:9200'
```

The Kibana setup can also be done in the Kibana UI, by importing `setup-ek/kibana/*.ndjson` files in "Management > Kibana > Saved Objects".

```sh
./setup-ek/kibana/setup-kibana.sh 'http://kibana.example.com:5601'
# Or using a Kibana "Space" named "airlock"
./setup-ek/kibana/setup-kibana.sh 'http://kibana.example.com:5601/s/airlock'
```

# Docker Compose

After running `docker-compose up --build` Elasticsearch should be available on `localhost:9200`
and Kibana on `localhost:5601`. The EK setup scripts will run automatically as part of the stack.

