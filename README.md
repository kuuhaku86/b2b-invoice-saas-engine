# B2B Invoice & Subscription SaaS Engine

A multi-tenant B2B invoicing and subscription SaaS built on Laravel 11, showcasing
database-per-tenant multi-tenancy, Stripe-based recurring billing, async queue
processing (Horizon), and per-plan feature gating.

## Architecture

- **Multi-tenancy:** database-per-tenant via [`stancl/tenancy`](https://tenancyforlaravel.com).
  Each tenant is identified by subdomain (`acme.saas.test`) and gets its own physical
  MySQL database. The apex domain (`saas.test`) is the central/landlord side —
  tenant management, plans, Stripe webhooks, and platform-wide analytics.
- **Billing:** tenants subscribe to a central `Plan` (Free/Starter/Enterprise) via
  Stripe Checkout. Webhooks (signature-verified, idempotent) keep subscription
  status in sync and gate invoice creation on quota + payment status.
- **Async work:** PDF generation and email delivery run as queued jobs (Redis +
  Horizon), tenant-aware via `QueueTenancyBootstrapper` — a job dispatched under
  tenant A always re-initializes tenant A's database when a worker picks it up,
  regardless of which tenant the worker last processed.
- **Frontend:** mostly server-rendered Blade (deliberately unstyled — this is a
  backend-architecture showcase, not a design one). The two analytics dashboards
  (`/dashboard` for tenants, `/admin/dashboard` for the landlord) are Inertia +
  React.

See the git log for how this was built milestone-by-milestone (tenancy → data
model → invoicing → billing → gating → recurring → Horizon → dashboards → tests).

## Prerequisites

- Docker + Docker Compose
- Node.js 18+ (only needed on the host, to build frontend assets — the app
  container itself only has PHP)
- [Stripe CLI](https://docs.stripe.com/stripe-cli) (optional, for local webhook testing)

## Local setup

1. **Environment file**

   ```bash
   cp .env.example .env
   ```

   The defaults already match the Docker Compose services below. Notably:
   - `.env`'s `DB_HOST`/`DB_PORT` (`127.0.0.1:3306`) are what the app
     container uses internally — `docker-compose.yml` overrides `DB_HOST` to
     the service name `mysql` for the `app`/`horizon` containers at runtime,
     so these values only matter for tooling running *inside* a container.
     To reach MySQL from the **host** (e.g. a GUI client), use
     `127.0.0.1:3307` instead — published on 3307, not 3306, to avoid
     clashing with a MySQL instance you might already have running locally.
     Same idea for Redis: internally `6379`, externally `6390`.
   - `DB_USERNAME=root` — the app needs `CREATE DATABASE`/`DROP DATABASE`
     privileges to provision and tear down tenant databases dynamically.

2. **Wildcard subdomain hosts**

   Tenants live on subdomains of `saas.test`. Add entries for the central
   domain and any demo tenants you create to `/etc/hosts` (no wildcard support
   in the hosts file format, so add them individually):

   ```
   127.0.0.1 saas.test
   127.0.0.1 acme.saas.test
   ```

3. **Start the stack**

   ```bash
   docker compose up -d --build
   ```

   This brings up `app` (PHP-FPM), `nginx`, `mysql`, `redis`, `mailpit`,
   `horizon` (the queue worker), and `selenium` (for browser tests — see
   below). Nginx listens on port 80.

4. **Install PHP dependencies and set up the central database**

   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```

   The seeder creates the default plans (Free/Starter/Enterprise) and a
   landlord admin account: **admin@saas.test / password**. Change this before
   deploying anywhere real.

5. **Build frontend assets** (on the host — the app container has no Node)

   ```bash
   npm install
   npm run build
   ```

   For active frontend development, `npm run dev` works too — Vite's dev
   server runs on the host and the `@vite` directive points the browser at
   it directly (`localhost:5173`) for hot module reloading; `npm run build`
   is still what actually ships for anything served through nginx.

6. **Create a demo tenant**

   Log in to `http://saas.test/login` as the admin above, then use
   **Tenants → New tenant** to create one (e.g. subdomain `acme`, any plan).
   This synchronously provisions a real `tenantacme` database and runs all
   tenant migrations against it.

   Alternatively, via tinker:

   ```bash
   docker compose exec app php artisan tinker --execute="
   \$t = App\Models\Tenant::create(['id' => 'acme', 'name' => 'Acme Corp']);
   \$t->domains()->create(['domain' => 'acme']);
   "
   ```

   Then register a user at `http://acme.saas.test/register` to get into the
   tenant app itself (clients, invoices, recurring schedules, billing).

## Makefile shortcuts

Steps 3–5 above (and most other day-to-day commands) are wrapped in a
`Makefile`. Run `make help` for the full, self-documenting list; the most
common ones:

```bash
make setup            # build, install deps, migrate+seed, build assets (steps 3-5)
make up               # docker compose up -d --build
make down             # docker compose down
make shell            # shell into the app container
make migrate-seed     # php artisan migrate --seed
make migrate-fresh-seed  # php artisan migrate:fresh --seed
make tinker           # php artisan tinker
make test             # vendor/bin/pest
make test-filter FILTER=Tenancy  # vendor/bin/pest --filter=Tenancy
make dusk             # php artisan dusk (browser tests)
make pint             # code style fixer
make npm-dev          # vite dev server (HMR)
make artisan ARGS="route:list"   # arbitrary artisan command
make composer ARGS="require foo/bar"  # arbitrary composer command
```

## Stripe webhooks (local dev)

The webhook endpoint is `POST http://saas.test/stripe/webhook` — a single
fixed URL regardless of tenant (Stripe has no concept of our subdomains; the
tenant is resolved server-side from `stripe_customer_id`).

To forward real Stripe test-mode events to it locally:

```bash
stripe listen --forward-to http://saas.test/stripe/webhook
```

Copy the `whsec_...` signing secret it prints into `STRIPE_WEBHOOK_SECRET` in
`.env`, then trigger events to exercise the handled cases:

```bash
stripe trigger invoice.payment_succeeded
stripe trigger invoice.payment_failed
stripe trigger customer.subscription.deleted
```

You'll also need real `STRIPE_KEY`/`STRIPE_SECRET` test keys (and a Stripe
Price id set on each `Plan.stripe_price_id`) for the **Subscribe** button in
`/billing` to actually create a Checkout Session — the placeholders in
`.env.example` are enough to exercise webhook handling, but not checkout
creation itself.

## Queues and the scheduler

`docker compose up` already runs a `horizon` container processing the
`default` Redis queue — this handles PDF generation, email delivery, and
Stripe webhook processing. Check `http://saas.test/horizon` (requires the
admin login above).

The recurring-invoice generator (`invoices:process-recurring`) is registered
in `routes/console.php` to run daily via Laravel's scheduler. Locally, run it
on demand:

```bash
docker compose exec app php artisan invoices:process-recurring
```

or `make recurring`.

In production, point a real system cron at `schedule:run` every minute, as
usual for Laravel:

```
* * * * * php artisan schedule:run >> /dev/null 2>&1
```

## Testing

```bash
docker compose exec app vendor/bin/pest
```

or `make test` (`make test-filter FILTER=Tenancy` to filter by name). Tests run against a separate `saas_central_testing` database (see
`phpunit.xml`), migrated fresh automatically on first use — your dev data in
`saas_central`/`tenantacme` is never touched. Tests that provision tenants
clean up the physical databases they create in `tearDown()`.

Deliberately **not** using `RefreshDatabase`: it wraps each test in an open
transaction, and these tests provision real tenant databases (`CREATE`/`DROP
DATABASE`) via separate connections mid-test — that combination deadlocks
MySQL. See the comment in `tests/Pest.php` for the full explanation.

CI (`.github/workflows/tests.yml`) runs the same suite against MySQL/Redis
service containers on every push and PR to `main`.

### End-to-end (browser) tests

```bash
docker compose exec app php artisan dusk
```

or `make dusk`. These use [Laravel Dusk](https://laravel.com/docs/dusk) to drive a real
headless Chrome — via the `selenium` service, since the `app` container is
PHP-only and has no browser to run locally — against the actual dev stack
your browser would hit at `http://saas.test`. They exist specifically to
cover what `tests/Feature` structurally cannot: Laravel's HTTP test client
shares in-process session state across simulated requests regardless of
target host, so it can't verify real cookie scoping. `tests/Browser/
TenantIsolationTest.php` logs in on one tenant subdomain with a real browser
and confirms the session cookie isn't honored on another — the actual thing
protecting tenants from each other in production.

A few things follow from testing against the real stack rather than an
isolated one (see `phpunit.dusk.xml` for why there's no env override here,
unlike `phpunit.xml`):

- Browser tests **do mutate your local dev database** (`saas_central` and
  whatever tenant databases they create). They use fixed tenant ids
  (`e2ea`, `e2eb`) reserved and cleaned up for this purpose — don't reuse
  those ids for real dev tenants.
- Those two hostnames need to resolve *inside* the Docker network (so
  Selenium's Chrome can reach them) — see the `nginx` service's `aliases`
  in `docker-compose.yml`. If you add more browser tests against new
  subdomains, add an alias for each.
- `tearDown()` in `tests/DuskTestCase.php` drops any tenant a test created,
  same pattern as the Feature test suite.

## Notable design decisions

- **`asset_helper_tenancy` is disabled** in `config/tenancy.php`. It rewrites
  `asset()` calls to tenant-scoped URLs, which breaks Vite's build manifest
  resolution (the compiled JS/CSS bundle is global, not per-tenant data).
- **Central index of Stripe/subscription identifiers on `tenants`**
  (`stripe_customer_id`, `subscription_status`, etc.) mirrors state that
  otherwise lives in each tenant's own database, so the webhook handler and
  central analytics dashboard can look it up in one query instead of
  iterating every tenant database.
- **Custom columns on the `Tenant` model:** any real (non-JSON-blob) column
  you add to the `tenants` table must also be added to
  `Tenant::getCustomColumns()` — otherwise `stancl/tenancy`'s virtual-column
  trait silently redirects writes into the `data` JSON column instead.
- **Sessions use the `cookie` driver**, not `database` — tenant databases
  don't carry a `sessions` table, and a stateless cookie session avoids that
  dependency entirely.
