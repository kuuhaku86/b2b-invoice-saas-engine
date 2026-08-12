.DEFAULT_GOAL := help

.PHONY: help up down restart build ps logs shell horizon-logs \
        install composer-install npm-install npm-build npm-dev \
        key migrate migrate-seed migrate-fresh migrate-fresh-seed tinker \
        artisan composer test test-filter dusk pint recurring stripe-listen setup

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

## --- Docker stack -----------------------------------------------------

up: ## Start the full stack (app, nginx, mysql, redis, mailpit, horizon, selenium)
	docker compose up -d --build

down: ## Stop the stack
	docker compose down

restart: ## Restart all containers
	docker compose restart

build: ## Rebuild container images
	docker compose build

ps: ## List running containers
	docker compose ps

logs: ## Tail logs for all services
	docker compose logs -f

horizon-logs: ## Tail Horizon queue worker logs
	docker compose logs -f horizon

shell: ## Open a shell in the app container
	docker compose exec app bash

## --- Dependencies -------------------------------------------------------

install: composer-install npm-install ## Install PHP + JS dependencies

composer-install: ## Install PHP dependencies (in the app container)
	docker compose exec app composer install

npm-install: ## Install JS dependencies (on the host)
	npm install

## --- App setup ----------------------------------------------------------

key: ## Generate the application key
	docker compose exec app php artisan key:generate

migrate: ## Run database migrations
	docker compose exec app php artisan migrate

migrate-seed: ## Run migrations and seed default plans + admin account
	docker compose exec app php artisan migrate --seed

migrate-fresh: ## Drop all tables and re-migrate
	docker compose exec app php artisan migrate:fresh

migrate-fresh-seed: ## Drop all tables, re-migrate, and seed
	docker compose exec app php artisan migrate:fresh --seed

tinker: ## Open a Tinker REPL in the app container
	docker compose exec app php artisan tinker

setup: up composer-install key migrate-seed npm-install npm-build ## First-time setup: build, install deps, migrate+seed, build assets
	@echo "Setup complete. Visit http://saas.test"

## --- Frontend -------------------------------------------------------------

npm-dev: ## Start the Vite dev server (host, HMR)
	npm run dev

npm-build: ## Build frontend assets for production
	npm run build

## --- Misc artisan/composer passthrough ------------------------------------

artisan: ## Run an artisan command, e.g. make artisan ARGS="route:list"
	docker compose exec app php artisan $(ARGS)

composer: ## Run a composer command, e.g. make composer ARGS="require foo/bar"
	docker compose exec app composer $(ARGS)

recurring: ## Run the recurring-invoice generator on demand
	docker compose exec app php artisan invoices:process-recurring

stripe-listen: ## Forward Stripe webhooks to local dev (requires Stripe CLI)
	stripe listen --forward-to http://saas.test/stripe/webhook

## --- Testing & code style ---------------------------------------------

test: ## Run the Pest test suite
	docker compose exec app vendor/bin/pest

test-filter: ## Run Pest filtered by name, e.g. make test-filter FILTER=Tenancy
	docker compose exec app vendor/bin/pest --filter="$(FILTER)"

dusk: ## Run Laravel Dusk browser tests (mutates local dev data, see README)
	docker compose exec app php artisan dusk

pint: ## Run Laravel Pint (code style fixer)
	docker compose exec app vendor/bin/pint
