# External reporting (30-10-2026): the Gateway image airlock-gw-8.6.1-lab-extrep runs with
# "Local reporting" off (no elasticsearch/kibana inside the VM, ~2 GB RAM instead of
# ~5.7 GB) and "Remote reporting" on, sending to http://elasticsearch.container.sandbox.internal:9200
# (set at lab start by setup.hcl: REST PATCH /configuration/reporting + activate).
#
# The stack is the Airlock reporting bundle 2.6.0 (Techzone) as in its docker-compose.yml:
# elasticsearch + kibana 9.4.4 (public images), and instead of the bundle's init image
# (Dockerfile = ubi-minimal + setup-ek files) the public ubi-minimal image with the
# unchanged setup-ek files mounted from reporting/setup-ek (no build, no registry).
# Gateway only (NO_IAM=1): no IAM in this lab. Bundle and ES version must match.
#
# Names: <resource>.container.sandbox.internal, as for the backends (convention and DNS
# in backends.hcl). If the Gateway does not resolve the name to the address below,
# setup.hcl points Remote reporting at that fixed address instead.

resource "container" "elasticsearch" {
  image {
    name = "docker.elastic.co/elasticsearch/elasticsearch:9.4.4"
  }

  # As the bundle's compose file, minus bootstrap.memory_lock (needs a memlock ulimit;
  # irrelevant for a lab). single-node also skips the production bootstrap checks
  # (vm.max_map_count).
  environment = {
    "node.name"                          = "elasticsearch"
    "discovery.type"                     = "single-node"
    "network.host"                       = "0.0.0.0"
    "xpack.security.enabled"             = "false"
    "xpack.security.transport.ssl.enabled" = "false"
    "ES_JAVA_OPTS"                       = "-Xms512m -Xmx512m"
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.25"
    aliases    = ["elasticsearch.airlock.academy", "elasticsearch"]
  }

  resources {
    cpu    = 1000
    memory = 1536
  }

  health_check {
    timeout = "300s"
    exec {
      command = ["/bin/sh", "-c", "curl -fsS -m 5 http://127.0.0.1:9200/_cluster/health | grep -qE '\"status\":\"(green|yellow)\"'"]
    }
  }

  port {
    local = 9200
  }
}

# No health check: kibana needs 1-2 min, and the lab should open meanwhile.
resource "container" "kibana" {
  image {
    name = "docker.elastic.co/kibana/kibana:9.4.4"
  }

  environment = {
    "ELASTICSEARCH_HOSTS" = "http://elasticsearch.container.sandbox.internal:9200"
    # Behind the Instruqt service-tab proxy Kibana's strict CSP check fails
    # ("Please upgrade your browser"), lab run 30-10-2026.
    "CSP_STRICT" = "false"
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.26"
    aliases    = ["kibana.airlock.academy", "kibana"]
  }

  resources {
    cpu    = 1000
    memory = 1024
  }

  port {
    local = 5601
  }
}

# Loads the bundle's index templates, ingest pipelines and Kibana dashboards once both
# are up (reporting/init-ek.sh waits for the network and the stack, then stays running).
# Check: cat /tmp/ek-status (ok | failed) and /tmp/ek-setup.log in this container.
resource "container" "init_ek" {
  image {
    name = "registry.access.redhat.com/ubi8/ubi-minimal:8.10-1086"
  }

  entrypoint = ["/bin/bash", "-c"]
  command    = ["bash /opt/reporting/init-ek.sh"]

  environment = {
    "ELASTICSEARCH_URL" = "http://elasticsearch.container.sandbox.internal:9200"
    "KIBANA_URL"        = "http://kibana.container.sandbox.internal:5601"
    "NO_IAM"            = "1"
  }

  volume {
    source      = "reporting"
    destination = "/opt/reporting"
    read_only   = true
  }

  volume {
    source      = "reporting/setup-ek"
    destination = "/setup-ek"
    read_only   = true
  }

  network {
    id = resource.network.main.meta.id
  }

  resources {
    cpu    = 200
    memory = 128
  }
}
