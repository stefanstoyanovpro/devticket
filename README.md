# DevTicket

A ticket-tracking app I built to practice, hands-on, the kind of work I do professionally as a SW/App/Cloud Tech Analyst at Accenture — server administration, AWS/Azure cloud management, security, and troubleshooting. At work I manage environments that already exist; here I wanted to build, secure, and deploy one myself, end to end, and deal with the real problems that come up along the way.

**Live demo:** https://devticket.duckdns.org:8443

## Screenshots

*(ticket list showing a realistic ops ticket — "P1 – Critical Disk Utilization: Root Filesystem at 98%" — open, high priority)*

## Stack

PHP 8.2 · MariaDB · Docker + Docker Compose · Apache · AWS EC2 · Let's Encrypt HTTPS · GitHub Actions CI

## What it does

Register, log in, create and view tickets — title, description, priority, status. Nothing fancy, but it works the way a small internal tool would.

## Security & practices

- Prepared statements (no raw SQL string building)
- Passwords hashed with bcrypt
- Secrets in environment variables, not in code
- Key-only SSH, fail2ban, firewall limited to needed ports
- Trusted HTTPS cert (Let's Encrypt), not self-signed
- Automated tests + CI pipeline on every push

## Run it locally

```bash
git clone https://github.com/stefanstoyanovpro/devticket.git
cd devticket
cp .env.example .env
docker compose up -d --build
```

## Tests

```bash
docker compose exec web composer install
docker compose exec web ./vendor/bin/phpunit tests/
```

## Next up

Edit/delete on tickets, more test coverage, reverse proxy setup if I add more projects to the same server.
