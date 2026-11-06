resource "page" "lab1" {
  title = "LAB 01: Get to Know the Environment and Setup virtinc.com"
  file  = "instructions/lab1.md"

  # The image sets the same password for admin and root (95_instruqt_lab.cfg).
  variables = {
    admin_password = resource.secret.gateway_root_password.value
    virtinc_host   = local.virtinc_host
    # Rendered as-is into a code block ({{{license}}}, unescaped), so the secret must hold
    # the license text with its line breaks. A base64 secret would need base64_decode(...).
    license = resource.secret.gateway_license.value
  }

  activities = {
    lab1 = resource.task.lab1
  }
}

resource "page" "lab2" {
  title = "LAB 02: Integrate and Configure WordPress"
  file  = "instructions/lab2.md"

  variables = {
    virtinc_host = local.virtinc_host
  }

  activities = {
    lab2 = resource.task.lab2
  }
}
