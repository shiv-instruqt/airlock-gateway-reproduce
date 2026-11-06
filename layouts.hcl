resource "layout" "main" {
  column {
    width = "30"

    instructions {
      title  = "Instructions"
      active = true
    }
  }

  column {
    width = "70"

    tab "config_center" {
      title  = "Config Center"
      target = resource.service.config-center
      active = true
    }

    tab "config_center_window" {
      title  = "Config Center (new browser tab)"
      target = resource.external_website.config_center_window
    }

    tab "config_center_vb" {
      title  = "Config Center (virtual browser)"
      target = resource.virtual_browser.config_center_vb
    }

    tab "config_center_vb_internal" {
      title  = "Config Center (virtual browser, internal)"
      target = resource.virtual_browser.config_center_vb_internal
    }

    tab "virtinc" {
      title  = "Virtinc (via Gateway)"
      target = resource.service.virtinc
    }

    tab "virtinc_window" {
      title  = "Virtinc (new browser tab)"
      target = resource.external_website.virtinc_window
    }

    tab "kibana" {
      title  = "Kibana"
      target = resource.service.kibana
    }

    tab "tools" {
      title  = "Tools Shell"
      target = resource.terminal.tools
    }

    tab "gateway" {
      title  = "Gateway Shell"
      target = resource.terminal.gateway
    }
  }
}
