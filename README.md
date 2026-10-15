# DevTicket

I'm a Software/App/Cloud Tech Analyst (Linux/Windows, AWS/Azure, incidents). I put this together to run an environment myself instead of only supporting ones that already exist: a Debian server on AWS EC2 running Docker Compose, with a DuckDNS domain and a Let's Encrypt cert. The tickets are based on the kind of incidents I handle at work. Disk alerts, access issues, infra problems, etc.

**Live demo:** https://devticket.duckdns.org:8443

## Screenshots

<img width="2937" height="1750" alt="Screenshot 2026-10-15 at 12 08 21 (2)" src="https://github.com/user-attachments/assets/4d2fb830-c8fc-4250-a8cd-f7553c37c3cd" />

<img width="2929" height="1558" alt="Screenshot 2026-10-15 at 14 03 59" src="https://github.com/user-attachments/assets/43522f66-93bc-4b52-aae5-43bd0bbfe5b7" />

## What it is

ITSM-style ticket tracker. Category + impact + urgency → priority gets calculated, not picked. Every ticket has a timeline for updates. Knowledge base for writing up problem → root cause → fix.

## Stack

Linux Debian · PHP 8.2 · MariaDB · Docker + Docker Compose · Apache · AWS EC2 · Let's Encrypt HTTPS · GitHub Actions CI

## What went wrong

- Locked myself out of SSH on the VM after changing sshd_config. Recovered through the console and now keep a second session open when editing it.
- Committed my .env by accident, so the DB passwords ended up in git history. Removed it from tracking and rotated the passwords.
- Ran the full Prometheus + Grafana stack on the t2.micro and it froze. Now monitoring only runs on demand.

## Security

Prepared statements · bcrypt · secrets in env vars · key-only SSH + fail2ban · firewall limited to needed ports · trusted HTTPS (Let's Encrypt) · tests + CI on every push

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
## Monitoring

Prometheus + Grafana (node-exporter + cAdvisor) via Docker Compose, fully working. Doesn't run 24/7 — t2.micro can't hold monitoring + app at once, learned that the hard way. On demand: `docker compose up -d node-exporter cadvisor prometheus grafana`.

## Next up

Edit/delete on tickets, more tests, reverse proxy if I add more projects here.
