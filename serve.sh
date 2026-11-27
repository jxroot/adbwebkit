#!/data/data/com.termux/files/usr/bin/bash
# Start the console. adbwebkit over the built-in PHP server, plus the link gate.
#
#   ./serve.sh                 # local only, port 8080
#   ./serve.sh 9090            # local only, port 9090
#   TUNNEL=1 ./serve.sh        # also open a public cloudflared URL (what a
#                              # link needs: the target is NOT on your LAN)
#
# A quick tunnel (no Cloudflare account) carries NO uptime guarantee: Cloudflare
# drops the name at any time ("Tunnel not found"), and once dropped the hostname
# stops resolving and never comes back. serve.sh therefore SUPERVISES both the
# tunnel and the server: if the public URL stops answering, it tears down, opens
# a fresh tunnel, and restarts the server on the new URL. The console thus stays
# reachable; the one thing the supervisor cannot fix is an agent already baked
# with an old URL -- re-arm the link after a URL change (see LINK.md).
set -uo pipefail
cd "$(dirname "$0")"

PORT="${1:-${ADBWEBKIT_PORT:-8080}}"
export ADBWEBKIT_PORT="$PORT"
export ADBWEBKIT_ALLOW_PRIVATE="${ADBWEBKIT_ALLOW_PRIVATE:-}"
CHECK_INTERVAL="${ADBWEBKIT_HEALTH_INTERVAL:-20}"   # seconds between URL health probes
FAIL_THRESHOLD="${ADBWEBKIT_HEALTH_FAILS:-3}"       # consecutive fails before a restart

command -v php >/dev/null || { echo "php missing: pkg install php"; exit 1; }
command -v adb >/dev/null || { echo "adb missing: pkg install android-tools"; exit 1; }

mkdir -p linkdata linkdata/s linkdata/apk
adb start-server >/dev/null 2>&1 || true
# A console that already owns a key makes every wireless reconnect silent.
php -r 'require "link/adbctrl.php"; adb_ensure_keys();' >/dev/null 2>&1 || true

TUNNEL_PID=""
SERVER_PID=""
URL=""

cleanup() {
  [ -n "$SERVER_PID" ] && kill "$SERVER_PID" 2>/dev/null || true
  [ -n "$TUNNEL_PID" ] && kill "$TUNNEL_PID" 2>/dev/null || true
  pkill -P $$ 2>/dev/null || true
}
trap cleanup EXIT INT TERM

# Open a fresh quick tunnel and echo the public URL (empty on failure).
open_tunnel() {
  command -v cloudflared >/dev/null || { echo "cloudflared missing: pkg install cloudflared"; return 1; }
  local log; log="$(mktemp)"
  cloudflared tunnel --url "http://127.0.0.1:$PORT" --no-autoupdate >"$log" 2>&1 &
  TUNNEL_PID=$!
  local u=""
  for _ in $(seq 1 40); do
    u="$(grep -oE 'https://[a-z0-9-]+\.trycloudflare\.com' "$log" | head -1 || true)"
    [ -n "$u" ] && break
    kill -0 "$TUNNEL_PID" 2>/dev/null || break
    sleep 1
  done
  TUNNEL_LOG="$log"
  [ -n "$u" ] && { echo "$u"; return 0; }
  return 1
}

# Drop an already-served inline: the loop has no worker pool of its own.
start_server() {
  if php -m 2>/dev/null | grep -qi '^pcntl'; then
    export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"
    echo "workers: $PHP_CLI_SERVER_WORKERS"
  else
    echo "workers: 1 (no pcntl) -- a slow adb call can stall other tabs"
  fi
  php -S "0.0.0.0:$PORT" router.php &
  SERVER_PID=$!
}

# One end-to-end liveness probe from the phone to its own public URL.
url_alive() {
  curl -fsS -m 8 -o /dev/null "$URL/ping" 2>/dev/null
}

run_once() {
  if [ "${TUNNEL:-0}" = "1" ]; then
    URL="$(open_tunnel)" || { echo "tunnel did not come up"; return 1; }
    export ADBWEBKIT_URL="$URL"
    echo "public: $ADBWEBKIT_URL"
  else
    export ADBWEBKIT_URL="${ADBWEBKIT_URL:-http://127.0.0.1:$PORT}"
    URL="$ADBWEBKIT_URL"
    echo "local:  $ADBWEBKIT_URL"
    echo "        (set TUNNEL=1 for a public URL a far device can open)"
  fi
  echo "console: http://127.0.0.1:$PORT/   ctrl-c to stop"
  start_server
  return 0
}

if [ "${TUNNEL:-0}" != "1" ]; then
  # Local mode: the address never changes, so a plain run is enough.
  run_once
  wait "$SERVER_PID"
  exit 0
fi

# Public mode: supervise. Re-open the tunnel whenever the URL stops answering
# (Cloudflare dropped it) or the process died, so the console heals itself.
fails=0
while true; do
  command -v cloudflared >/dev/null || { echo "cloudflared missing: pkg install cloudflared"; exit 1; }
  if ! run_once; then
    echo "retrying tunnel in 5s"; sleep 5; continue
  fi
  fails=0
  while kill -0 "$SERVER_PID" 2>/dev/null; do
    sleep "$CHECK_INTERVAL"
    if url_alive; then
      fails=0
    else
      fails=$((fails + 1))
      echo "health: $fails/$FAIL_THRESHOLD fail ($(date +%H:%M:%S)) -- $URL"
      # cloudflared exiting is a faster, unambiguous signal than the probe.
      if [ "$fails" -ge "$FAIL_THRESHOLD" ] || ! kill -0 "$TUNNEL_PID" 2>/dev/null || ! kill -0 "$SERVER_PID" 2>/dev/null; then
        echo "tunnel dead -- restarting on a fresh URL"
        break
      fi
    fi
  done
  [ -n "$SERVER_PID" ] && kill "$SERVER_PID" 2>/dev/null || true
  [ -n "$TUNNEL_PID" ] && kill "$TUNNEL_PID" 2>/dev/null || true
  wait "$SERVER_PID" 2>/dev/null || true
  wait "$TUNNEL_PID" 2>/dev/null || true
  sleep 2
done
