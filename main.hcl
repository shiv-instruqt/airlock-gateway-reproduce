resource "lab" "main" {
  title       = "Airlock Gateway 8.6.1 as a VM - external reporting PoC"
  description = "Proof of concept: boot the Airlock Gateway 8.6.1 appliance (GCP disk imported with the image builder) as a vm resource and reach its Config Center through the Instruqt proxy; variant with Local reporting off and an external elasticsearch + kibana 9.4.4 (Airlock reporting bundle 2.6.0) as lab containers."

  layout = resource.layout.main

  content {
    title = "Instructions"

    chapter "lab1" {
      title = "LAB 01"

      page "lab1" {
        reference = resource.page.lab1
      }
    }

    chapter "lab2" {
      title = "LAB 02"

      page "lab2" {
        reference = resource.page.lab2
      }
    }
  }

  settings {
    theme = "modern-dark"

    timelimit {
      duration   = "60m"
      show_timer = true
    }

    idle {
      enabled      = true
      timeout      = "30m"
      show_warning = true
    }

    controls {
      show_stop = true
    }
  }
}
