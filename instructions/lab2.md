# LAB 02: Integrate and Configure WordPress

## LAB Goals

This LAB covers the integration of a WordPress blog behind your Airlock Gateway and the configuration of rewrites.

After this LAB, you should have learned and successfully managed:

- To set a mapping to log only and enable the integration mode.
- To configure rewrites on the Gateway.

LAB 01 must be done: this LAB adds WordPress to the *virtinc* virtual host.

## 1. Setup the WordPress Back-end

1. In **Application Firewall >> Reverse Proxy**, add a *back-end group*. Name:

```text
wordpress
```

2. Add the WordPress back-end host. As all back-ends in this lab, it is reached by its lab-internal name:

- Protocol: *HTTP*
- Host name:

```text
wordpress.container.sandbox.internal
```

- Port:

```text
8081
```

## 2. Setup the Mapping and Configure it to Log Only

1. In **Application Firewall >> Reverse Proxy**, add a **New medium security mapping**. Name:

```text
wordpress
```

2. Entry path:

```text
/wordpress/
```

3. Back-end path:

```text
/
```

4. Set the mapping to *log only* and enable the *integration mode*. You keep these settings during the integration period.
5. Connect the mapping to the virtual host *virtinc* and to the back-end group *wordpress*.

**Check:** the Reverse Proxy overview shows the mapping *wordpress* connected to *virtinc* and *wordpress*.

## 3. Try to Access the WordPress Blog

1. Click **Activate** with a comment, for example:

```text
WordPress back-end group and log-only mapping
```

2. Open the **Virtinc (new browser tab)** tab, scroll down on the Virtinc main page and click **Go to Blog**.

When you look at the blog, it should become obvious that you need some configuration changes so that WordPress is rendered correctly.

## 4. "Fix" the Configuration to Render WordPress Correctly

The blog is not rendered as it should be. But why is this the case? Find out with the browser tools of the new browser tab:

1. Open the browser's developer tools (inspector, console, network). They show several issues.
2. Look at the page source as well.

Both show that back-end information is reflected in the page source: `wordpress.container.sandbox.internal:8081`. Your browser cannot reach that address, only your Gateway can.

3. Use rewrites to address every issue that causes an incorrect rendering of WordPress: on the mapping *wordpress*, tab **Response Actions**, section **Rewrite Response Body (any content)**. Address the issues one after the next, starting with the example above. Use the Gateway's placeholders such as `%BACKENDPROTOCOL%`, `%BACKENDHOST%` and `%ENTRYDIR%` instead of fixed host names, so the rules keep working when the back-end moves.

Aside from the inspector and the page source, the Gateway's logs (**Kibana** tab) show whether request headers are blocked.

Some tips:

- Start with the requests. See if headers are getting dropped.
- Then work with the response.
- Check for redirects.
- There seems to be some mixed content.
- Finally, have a look at tags that expose oEmbed endpoints for the blog page.

><details>
><summary><b>Need help? Sample rewrites</b></summary>
>
>Add all rules on the mapping *wordpress*, tab **Response Actions**, section **Rewrite Response Body (any content)**, not in the HTML-only section.
>
>Content-Type pattern for all rules:
>
>```text
>text/html.*
>```
>
>**Rule 1: plain URLs.** Content pattern:
>
>```text
>%BACKENDPROTOCOL%://%BACKENDHOST%
>```
>
>Replace with:
>
>```text
>%ENTRYDIR%
>```
>
>**Rule 2: JSON-escaped URLs.** Content pattern:
>
>```text
>%BACKENDPROTOCOL%:\\/\\/%BACKENDHOST%
>```
>
>Replace with:
>
>```text
>\\/%ENTRYPATH%
>```
>
>**Rule 3: URL-encoded URLs.** Content pattern:
>
>```text
>%BACKENDPROTOCOL%%3A%2F%2F%BACKENDHOSTNAME%%3A%BACKENDPORT%
>```
>
>Replace with:
>
>```text
>%2F%ENTRYPATH%
>```
>
>**Rule 4: fixed host in the WordPress content.** Content pattern:
>
>```text
>https://wordpress.teacher.virtinc.com
>```
>
>Replace with:
>
>```text
>%ENTRYDIR%
>```
>
>The second rule catches JSON-escaped URLs (`http:\/\/...`) in inline scripts, the third URL-encoded ones. The last one is a leftover of the WordPress content: some theme images point to a fixed host `wordpress.teacher.virtinc.com` that does not exist in this lab (the mixed content).
></details>

## 5. Activate and Test Your Configuration!

1. Click **Activate** with a comment, for example:

```text
WordPress rewrites
```

2. Reload the blog in the **Virtinc (new browser tab)** tab. It is rendered correctly.
3. In the **Tools Shell**, count the back-end addresses left in the page:

```bash
curl -sk https://{{virtinc_host}}/wordpress/ | grep -o 'wordpress.container.sandbox.internal' | wc -l
```

**Check:** the output is `0`.

><details>
><summary><b>Not working?</b></summary>
>
>Check that you activated your last changes, that the mapping is connected to both the virtual host *virtinc* and the back-end group *wordpress*, and that the rewrites are enabled. The page source shows which addresses are still left.
></details>

## Verify

Click **Check** to verify your configuration.

<instruqt-task id="lab2"></instruqt-task>
