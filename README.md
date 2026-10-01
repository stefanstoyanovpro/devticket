# DevTicket

I work as a Software/App/Cloud Tech Analyst — Linux/Windows servers, AWS/Azure, incidents, troubleshooting. But always inside an environment someone else already built. I wanted to build and run one myself, start to finish, and deal with whatever broke without anyone to hand it off to.

The tickets are modeled on real incidents I work — disk alerts, access issues, infra problems — not a generic to-do app with a login screen.

**Live demo:** https://devticket.duckdns.org:8443

## Screenshots

(ticket list showing "P1 – Critical Disk Utilization: Root Filesystem at 98%" — open, high priority)

## What it is

ITSM-style ticket tracker. Category + impact + urgency → priority gets calculated, not picked. Every ticket has a timeline for updates. Knowledge base for writing up problem → root cause → fix.

## Stack

PHP 8.2 · MariaDB · Docker + Docker Compose · Apache · AWS EC2 · Let's Encrypt HTTPS · GitHub Actions CI

## What I actually learned

Locked myself out over SSH and had to recover through the console. Found my own DB password sitting exposed in git history and rotated it. Threw Prometheus + Grafana at a free-tier box that couldn't take it and watched it grind to a halt. None of that's in a tutorial — it's just what happens running something yourself.

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

## Backups

Daily cron MySQL dump, last 7 kept. Actually tested the restore — deleted live data, restored, confirmed it came back. A backup you've never restored is just a guess.

## Monitoring

Prometheus + Grafana (node-exporter + cAdvisor) via Docker Compose, fully working. Doesn't run 24/7 — t2.micro can't hold monitoring + app at once, learned that the hard way. On demand: `docker compose up -d node-exporter cadvisor prometheus grafana`.

## Next up

Edit/delete on tickets, more tests, reverse proxy if I add more projects here.
