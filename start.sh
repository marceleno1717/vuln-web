#!/usr/bin/env bash
set -euo pipefail

port="${1:-8017}"
if ! [[ "$port" =~ ^[0-9]+$ ]]; then
    echo "Usage: ./start.sh [port]" >&2
    exit 1
fi

ws_port=$((port + 1))
host="127.0.0.1"
root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

cleanup() {
    kill "$http_pid" "$ws_pid" 2>/dev/null || true
}
trap cleanup EXIT INT TERM

php -S "$host:$port" -t "$root_dir" &
http_pid=$!

php "$root_dir/ws-server.php" "$host" "$ws_port" &
ws_pid=$!

echo "Website:   http://$host:$port"
echo "WebSocket: ws://$host:$ws_port/orders/live"
echo "Press Ctrl-C to stop."

wait
