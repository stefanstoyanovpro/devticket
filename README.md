# DevTicket

A hands-on infrastructure project inspired by my experience as a Software/Application/Cloud Tech Analyst, where I worked with Linux/Windows environments, cloud infrastructure, monitoring, incidents, and troubleshooting.

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

## Backups

Automated daily MySQL dump via cron (2 AM), keeping the last 7 backups. Tested restore process — verified by deleting live data and confirming full recovery from a backup file:

## Monitoring

Built out Prometheus + Grafana (node-exporter + cAdvisor) via Docker Compose, got it fully working — scraping, dashboards, the works. Don't run it 24/7 though: the t2.micro this sits on doesn't have the RAM for monitoring + app at the same time (found that out the hard way). Spins up on demand with `docker compose up -d node-exporter cadvisor prometheus grafana`.

## Next up

Edit/delete on tickets, more test coverage, reverse proxy setup if I add more projects to the same server.
