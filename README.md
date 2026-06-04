# ShopNest Vulnerable Lab

Local PHP lab app.

## Run

```bash
php -S localhost:8090 -t /home/kali/Desktop/cursor/vuln-web
```

## Seeded Users

| Username | Password | Role |
| --- | --- | --- |
| `admin` | `admin123` | `admin` |
| `john_doe` | `john1234` | `user` |
| `alice` | `alice123` | `user` |

## Access

- Feedback page: `/xss`, feedback save/list require login.
- XML import: `/xml`, admin only.
- Account page: `/account`, login required.

## Lab Vulnerabilities

- SQL injection: `/products?category=...`
- SQL injection auth bypass: `/login`
- OS command injection: `/support?tracking=...`
- Reflected/stored/DOM XSS: `/xss`
- XML entity expansion: `/xml`
