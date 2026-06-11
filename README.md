# ShopNest Local Test App

Local PHP app used with the Rell scanner.

## Run

```bash
cd /home/kali/Desktop/cursor/vuln-web
./start.sh 8030
```

This starts:

```text
Website:   http://127.0.0.1:8030
WebSocket: ws://127.0.0.1:8031/orders/live
```

The first argument is the website port. The WebSocket server runs on `port + 1`.

## Seeded Users

| Username | Password | Role |
| --- | --- | --- |
| `admin` | `admin123` | `admin` |
| `john_doe` | `john1234` | `user` |
| `alice` | `alice123` | `user` |

## Access

- Feedback page: `/feedback`, feedback save/list require login.
- XML import: `/admin/import`, admin only.
- Account page: `/account`, login required.
- Reports page: `/admin/reports`.
- Live updates page: `/orders/live`.

## Scanner Targets

- SQL injection: `/products?category=...`
- SQL injection auth bypass: `/login`
- OS command injection: `/orders/track?tracking=...`
- Reflected/stored/DOM XSS: `/feedback`
- XML entity expansion: `/admin/import`
- Path traversal: `/orders/documents?document=...`
- Access control: `/orders/detail?order=...` and `/admin/reports`
- WebSocket access control: `ws://127.0.0.1:<port+1>/orders/live`

## Rell Examples

```bash
cd /home/kali/Desktop/cursor/Rell

.venv/bin/python -m scanner.main scan http://127.0.0.1:8030 --modules all --max-pages 20 --proxy-port 8898
.venv/bin/python -m scanner.main scan http://127.0.0.1:8030 --modules websocket --max-pages 12 --proxy-port 8898
.venv/bin/python -m scanner.main scan http://127.0.0.1:8030 --modules access --proxy-port 8898
```
