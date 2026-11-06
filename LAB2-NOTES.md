# LAB 02 on Instruqt 2.0 (extrep lab): notes

Source: LAB 03 "Integrate and Configure WordPress" in `Gateway Connaisseur - labs.pdf` (Training Materials, 01 Gateway - Connaisseur), rewrites from `Solutions/wordpress_rewrites.txt`. It is the second challenge here (chapter "LAB 02"); the guide's LAB 02 (traffic analysis) is not ported. Added 30-10-2026; `instruqt lab validate` passes, nothing has run on Instruqt yet. Back-end names and Gateway DNS: see LAB1-NOTES.md.

## Guide-to-lab mapping

| Guide section | In the lab | Change |
|---------------|-----------|--------|
| LAB Goals | Log only + integration mode, rewrites | Error pages and favicon dropped |
| Configure Custom Error Pages, Activate and Test | Dropped | The error pages come from the Academy File Browser, which this lab does not have |
| Setup the WordPress back-end | Back-end group `wordpress`: HTTP `wordpress.container.sandbox.internal:8081` | Name instead of `wordpress.airlock.academy:8081`; group name new |
| Setup the Mapping, Log Only | Medium security mapping `wordpress`, entry `/wordpress/`, back-end path `/`, log only, integration mode, on vhost `virtinc` | Mapping name new; the Virtinc vhost of LAB 01 is reused, as in the guide (blog linked from the Virtinc main page) |
| Try to Access the WordPress Blog | Activate, "Virtinc (new browser tab)" > Go to Blog | Single activation per part, as in LAB 01 |
| Fix the Configuration | Same steps and tips; sample rewrites in a collapsed hint | "Logs in Airlock Gateway" -> Kibana tab |
| Fix the favicon.ico | Dropped | Needs the error pages |

## Rewrites

The sample's header ("Content Pattern / Replace with") is the *Rewrite Response Body (any content)* section, so the guide and the solve use it, Content-Type pattern `text/html.*` (WordPress sends `text/html; charset=UTF-8`; the pattern matches both if the Gateway uses find or full match). Four rules, copied verbatim: plain, JSON-escaped (`\\/`), URL-encoded, and `https://wordpress.teacher.virtinc.com` -> `%ENTRYDIR%`.

**Logo/theme images:** the dump hard-codes about a dozen theme image and logo URLs to `https://wordpress.teacher.virtinc.com` (Colibri JSON). The sample's fourth rule already rewrites them, so they are handled the course way (the guide's "mixed content" tip); the hint names it as a content leftover. The page check does not require it.

## Check design

As LAB 01: tools container, SSH to the Gateway, ACTIVE configuration. Mappings counted are those linked to a Virtinc vhost (`VIRTINC_HOST`) and a WordPress back-end group.

| Condition | Check | How |
|-----------|-------|-----|
| `backend_group` | check-backend-group | an HTTP back-end host `wordpress.container.sandbox.internal` (lab IP also accepted), port 8081 |
| `mapping` | check-mapping | a linked mapping with entry path `/wordpress/` (slash and regex anchors optional), back-end path `/` |
| | check-mapping-mode | the same mapping: `threatHandling == "NOTIFY"` and `operationalMode == "INTEGRATION"` |
| `rewrites` | check-rewrites | the same kind of mapping has an enabled body or HTML rewrite whose pattern mentions `BACKENDHOST` or `wordpress` |
| | check-wordpress-page | `curl -skL --resolve` `https://<virtinc host>/wordpress/`: 200 on the Gateway host (a redirect to the back-end fails with its own message), body contains `wp-content`, and no `wordpress.container.sandbox.internal` (covers plain, `\/`-escaped and `%3A%2F%2F`-encoded URLs) |

**Skip:** `scripts/lab2/solve.sh`, same background-job pattern as LAB 01 with its own `solve-lab2.lock` and `solved-lab2` marker. The job runs the license and the idempotent LAB 01 solve first (it needs the vhost), then `rest-solve-lab2.sh`: back-end group, mapping from the "Medium Security Mapping" template, relationships, one PATCH with name, entry/back-end path, `threatHandling`, `operationalMode` and the four rewrites appended to `application.response.body.rewrites` (the whole `application` object is sent back so other settings stay), one activation.

## Verified vs unverified

- REST attribute names from the 8.6 REST reference (NotebookLM `f4aa7e31-0cd3-409b-9c8b-1d282733684c`, source "REST API" 30-10-2026): `application.response.body.rewrites[]` with `enabled`, `contentType`, `contentPattern.pattern`, `contentPattern.caseIgnored`, `replaceWith`; HTML-only is `application.response.html.rewrites[]` with `urlPattern`; `entryPath.value`, `backendPath`, `operationalMode` (`PRODUCTION`/`INTEGRATION`).
- UNVERIFIED: "log only" = `threatHandling: "NOTIFY"` (NotebookLM answer, not a quoted field list); the GUI labels for log only / integration mode; whether a mapping PATCH replaces or merges nested objects (hence the full `application` object); whether `GET mappings` returns `application` in the list (checks rely on it).
- UNVERIFIED: `%BACKENDPROTOCOL%`, `%BACKENDHOSTNAME%`, `%BACKENDPORT%` are not in the public 8.6 docs (only `%BACKENDHOST%`, `%BACKENDDIR%`, `%ENTRYDIR%`, `%ENTRYPATH%`); they come from the course sample. If the Gateway rejects or ignores them, the plain rule alone still clears most URLs.
- UNVERIFIED: `%BACKENDHOST%` includes the port (`host:8081`), which the sample assumes; it must match `WP_URL`, so the back-end host must be exactly `wordpress.container.sandbox.internal`, port 8081.
- The WordPress `wp search-replace` to `WP_URL` (entry.sh) has not run yet.
