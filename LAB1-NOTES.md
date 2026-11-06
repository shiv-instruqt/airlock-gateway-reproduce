# LAB 01 on Instruqt 2.0 (extrep lab): notes

Source: LAB 01 "Get to Know the airlock.academy Environment and Setup virtinc.com" in `Gateway Connaisseur - labs.docx` (Training Materials, 01 Gateway - Connaisseur). Exercise steps and goals are kept; only environment-specific parts are migrated. Ported 30-10-2026 from the uncommitted `labs/poc/gateway-vm` draft into this lab, which boots on Instruqt (cold image, Config Center after about 2 min). `instruqt lab validate` passes; the checks and the solve have not run on Instruqt yet. Revised the same day: no student numbers, the virtual host uses the Instruqt URL of the Virtinc tab, and the license is a participant step again (pasted from the page), as in the guide.

## Hostname design

Instruqt has no student numbers and no public DNS for `test.student[X].virtinc.com`. As in the Microgateway labs, the participant reaches the app through a service tab, whose public URL is built from Instruqt system variables:

- MGW evidence: `academy-instruqt-2.0-poc`, branch `poc-feature/ACADEMYADM-842-teaser`, `labs/mgw-teaser/variables.hcl` defines `local "url_juice_shop" = "https://service-juice-shop-${variable.instruqt_session_id}.${variable.instruqt_sandbox_domain}"` (also headlamp, grafana), `instructions.hcl` passes them as page variables, the pages link `{{url_juice_shop}}`; `sandbox.hcl` hands `variable.instruqt_session_id` into the VM as `_SANDBOX_ID`. The MGW README marks the URL pattern as "to be verified on the first run".
- Instruqt docs (docs.labs.instruqt.com, Variable > System Variables): `instruqt_session_id` and `instruqt_sandbox_domain` are injected at launch; their example builds `https://service-workshop-${variable.instruqt_session_id}.${variable.instruqt_sandbox_domain}` for a service named `workshop`.
- This lab: `tabs.hcl` `local "virtinc_host" = "service-virtinc-<session>.<domain>"` for `resource "service" "virtinc"`. The same value goes to the instructions page (`{{virtinc_host}}`), the task environment (`VIRTINC_HOST`, used by checks and solve) and the tools container (`/etc/hosts` maps it to the Gateway, so `curl` from Tools Shell hits the Gateway; checks use `curl --resolve`).
- Airlock 8.6 (public docs, virtual host "Host name (FQDN)" and "Strictly match FQDN and aliases"): the virtual host gets this name as its host name. Explicit host names/aliases are the documented way; wildcards are documented only for certificates and DNS records, so no wildcard alias. If the proxy forwards another Host header, the vhost still answers as the catch-all vhost as long as "Strictly match FQDN" stays off (the default in the REST example).
- Still open: whether the session ID in the URL equals `instruqt_session_id` (unverified for MGW too), and which Host header the proxy forwards.

## Files (LAB 01 only; reporting files untouched)

| File | What |
|------|------|
| `tasks.hcl` | `task "lab1"`: 5 conditions, 7 checks, one shared solve |
| `instructions.hcl`, `instructions/lab1.md` | LAB 01 page; admin password rendered from `ACADEMY_GATEWAY_ROOT_PASSWORD` (the image sets the same password for admin and root) |
| `scripts/lib/gw.sh` | Tools-side helpers: SSH to the Gateway, run a REST script there, jq filters, lab constants (NTP address, back-end names and lab IPs) |
| `scripts/gateway/rest-lib.sh` | Gateway-side: JWT + REST helpers, same auth as `setup/gateway-rest.sh` |
| `scripts/gateway/rest-query.sh` | Gateway-side: loads the ACTIVE config, prints the requested resources as one JSON object |
| `scripts/gateway/rest-solve.sh` | Gateway-side: LAB 01 solution, idempotent, one activation |
| `scripts/gateway/rest-license.sh` | Gateway-side license for the skip path (only if none is active) |
| `setup.hcl`, `setup/gateway-rest.sh` | Comments only: no license at lab start (participant step) |
| `scripts/lab1/checks.sh`, `check-*.sh` | One function per check, thin wrappers per check block |
| `scripts/lab1/solve.sh`, `solve-job.sh` | Skip: detached solve job, checks wait for it |
| `sandbox.hcl` | tools: + `jq sshpass`, env `VIRTINC_HOST`, `/etc/hosts` entry `VIRTINC_HOST` -> Gateway, scripts volume at `/opt/lab/scripts`; gateway VM: + ports 80/443. Image line untouched. |
| `tabs.hcl`, `layouts.hcl` | + "Virtinc (via Gateway)" service tab (VM port 443), `local.virtinc_host` |
| `main.hcl` | Chapter LAB 01 replaces the PoC page |
| `instructions/01-explore.md` | Deleted (PoC page with the admin/root password in clear text; still in git history) |
| `.gitattributes` | `*.sh text eol=lf` |

## Guide-to-lab mapping

| Guide section | In the lab | Change |
|---------------|-----------|--------|
| LAB Goal | Same three bullets | none |
| Your Software Setup | Dropped | The tabs are the setup |
| Connect to 'airlock.academy' | "Your Lab Environment" tab table + the Virtinc tab's address | Portal tiles -> tabs; Academy Files and certification link not applicable |
| Open the Gateway Configuration Center (admin / expert) | Same, password from the secret | Image password is not `expert` |
| Insert a Gateway License | Copy the license from the page (code block, from the secret), paste under System Setup >> License, activate | Source: page instead of the Academy File Browser |
| Setup NTP server | Unchanged, same value | See "Guide vs public docs" |
| Setup Virtinc Main Page | Unchanged except host names | Virtual host = the Virtinc tab's host (`{{virtinc_host}}`) instead of the per-student virtinc.com name; back-end = `virtinc.container.sandbox.internal:8080` instead of `virtinc.airlock.academy:8080`; vhost interface `EXT0` and the Gateway's IP/24 given explicitly (the guide gives none) |
| Activate and Test | Activate unchanged; test via the Virtinc tab and `curl` in Tools Shell | none |

## Instructions page format (30-10-2026)

Per Nicolas: every value the participant types or pastes is in its own fenced code block (Instruqt copy button): admin login, license, NTP server, host name, interface, IP, back-end URL, names, activation comments, shell commands. Numbered steps with a bold menu path, one action per step, a short **Check** line per part, a collapsible hint (`><details>`) as in the MGW teaser pages, no emoji, plain ASCII. The page carries no references to the original guide; these differences live only in this file:

- The guide's per-student host `test.student[X].virtinc.com` is replaced by the Virtinc tab's host; there are no student numbers.
- The guide's back-end `virtinc.airlock.academy:8080` is replaced by `virtinc.container.sandbox.internal:8080` (the Gateway resolves the academy name to a public host).
- New on the page (the guide leaves them open): the names `virtinc` for virtual host, back-end group and mapping, the activation comments, and the vhost interface/IP. The solve uses the same names.
- The guide's "Open a second browser in private mode" test became the Virtinc tab plus two `curl` commands in Tools Shell.

## Check design

Checks run in the tools container. Each one SSHes to the Gateway as root (password from `ACADEMY_GATEWAY_ROOT_PASSWORD`, task env `GW_ROOT_PASSWORD`) and pipes `rest-lib.sh` + `rest-query.sh` to `bash -s`. On the Gateway: JWT of `airlock-configuration-cli` from `airlock-user-manager-tool -J -u airlock-configuration-cli -j` (recursive `token` lookup), REST on `http://localhost:8080/airlock/rest`, `session/create`, `configuration/configurations/load-active`, GETs, `session/terminate`. Because the ACTIVE configuration is read, a step only passes after Activate. REST call log: `/var/log/lab-tasks.log` in the tools container.

| Condition | Check | How |
|-----------|-------|-----|
| `license` | check-license | `license`: `data.attributes.expiry` non-empty |
| `ntp` | check-ntp | `network-services`: `ntp.servers[]` contains the guide's NTP address |
| `virtual_host` | check-vhost-exists | `virtual-hosts`: `hostName` or `aliasNames[]` equals `VIRTINC_HOST` (case-insensitive) |
| | check-vhost-protocols | that vhost: `networkInterface.http.enabled`, `.https.enabled`, `.http.httpsRedirectEnforced` true |
| `backend_group` | check-backend-group | `back-end-groups`: an HTTP back-end host `virtinc.container.sandbox.internal` (the lab IP also accepted), port 8080 (80 also accepted) |
| `mapping` | check-mapping | `mappings`: one whose `virtual-hosts` and `back-end-groups` relationships include those ids |
| `test` | check-http-redirect | `curl --resolve $VIRTINC_HOST:80:<gateway ip> http://$VIRTINC_HOST/` -> 30x to `https://` |
| | check-virtinc-page | `curl -kL --resolve ... https://<host>/` contains `<title>Virtinc` |

All conditions, including `test`, are expected to pass once the license from lab start is active and LAB 01 is configured and activated.

**Not checked (manual):** Config Center login, looking at the license page, the medium security template (not visible in the config), the `test.certificate` choice, the activation comment, the browser test.

**Skip:** every condition's solve runs `solve.sh`, which starts `solve-job.sh` detached (a solve over ~30 s gets a 504) and waits up to 20 s; checks wait up to 20 s for a running job. The job first runs `rest-license.sh` (only if no license is active), then `rest-solve.sh` on the Gateway: NTP, vhost `virtinc` (host name `VIRTINC_HOST`, EXT0, Gateway IP/24, HTTP 80 + HTTPS 443 + redirect, the certificate with `CN=test.certificate`), back-end group `virtinc` (`virtinc.container.sandbox.internal:8080`), mapping `virtinc` from the "Medium Security Mapping" template (entry path `/`), relationships, one activation. Log: `/var/log/lab-solve.log`.

**License:** a participant step as in the guide. `instructions.hcl` renders the team secret `ACADEMY_GATEWAY_LICENSE` unchanged into a code block (`{{{license}}}`, the unescaped form the IAM ssi-sca lab used for its license). The `license` condition checks that the activated configuration has a license expiry; the skip applies the license over REST only if none is active (`rest-license.sh`).

**License at lab start (dropped 30-10-2026):** the first run did not activate it. The cause is not visible from the code; one candidate is the secret format (if the secret is base64 or lost its line breaks, the Gateway rejects the PATCH and `gateway-rest.sh` stops before the activation). `/tmp/gateway-setup.log` in the `gateway_setup` container of that run would show it.

## Secrets

`ACADEMY_GATEWAY_ROOT_PASSWORD` and `ACADEMY_GATEWAY_LICENSE` (both exist). The license is shown on the page and is in the task environment for the skip path.

## Open questions

1. **License secret format:** the page shows the secret as-is, so it must hold the license text with its line breaks (BEGIN/END lines). Instruqt 2.0 has no try/can in HCL, so the page cannot detect base64. If the secret was stored as base64, or the secrets UI flattened the line breaks, store it as base64 and change the page variable to `base64_decode(resource.secret.gateway_license.value)` (the IAM ssi-sca pattern, `IAM_LICENSE_B64`); the scripts accept both. The license runs until 31-12-2026.
2. **Virtual host IP:** the guide gives none, REST requires one. Interface `INTERNAL` (the single-NIC lab VM has no `EXT0`; eth0 is INTERNAL and mgmt, first run 30-10-2026), the Gateway's own address /24.
3. **Virtinc tab and host name:** the `service-virtinc-<session>.<domain>` pattern and the Host header the proxy forwards are unverified (see Hostname design).
4. **Solve REST bodies** (vhost create, network-services PATCH, relationships) come from the public 8.6 REST reference and are untested.
5. **Concurrent sessions:** checks open short REST sessions as `airlock-configuration-cli` while the participant works in the Config Center; a skip activates and leaves the participant's Config Center session outdated.
6. **Git history:** the deleted `01-explore.md` holds the admin/root password in clear text.

## Guide vs current public docs (Gateway 8.6)

Checked against docs.airlock.com 8.6 (NotebookLM `airlock-gateway-docs-8.6-1/-2`):

- *System Setup >> License*, *System Setup >> Network Services* (Section NTP), *Application Firewall >> Reverse Proxy*, "New medium security mapping", the Activate dialog with Comment: still as in the guide.
- **NTP address:** the guide's value is the AWS Time Sync link-local address (ACE runs on EC2); outside AWS it is no NTP server. The setting is accepted, but the Gateway will not sync time. Kept as a config exercise.
- **`test.student[X].virtinc.com`:** per-student public DNS of ACE; replaced by the Virtinc tab's Instruqt host.
- **Back-end `virtinc.airlock.academy`:** a public wildcard record; the Gateway resolves it to a public host, not to the lab container. Replaced by `virtinc.container.sandbox.internal` in this lab.
- **Password `expert`:** environment-specific, replaced by the secret.
- **"redirect to https":** stored as `networkInterface.http.httpsRedirectEnforced` (REST reference); the GUI label was not confirmed in the public docs, so the guide's wording is kept.
- **`test.certificate`:** the docs say "a self-signed test-certificate"; the name appears as `CN=test.certificate` in the REST example.
- **Virtual host IP:** not in the guide, required in 8.6.
- Minor: the guide mixes `[x]` and `[X]`.
- **Certificate (30-10-2026, Nicolas):** `test.certificate` ships with every Gateway; no real certificate is needed, because both Gateway tabs go through the Instruqt proxy (browser sees Instruqt's certificate; the proxy does not verify the Gateway's, see the 8443 Config Center tab). The earlier "A certificate must be chosen" was most likely the solve's CN-from-PEM lookup finding nothing; `rest-solve.sh` now matches the name first and logs the certificate list on a miss.

## Service host names resolved (30-10-2026)
The documented `service-<name>-<session>.<domain>` pattern 404s. The real host (read from the tab iframes, verified in the browser) is `service-<hash>-<session>.<domain>` with hash = first 6 hex chars of `md5("resource.service.<name>")`: virtinc `b03acb`, config-center `e0b9cd`, kibana `ef8e94`. `tabs.hcl` now uses these, so the new-window tabs and `virtinc_host` (guide, VIRTINC_HOST, tools /etc/hosts) follow. Renaming a service changes its hash. The MGW teaser's `url_*` locals use the unhashed pattern too.

## Back-end names and Gateway DNS (30-10-2026)
Convention (Nicolas): every back-end and every inter-container reference uses the Instruqt internal name `<resource>.container.sandbox.internal`, never `*.airlock.academy` (public wildcard) and an IP only where a component needs an address (said in a comment there). Applies to future back-ends too; the rule is also at the top of `backends.hcl`.

- Verified in the live run: the lab router 10.60.0.1 resolves `<resource>.container.sandbox.internal` (and `<resource>.main.sandbox.internal`) to each container; containers already use it. The Gateway had `nameserver 10.60.0.1`, `nameserver 1.1.1.1`, `options rotate`, so the names failed at random.
- Fix, two layers: udhcpd in the tools container hands out only `opt dns 10.60.0.1`; `setup/gateway-rest.sh` PATCHes `network-services` `dns.ipAddresses = ["10.60.0.1"]` (8.6 REST reference via NotebookLM `f4aa7e31...`: `data.attributes.dns.ipAddresses[]`, "up to three DNS server IP addresses", GUI System Setup >> Network Services, Section DNS) in the same activation as reporting. UNVERIFIED: whether `dhcpInterface: ""` is accepted to stop DHCP-provided DNS (the script retries without it), and that the Gateway rewrites resolv.conf on activation. The setup log prints resolv.conf and three lookups after the activation; DNS failure does not fail the setup (`RESULT ok ... dns=failed`).
- Changed: LAB 01 back-end host (guide, solve, check; the check also accepts the IP), `WORDPRESS_DB_HOST`, `WP_URL`, Remote reporting URL (IP fallback kept), kibana/init_ek elasticsearch URLs. The `*.airlock.academy` network aliases stay but are unused.
- `WP_URL` alone did not move WordPress: `wp db import` brings the dump's `wordpress.airlock.academy:8081` back. `backends/wordpress/entry.sh` adds one `wp search-replace 'wordpress.airlock.academy:8081' "$WP_URL"` right after wp-start.sh's own (wp-start.sh unchanged).
