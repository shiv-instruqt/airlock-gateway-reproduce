resource "service" "config-center" {
  target = resource.vm.gateway
  scheme = "https"
  port   = 8443
  path   = "/airlock/"
}

# The participant's client view: the Gateway's virtual host port 443 through the
# Instruqt proxy.
# Public host of a service tab (verified 30-10-2026 from the tab iframes):
# service-<hash>-<session>.<domain>, hash = first 6 hex chars of md5("resource.service.<name>"),
# NOT the service name (service-<name>-... returns an Instruqt 404).
#   printf '%s' resource.service.virtinc | md5 | cut -c1-6   -> b03acb
#   config-center -> e0b9cd, kibana -> ef8e94
# Renaming a service changes its hash. It is the Virtinc virtual host's host name in LAB 01.
local "virtinc_host" {
  value = "service-b03acb-${variable.instruqt_session_id}.${variable.instruqt_sandbox_domain}"
}

resource "service" "virtinc" {
  target = resource.vm.gateway
  scheme = "https"
  port   = 443
  path   = "/"
}

# The same Virtinc service in its own browser tab (30-10-2026): inside the embedded tab
# the browser's dev tools only see the Instruqt frame, and LAB 03 needs page source and
# network tab of the app behind the Gateway. resource.service.<x>.url (documented) does not
# validate (CLI 30-10-2026), so the URL is the hand-built tab host (see virtinc_host).
# Config Center in its own browser tab (30-10-2026): the embedded tab cannot download
# files (config export).
resource "external_website" "config_center_window" {
  url                = "https://service-e0b9cd-${variable.instruqt_session_id}.${variable.instruqt_sandbox_domain}/airlock/"
  open_in_new_window = true
}

# Test 05-11-2026: Instruqt's virtual browser (a real Chromium streamed into the tab,
# not an iframe), meant to replace the new-browser-tab workaround: page source, network
# tab and file downloads; home/refresh/address bar in a toolbar. Public host as above
# (only exists while the config_center service tab is in the layout); the second tab
# checks whether the virtual browser reaches the VM by its sandbox-internal name.
resource "virtual_browser" "config_center_vb" {
  url = "https://service-e0b9cd-${variable.instruqt_session_id}.${variable.instruqt_sandbox_domain}/airlock/"
}

resource "virtual_browser" "config_center_vb_internal" {
  url = "https://gateway.vm.sandbox.internal:8443/airlock/"
}

resource "external_website" "virtinc_window" {
  url                = "https://${local.virtinc_host}/"
  open_in_new_window = true
}

resource "terminal" "tools" {
  target            = resource.container.tools
  shell             = "/bin/sh"
  working_directory = "/"
}

# Only works if the imported image still carries the Instruqt agent.
resource "terminal" "gateway" {
  target = resource.vm.gateway
  shell  = "/bin/sh"
}

# Kibana of the external reporting stack (reporting.hcl), with the bundle's Airlock
# Gateway dashboards once init_ek has run.
resource "service" "kibana" {
  target = resource.container.kibana
  scheme = "http"
  port   = 5601
  path   = "/app/dashboards"
}
