# LAB 01: Get to Know the Lab Environment and Setup virtinc.com

## LAB Goal

After this LAB, you should have:

- A general understanding of the lab environment for the upcoming LABs.
- Connected to Airlock Gateway, inserted a license, and activated your configuration.
- Created a virtual host, back-end group and mapping for virtinc.com.

## Your Lab Environment

You are both Airlock *administrator* and *tester*. The tabs on the right give you everything you need:

| Tab | Purpose |
|-----|---------|
| **Config Center** | The Configuration Center of the Airlock Gateway that you configure. |
| **Virtinc (via Gateway)** | The Virtinc main page through your Gateway, for testing. |
| **Kibana** | The Gateway's reporting (not used in LAB 01). |
| **Tools Shell** | A shell in the lab network with `curl`, for testing. |
| **Gateway Shell** | A terminal on your Airlock Gateway. |

Your Virtinc page is reached under this host name:

```text
{{virtinc_host}}
```

## 1. Open the Gateway Configuration Center

1. Open the **Config Center** tab.
2. Log in with the user:

```text
admin
```

3. And the password:

```text
{{{admin_password}}}
```

> The Gateway needs about two minutes after the lab start before the Configuration Center answers. If the tab stays blank, reload it.

**Check:** you see the Configuration Center dashboard.

## 2. Insert a Gateway License

1. Copy your license:

```text
{{{license}}}
```

2. Go to **System Setup >> License**.
3. Paste the license into the license field.

You activate all changes of this LAB together at the end (step 5).

## 3. Setup a Network Time Protocol (NTP) Server

1. Go to **System Setup >> Network Services**.
2. Set the NTP server to:

```text
169.254.169.123
```

**Check:** the NTP server is listed under **System Setup >> Network Services**.

## 4. Setup the Virtinc Main Page

### Virtual host

1. Go to **Application Firewall >> Reverse Proxy** and add a *virtual host*.
2. Name:

```text
virtinc
```

3. Host name:

```text
{{virtinc_host}}
```

4. Untick **Strictly match FQDN and aliases**. The Virtinc tab reaches your Gateway through the Instruqt proxy, which asks for a different host name than the one above.
5. The network interface and IP address are already set: your Gateway has only one interface.
6. Allow *http* and *https* and enable the *redirect to https*.
7. On the SSL tab, select the server certificate *test.certificate*.

### Back-end group

1. In **Application Firewall >> Reverse Proxy**, add a *back-end group*. Name:

```text
virtinc
```

2. Add the Virtinc back-end host. Every back-end in this lab is reached by its lab-internal name `<name>.container.sandbox.internal`:

- Protocol: *HTTP*
- Host name:

```text
virtinc.container.sandbox.internal
```

- Port:

```text
8080
```

### Mapping

1. In **Application Firewall >> Reverse Proxy**, add a **New medium security mapping**. Name:

```text
virtinc
```

2. Connect the mapping to the virtual host *virtinc* and to the back-end group *virtinc*.

**Check:** the Reverse Proxy overview shows the virtual host, the mapping and the back-end group connected.

## 5. Activate and Test Your Configuration!

1. Click **Activate**. Activation pushes all your changes of this LAB (license, NTP server, Virtinc) to the Gateway. Always enter an expressive comment to track your changes, for example:

```text
LAB 01: license, NTP, Virtinc
```

2. Go to **System Setup >> License**: the page shows the license details and an expiry date.

3. Open the **Virtinc (via Gateway)** tab and reload it. You see the Virtinc main page.
4. In the **Tools Shell**, check that HTTP is redirected to HTTPS:

```bash
curl -sI http://{{virtinc_host}}/
```

The response is a redirect with a `Location: https://...` header.

5. Fetch the page over HTTPS (`-k`, because the test certificate is self-signed):

```bash
curl -sk https://{{virtinc_host}}/ | grep -i '<title>'
```

**Check:** the output contains the Virtinc page title.

><details>
><summary><b>Not working?</b></summary>
>
>Check that you activated your last changes, that *Strictly match FQDN and aliases* is unticked, that the mapping is connected to both the virtual host and the back-end group, and that the license is active.
></details>

## Verify

Click **Check** to verify your configuration.

<instruqt-task id="lab1"></instruqt-task>
