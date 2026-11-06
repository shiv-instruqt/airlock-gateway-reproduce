# Lab-start step (30-10-2026): sets the Gateway's DNS server and reporting once it is up. The license is
# not set here: it is a participant step in LAB 01 (instructions.hcl, tasks.hcl).
#
# The image airlock-gw-8.6.1-lab-extrep2 is the cold lab image (no pre-warm), edited offline:
# "Local reporting" off in the installation config template, elasticsearch/kibana units
# disabled, and a cloud-init bootcmd that disables them once per instance. This step makes
# the setting a real, activated configuration and adds the Remote reporting URL.
#
# Auth as on ACE: root on the Gateway runs airlock-user-manager-tool for a JWT and calls
# REST on localhost:8080. Root password from a team secret, never in the repo.
resource "secret" "gateway_root_password" {
  reference = "ACADEMY_GATEWAY_ROOT_PASSWORD"
}

resource "container" "gateway_setup" {
  image {
    name = "alpine:3.20"
  }

  entrypoint = ["/bin/sh", "-c"]
  command    = ["sh /opt/setup/gateway-setup.sh"]

  environment = {
    "GW_HOST"          = "gateway"
    "GW_ROOT_PASSWORD" = resource.secret.gateway_root_password.value
    "ES_URL"           = "http://elasticsearch.container.sandbox.internal:9200"
    # Address fallback: Remote reporting must work even if the Gateway's DNS change failed.
    "ES_FALLBACK_URL"  = "http://10.60.0.25:9200"
    # The only DNS server the Gateway gets (lab router; resolves *.container.sandbox.internal).
    "GW_DNS"           = "10.60.0.1"
  }

  volume {
    source      = "setup"
    destination = "/opt/setup"
    read_only   = true
  }

  network {
    id = resource.network.main.meta.id
  }

  resources {
    cpu    = 100
    memory = 128
  }

  # Sandbox ready only once the Gateway is configured; a failed setup fails the start
  # instead of handing out an unconfigured Gateway.
  health_check {
    timeout = "900s"
    exec {
      command = ["/bin/sh", "-c", "grep -qx ok /tmp/gateway-setup-status"]
    }
  }
}
