resource "network" "main" {
  subnet = "10.60.0.0/24"
}

# The appliance is the Gateway 8.6.1 GCP disk, prepared offline (27-10-2026):
# grown to 20 GB (the stock root fs is 98% full and Instruqt's own NoCloud seed
# turns growpart off), 2G swap file, kernel loglevel=3 and THP off (the serial
# console flood stalled the boot), and the ACE user-data (admin + root password)
# as /etc/cloud/cloud.cfg.d/95_instruqt_lab.cfg, because Instruqt's seed on
# /dev/vdd replaces any seed baked into the image. Since Instruqt's item 6 fix (27-10-2026) the
# agent sets the VM's address itself. No startup_script and no health_check here: the
# appliance is locked down, so readiness is probed from the tools container.
#
# External reporting variant (30-10-2026): image airlock-gw-8.6.1-lab-extrep2 = the cold lab
# image (NOT pre-warmed), edited offline so the internal elasticsearch/kibana never start
# (details in setup.hcl); setup.hcl then sets Remote reporting at lab start (reporting.hcl).
# Without the internal elasticsearch/kibana the Gateway uses ~2 GB, so 8 GB here.
# extrep2 = extrep + nftables template opening TCP 15780 for the Instruqt agent.
# extrep3 (03-11-2026) = extrep2 re-imported unchanged, to test whether the importer now keeps /var/tmp.
resource "vm" "gateway" {
  image {
    name = "airlock/airlock-gw-8.6.1-lab-extrep3:v1"
  }

  resources {
    cpu    = 4
    memory = 8192
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.10"
    aliases    = ["gateway"]
  }

  port {
    local = 8443
  }

  port {
    local = 22
  }

  # Virtual host ports for the Virtinc test tab (LAB 01).
  port {
    local = 80
  }

  port {
    local = 443
  }
}

# Helper with a shell and curl. No health check on purpose (debug run, 27-10-2026):
# the sandbox must open even when the Gateway does not answer yet, so the
# gateway terminal tab can be used to see why. Check readiness by hand:
# nc -z -w 3 <gateway ip> 8443 (Configuration Center; 443 only listens once a
# virtual host is configured).
resource "container" "tools" {
  image {
    name = "alpine:3.20"
  }

  entrypoint = ["/bin/sh", "-c"]
  # The Gateway configures eth0 by DHCP and its boot waits for a lease, but the
  # Instruqt network has no DHCP server: busybox udhcpd hands out exactly the
  # Gateway's address (no NET_ADMIN needed, unlike dnsmasq). DNS = the lab router only: it
  # resolves <resource>.container.sandbox.internal; a second, public server made those
  # names fail at random (resolv.conf "options rotate", run 30-10-2026). setup.hcl also
  # sets it in the Gateway configuration.
  command = [
    <<-EOT
    apk add --no-cache curl openssh-client busybox-extras jq sshpass >/dev/null 2>&1
    (i=0; until ip=$(getent hosts gateway | awk '{print $1; exit}'); [ -n "$ip" ] || [ $i -ge 150 ]; do i=$((i + 1)); sleep 2; done; [ -n "$ip" ] && echo "$ip $VIRTINC_HOST" >> /etc/hosts) &
    printf 'start 10.60.0.10\nend 10.60.0.10\ninterface eth0\nlease_file /tmp/udhcpd.leases\nopt router 10.60.0.1\nopt dns 10.60.0.1\nopt subnet 255.255.255.0\nopt lease 86400\n' > /tmp/udhcpd.conf
    touch /tmp/udhcpd.leases
    busybox-extras udhcpd -f /tmp/udhcpd.conf > /tmp/udhcpd.log 2>&1 &
    sleep infinity
  EOT
  ]

  network {
    id = resource.network.main.meta.id
  }

  # LAB 01: the Virtinc tab's public host, mapped to the Gateway in /etc/hosts (command above)
  # for the curl tests.
  environment = {
    VIRTINC_HOST = local.virtinc_host
  }

  # LAB 01 check and solve scripts (tasks.hcl), run in this container.
  volume {
    source      = "scripts"
    destination = "/opt/lab/scripts"
    read_only   = true
  }

  resources {
    cpu    = 500
    memory = 256
  }
}
