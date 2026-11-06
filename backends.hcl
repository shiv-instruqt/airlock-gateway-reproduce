# Training backends for the Gateway lab guides (Gateway Connaisseur, later SAH), as in
# production ACE (ECS task, airlock-academy-manager role setup-ecs-and-container):
# virtinc, wordpress, mysql, openldap. Public base images; the files come from
# backends/<name>/ in this lab directory (only the lab directory ships), mounted
# read-only. Sources: github.com/ergon/airlock-academy-back-ends, branch instruqt-poc.
#
# Names (convention, 30-10-2026): every container is addressed by its Instruqt internal
# name <resource>.container.sandbox.internal (e.g. wordpress.container.sandbox.internal),
# in Gateway back-end groups, guides, checks, solves and container environments. The lab
# router 10.60.0.1 resolves these names for containers and, once setup.hcl has set the
# Gateway's DNS server to 10.60.0.1 only, for the Gateway (verified live 30-10-2026).
# Follow the same rule for every new back-end. Do NOT use the *.airlock.academy aliases:
# that is a public wildcard record, so the Gateway resolves it to a public host. They stay
# only so old commands in a shell still work. Fixed IPs stay only where a component needs
# an address: the checks also accept them, and setup.hcl uses elasticsearch's as fallback.

# Ports: production published virtinc on host port 8080 and WordPress on 8081 (the
# WordPress siteurl was http://wordpress.airlock.academy:8081, here WP_URL), so the start scripts
# listen on 80 plus 8080 / 8081 and the guides' backend ports keep working.
#
# Local mirror for testing: airlock-academy-back-ends/instruqt-poc/compose.yml.

# MySQL root password, also used by WordPress. Team secret (Settings > Secrets),
# not committed. Production uses the ansible vault value other_password.
resource "secret" "backends_mysql_root" {
  reference = "ACADEMY_BACKENDS_MYSQL_ROOT_PASSWORD"
}

# WordPress admin login used in the lab guides (same value as production ACE).
variable "wp_admin_password" {
  default     = "expert"
  description = "WordPress admin password used in the lab guides (lab value, not a secret)"
}

resource "container" "virtinc" {
  image {
    name = "httpd:2.4-alpine"
  }

  entrypoint = ["/bin/sh", "-c"]
  command    = ["sh /opt/backend/start.sh"]

  volume {
    source      = "backends/virtinc"
    destination = "/opt/backend"
    read_only   = true
  }

  volume {
    source      = "backends/virtinc/htdocs"
    destination = "/usr/local/apache2/htdocs"
    read_only   = true
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.21"
    aliases    = ["virtinc.airlock.academy", "virtinc"]
  }

  resources {
    cpu    = 100
    memory = 128
  }

  health_check {
    timeout = "60s"
    exec {
      command = ["/bin/sh", "-c", "wget -q -O /dev/null http://127.0.0.1:8080/"]
    }
  }

  port {
    local = 80
  }

  port {
    local = 8080
  }
}

resource "container" "mysql" {
  image {
    name = "mysql:5.6"
  }

  command = ["--default-authentication-plugin=mysql_native_password"]

  environment = {
    MYSQL_ROOT_PASSWORD = resource.secret.backends_mysql_root.value
    MYSQL_DATABASE      = "wordpress"
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.23"
    aliases    = ["mysql.airlock.academy", "mysql"]
  }

  resources {
    cpu    = 500
    memory = 512
  }

  health_check {
    timeout = "180s"
    exec {
      command = ["/bin/sh", "-c", "mysqladmin ping -h 127.0.0.1 -uroot -p\"$MYSQL_ROOT_PASSWORD\" --silent"]
    }
  }

  port {
    local = 3306
  }
}

# wordpress:php7.4-apache = PHP 7.4.33 on Debian 11; the WordPress 5.3.2 tree is copied
# in at start. entry.sh installs sudo/less/mysql client (archive.debian.org) and a pinned,
# checksummed WP-CLI 2.12.0, then runs the unchanged wp-start.sh (which waits 60 s for
# the DB). Ready after about 70 s.
resource "container" "wordpress" {
  image {
    name = "wordpress:php7.4-apache"
  }

  entrypoint = ["/bin/sh", "-c"]
  command    = ["sh /opt/backend/entry.sh"]

  environment = {
    WORDPRESS_DB_HOST     = "mysql.container.sandbox.internal"
    WORDPRESS_DB_USER     = "root"
    WORDPRESS_DB_PASSWORD = resource.secret.backends_mysql_root.value
    WORDPRESS_DB_NAME     = "wordpress"
    # Site address: WordPress writes it into every absolute URL, which the Gateway rewrites
    # in LAB 02 (%BACKENDHOST% = this host:port, so it must equal the back-end host).
    WP_URL                = "wordpress.container.sandbox.internal:8081"
    WP_TITLE              = "Virtinc"
    WP_ADMIN_USER         = "admin"
    WP_ADMIN_PASSWORD     = variable.wp_admin_password
    WP_ADMIN_EMAIL        = "admin@virtinc.local"
  }

  volume {
    source      = "backends/wordpress"
    destination = "/opt/backend"
    read_only   = true
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.22"
    aliases    = ["wordpress.airlock.academy", "wordpress"]
  }

  resources {
    cpu    = 500
    memory = 512
  }

  health_check {
    timeout = "420s"
    exec {
      command = ["/bin/sh", "-c", "curl -fsS -o /dev/null http://127.0.0.1:8081/wp-login.php"]
    }
  }

  port {
    local = 80
  }

  port {
    local = 8081
  }
}

# alpine 3.24 + OpenLDAP 2.6 (apk at start). Same slapd.conf semantics as production
# (suffix dc=ergon,dc=ch, memberof + ppolicy, airlock-iam schema, same LDIF).
# Anonymous read is allowed (no ACLs, as in production), so the check needs no password.
resource "container" "openldap" {
  image {
    name = "alpine:3.24"
  }

  entrypoint = ["/bin/sh", "-c"]
  command    = ["sh /opt/backend/start.sh"]

  volume {
    source      = "backends/openldap"
    destination = "/opt/backend"
    read_only   = true
  }

  network {
    id         = resource.network.main.meta.id
    ip_address = "10.60.0.24"
    aliases    = ["ldap.airlock.academy", "ldap"]
  }

  resources {
    cpu    = 200
    memory = 128
  }

  health_check {
    timeout = "120s"
    exec {
      command = ["/bin/sh", "-c", "ldapsearch -x -LLL -H ldap://127.0.0.1 -b ou=groups,dc=ergon,dc=ch '(member=cn=adm_hansm,ou=users,dc=ergon,dc=ch)' dn | grep -q airlock-administrator"]
    }
  }

  port {
    local = 389
  }
}

# DNS for the Gateway VM (resolved 30-10-2026): the Gateway got "nameserver 10.60.0.1,
# nameserver 1.1.1.1, options rotate" from udhcpd, so internal names failed at random
# (1.1.1.1 answers NXDOMAIN). Now udhcpd hands out 10.60.0.1 only (sandbox.hcl) and
# setup/gateway-rest.sh sets the Gateway's DNS server to 10.60.0.1 in its configuration
# (Network Services, REST network-services dns.ipAddresses), which owns resolv.conf.
