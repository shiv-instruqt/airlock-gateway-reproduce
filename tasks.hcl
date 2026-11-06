# LAB 01 checks. The scripts run in the tools container, SSH to the Gateway as root and
# run the REST calls there (scripts/gateway/rest-*.sh, same auth as setup/gateway-rest.sh).
# They read the ACTIVE configuration, so a step only passes once it is activated.
# Skip runs one REST solve for the whole lab (scripts/gateway/rest-solve.sh).
# Gateway license (team secret, not MAC-bound): shown on the instructions page for the
# participant to paste (instructions.hcl), and used by the skip if no license is active.
resource "secret" "gateway_license" {
  reference = "ACADEMY_GATEWAY_LICENSE"
}

# The license condition checks the participant's pasted and activated license. The virtual host name is the public host of the
# "Virtinc (via Gateway)" tab (tabs.hcl local.virtinc_host).
resource "task" "lab1" {
  description     = "LAB 01: license, NTP server and the Virtinc virtual host, back-end group and mapping, activated and tested"
  success_message = "Virtinc is reachable through your Airlock Gateway."

  config {
    target  = resource.container.tools
    user    = "root"
    timeout = "60s"

    environment = {
      GW_ROOT_PASSWORD = resource.secret.gateway_root_password.value
      GW_LICENSE       = resource.secret.gateway_license.value
      VIRTINC_HOST     = local.virtinc_host
    }
  }

  condition "license" {
    description = "A Gateway license is active"

    check {
      script          = "scripts/lab1/check-license.sh"
      failure_message = "No active license yet. Paste the license from the instructions under System Setup >> License and activate."
    }

    solve {
      script = "scripts/lab1/solve.sh"
    }
  }

  condition "ntp" {
    description = "The NTP server is set and activated"

    check {
      script          = "scripts/lab1/check-ntp.sh"
      failure_message = "The NTP server from the instructions is not in the active configuration. Set it under System Setup >> Network Services and activate."
    }

    solve {
      script = "scripts/lab1/solve.sh"
    }
  }

  condition "virtual_host" {
    description = "The Virtinc virtual host is activated with HTTP, HTTPS and the redirect to HTTPS"

    check {
      script          = "scripts/lab1/check-vhost-exists.sh"
      failure_message = "No active virtual host with the host name of the Virtinc tab (see the instructions) yet."
    }

    check {
      script          = "scripts/lab1/check-vhost-protocols.sh"
      failure_message = "The virtual host needs HTTP and HTTPS enabled and the redirect to HTTPS."
    }

    solve {
      script = "scripts/lab1/solve.sh"
    }
  }

  condition "backend_group" {
    description = "A back-end group points to the Virtinc back-end"

    check {
      script          = "scripts/lab1/check-backend-group.sh"
      failure_message = "No active back-end group with the Virtinc back-end host from the instructions (HTTP, virtinc.container.sandbox.internal, port 8080) yet."
    }

    solve {
      script = "scripts/lab1/solve.sh"
    }
  }

  condition "mapping" {
    description = "A mapping connects the virtual host and the back-end group"

    check {
      script          = "scripts/lab1/check-mapping.sh"
      failure_message = "No active mapping is connected to both the Virtinc virtual host and the Virtinc back-end group yet."
    }

    solve {
      script = "scripts/lab1/solve.sh"
    }
  }

  condition "test" {
    description = "Virtinc answers through the Gateway"

    check {
      script          = "scripts/lab1/check-http-redirect.sh"
      failure_message = "HTTP to the Virtinc virtual host does not redirect to HTTPS yet. Did you activate?"
    }

    check {
      script          = "scripts/lab1/check-virtinc-page.sh"
      failure_message = "The Virtinc virtual host does not show the Virtinc page over HTTPS yet. Check the mapping and the back-end group, then activate."
    }

    solve {
      script = "scripts/lab1/solve.sh"
    }
  }
}

# LAB 02 (the guide's LAB 03 "Integrate and Configure WordPress"): WordPress under
# /wordpress/ on the LAB 01 virtual host, log only + integration mode, rewrites of the
# back-end address. Same pattern as LAB 01: ACTIVE configuration, one shared solve
# (scripts/lab2/solve.sh, runs the LAB 01 solve first). The page check fetches
# https://<virtinc host>/wordpress/ through the Gateway from the tools container.
resource "task" "lab2" {
  description     = "LAB 02: WordPress back-end group and log-only mapping on the Virtinc virtual host, with rewrites so the page no longer exposes the back-end address"
  success_message = "WordPress renders through your Airlock Gateway without exposing its back-end address."

  config {
    target  = resource.container.tools
    user    = "root"
    timeout = "60s"

    environment = {
      GW_ROOT_PASSWORD = resource.secret.gateway_root_password.value
      GW_LICENSE       = resource.secret.gateway_license.value
      VIRTINC_HOST     = local.virtinc_host
    }
  }

  condition "backend_group" {
    description = "A back-end group points to the WordPress back-end"

    check {
      script          = "scripts/lab2/check-backend-group.sh"
      failure_message = "No active back-end group with the WordPress back-end host from the instructions (HTTP, wordpress.container.sandbox.internal, port 8081) yet."
    }

    solve {
      script = "scripts/lab2/solve.sh"
    }
  }

  condition "mapping" {
    description = "A log-only mapping in integration mode serves WordPress under /wordpress/ on the Virtinc virtual host"

    check {
      script          = "scripts/lab2/check-mapping.sh"
      failure_message = "No active mapping with entry path /wordpress/ and back-end path / connects the Virtinc virtual host and the WordPress back-end group yet."
    }

    check {
      script          = "scripts/lab2/check-mapping-mode.sh"
      failure_message = "The WordPress mapping is not set to log only and integration mode yet. Set both and activate."
    }

    solve {
      script = "scripts/lab2/solve.sh"
    }
  }

  condition "rewrites" {
    description = "Rewrites keep the back-end address out of the WordPress page"

    check {
      script          = "scripts/lab2/check-rewrites.sh"
      failure_message = "The WordPress mapping has no active rewrite for the back-end host yet."
    }

    check {
      script          = "scripts/lab2/check-wordpress-page.sh"
      failure_message = "The WordPress page through your Gateway still contains absolute URLs with wordpress.container.sandbox.internal (or does not load). Add the missing rewrites and activate."
    }

    solve {
      script = "scripts/lab2/solve.sh"
    }
  }
}
