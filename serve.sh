#!/data/data/com.termux/files/usr/bin/bash
# Start the console. adbwebkit over the built-in PHP server, plus the link gate.
#
#   ./serve.sh                 # local only, port 8080
#   ./serve.sh 9090            # local only, port 9090
#   TUNNEL=1 ./serve.sh        # also open a public cloudflared URL (what a
#                              # link needs: the target is NOT on your LAN)
#
# The public address is what gets baked into the agent, so it is fixed for the
# whole run. If TUNNEL=1 it is read from cloudflared and exported; otherwise
# set ADBWEBKIT_URL yourself.
set -euo pipefail
cd "$(dirname "$0")"

PORT="${1:-${ADBWEBKIT_PORT:-8080}}"
export ADBWEBKIT_PORT="$PORT"
export ADBWEBKIT_ALLOW_PRIVATE="${ADBWEBKIT_ALLOW_PRIVATE:-}"

command -v php >/dev/null || { echo "php missing: pkg install php"; exit 1; }
command -v adb >/dev/null || { echo "adb missing: pkg install android-tools"; exit 1; }

mkdir -p linkdata linkdata/s linkdata/apk
adb start-server >/dev/null 2>&1 || true
# A console that already owns a key makes every wireless reconnect silent.
php -r 'require "link/adbctrl.php"; adb_ensure_keys();' >/dev/null 2>&1 || true

TUNNEL_PID=""
if [ "${TUNNEL:-0}" = "1" ]; then
  command -v cloudflared >/dev/null || { echo "cloudflared missing: pkg install cloudflared"; exit 1; }
  LOG="$(mktemp)"
  cloudflared tunnel --url "http://127.0.0.1:$PORT" --no-autoupdate >"$LOG" 2>&1 &
  TUNNEL_PID=$!
  for _ in $(seq 1 30); do
    URL="$(grep -oE 'https://[a-z0-9-]+\.trycloudflare\.com' "$LOG" | head -1 || true)"
    [ -n "${URL:-}" ] && break
    sleep 1
  done
  if [ -z "${URL:-}" ]; then echo "tunnel did not come up; see $LOG"; kill "$TUNNEL_PID" 2>/dev/null; exit 1; fi
  export ADBWEBKIT_URL="$URL"
  echo "public: $ADBWEBKIT_URL"
else
  export ADBWEBKIT_URL="${ADBWEBKIT_URL:-http://127.0.0.1:$PORT}"
  echo "local:  $ADBWEBKIT_URL"
  echo "        (set TUNNEL=1 for a public URL a far device can open)"
fi

echo "console: http://127.0.0.1:$PORT/   ctrl-c to stop"
trap '[ -n "$TUNNEL_PID" ] && kill "$TUNNEL_PID" 2>/dev/null || true' EXIT

# php -S alone serves one request at a time, so a slow adb call in one tab
# freezes every other tab and the visitor's beacon. Workers fix that without a
# real web server. Needs pcntl; if it is absent, run 1 worker and accept it.
if php -m 2>/dev/null | grep -qi '^pcntl'; then
  export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"
  echo "workers: $PHP_CLI_SERVER_WORKERS"
else
  echo "workers: 1 (no pcntl) -- a slow adb call can stall other tabs"
fi
exec php -S "0.0.0.0:$PORT" router.php
