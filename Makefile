.DEFAULT_GOAL := help
SHELL := /bin/bash

## —— Setup ————————————————————————————————————————————————————————————
install: ## Install PHP dependencies
	composer install

db-up: ## Start PostgreSQL
	docker compose up -d database

db-migrate: ## Apply migrations on the dev database
	php bin/console doctrine:migrations:migrate --no-interaction

db-test: ## Create and migrate the test database
	php bin/console doctrine:database:create --env=test --if-not-exists
	php bin/console doctrine:migrations:migrate --no-interaction --env=test

setup: install db-up db-migrate db-test ## Full local setup

## —— Run ——————————————————————————————————————————————————————————————
serve: ## Run the dev server on http://127.0.0.1:8000
	php -S 127.0.0.1:8000 -t public

up: ## Run the full production-like stack on http://127.0.0.1:8080
	docker compose up --build -d

down: ## Stop the stack
	docker compose down

logs: ## Follow application logs
	docker compose logs -f app

## —— Quality ——————————————————————————————————————————————————————————
test: ## Run the whole test suite
	php vendor/bin/phpunit

test-unit: ## Run unit tests only (no database needed)
	php vendor/bin/phpunit --testsuite unit

stan: ## Static analysis (PHPStan level 8)
	php vendor/bin/phpstan analyse

lint: ## Check coding standards
	php vendor/bin/php-cs-fixer fix --dry-run --diff

fix: ## Fix coding standards
	php vendor/bin/php-cs-fixer fix

ci: lint stan test ## Everything the CI pipeline runs

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-14s\033[0m %s\n", $$1, $$2}'

.PHONY: install db-up db-migrate db-test setup serve up down logs test test-unit stan lint fix ci help
