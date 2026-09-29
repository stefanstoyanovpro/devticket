# DevTicket

A small ticket/issue tracker I built to learn the full path from "Linux VM on my laptop" to a properly hardened, containerized, tested web app. Started as a plain LAMP stack in a UTM VM, ended up here.

## Why this exists

I wanted a project that wasn't just CRUD-in-a-tutorial. So along the way I kept adding the things a real small app actually needs: auth, tests, a firewall, a CI pipeline. Some of it I broke and had to debug properly (there's a longer story behind the SSH lockout and the Docker/SSL config mismatch than I'll put here, but both taught me more than if things had just worked first try).

## Stack

- PHP 8.2 (mysqli, prepared statements throughout)
- MariaDB
- Apache, running in Docker (two containers: app + db)
- Docker Compose for orchestration
- PHPUnit for the core auth logic
- GitHub Actions for CI (tests + Docker build on every push)

## Features

- Create/view tickets with title, description, priority, status
- User accounts — register, login, logout, sessions
- Passwords hashed with bcrypt (`password_hash()` / `password_verify()`)
- Tickets are linked to the user who created them

## Security bits worth mentioning

- All DB queries use prepared statements, not string concatenation
- DB credentials live in environment variables (`.env`, gitignored), not in code
- SSH access is key-only, with fail2ban watching for brute-force attempts
- ufw firewall, only necessary ports open
- Self-signed TLS cert for local HTTPS (would swap for Let's Encrypt on a real domain)

## Running it locally

```bash
git clone https://github.com/stefanstoyanovpro/devticket.git
cd devticket
cp .env.example .env
# edit .env with your own values
docker compose up -d --build
```

Then visit `http://localhost:8080` (or `https://localhost:8443` for the self-signed HTTPS version).

## Running the tests

```bash
docker compose exec web composer install
docker compose exec web ./vendor/bin/phpunit tests/
```

## What's not done yet

- Not deployed to a public server yet (AWS EC2 is the plan — held up on getting the account sorted, not a technical blocker)
- No edit/delete on tickets yet, just create + view
- Tests only cover the auth helper functions right now, not the full request flow

## What I'd do differently with more time

Probably split the Docker setup into separate dev/prod compose files, and add a couple of integration tests instead of just unit tests on the helper functions. Also the SSL cert handling in the Dockerfile is a bit manual — would look at automating that with Let's Encrypt once there's a real domain involved.
