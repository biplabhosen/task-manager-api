# Laravel Sail Makefile
# Common Docker commands for the Task Manager API

.PHONY: help start stop restart logs shell migrate seed test build up down fresh

help: ## Display this help menu
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

start: ## Start the Docker containers
	./vendor/bin/sail up -d

stop: ## Stop the Docker containers
	./vendor/bin/sail stop

restart: ## Restart the Docker containers
	./vendor/bin/sail restart

logs: ## View container logs
	./vendor/bin/sail logs

logs-follow: ## Follow container logs (real-time)
	./vendor/bin/sail logs -f

shell: ## Open a shell in the Laravel container
	./vendor/bin/sail shell
	./vendor/bin/sail bash

artisan: ## Run artisan commands
	./vendor/bin/sail artisan $(filter-out $@,$(MAKECMDGOALS))

migrate: ## Run database migrations
	./vendor/bin/sail artisan migrate

migrate-fresh: ## Fresh migrate with seed
	./vendor/bin/sail artisan migrate:fresh --seed

seed: ## Run database seeders
	./vendor/bin/sail artisan db:seed

test: ## Run PHPUnit tests
	./vendor/bin/sail artisan test

test-coverage: ## Run tests with coverage
	./vendor/bin/sail artisan test --coverage

build: ## Build the Docker containers
	./vendor/bin/sail build

up: ## Start containers in detached mode
	./vendor/bin/sail up -d

down: ## Stop all containers
	./vendor/bin/sail down

fresh: ## Fresh database migration with seeding
	./vendor/bin/sail artisan migrate:fresh --seed

queue: ## Start the queue worker
	./vendor/bin/sail artisan queue:work --tries=3

npm-install: ## Install npm dependencies
	./vendor/bin/sail npm install

npm-dev: ## Run npm dev server
	./vendor/bin/sail npm run dev

npm-build: ## Build npm assets
	./vendor/bin/sail npm run build

composer-install: ## Install composer dependencies
	./vendor/bin/sail composer install

setup: ## Full setup: install dependencies, migrate, and seed
	./vendor/bin/sail composer install
	./vendor/bin/sail npm install
	./vendor/bin/sail artisan migrate:fresh --seed
	./vendor/bin/sail artisan storage:link
