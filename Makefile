.PHONY: test test-unit test-integration cs-fix cs-check fetch-github-commits docker-up docker-down migrate sync-gh-commits setup

# Default target
test: test-unit test-integration

# Run unit tests
test-unit:
	@echo "Running unit tests..."
	cd source && ./vendor/bin/phpunit tests/Unit --colors=always

# Run integration tests
test-integration:
	@echo "Running integration tests..."
	cd source && ./vendor/bin/phpunit tests/Integration --colors=always

# Install dependencies
install:
	cd source && composer install

# Update dependencies
update:
	cd source && composer update

# Fix code style
cs-fix:
	@echo "Fixing code style..."
	cd source && ./vendor/bin/php-cs-fixer fix

# Check code style
cs-check:
	@echo "Checking code style..."
	cd source && ./vendor/bin/php-cs-fixer fix --dry-run --diff

# Docker operations
docker-up:
	@echo "Starting Docker containers..."
	docker-compose up -d

docker-down:
	@echo "Stopping Docker containers..."
	docker-compose down

docker-logs:
	@echo "Showing Docker logs..."
	docker-compose logs -f

# Database operations
migrate:
	@echo "Running database migrations..."
	cd source && php scripts/migrate.php

# Create .env file from example (if it doesn't exist)
setup-env:
	@if [ ! -f source/.env ]; then \
		echo "Creating .env file..."; \
		echo "DB_CONNECTION=mysql" > source/.env; \
		echo "DB_HOST=localhost" >> source/.env; \
		echo "DB_PORT=3306" >> source/.env; \
		echo "DB_DATABASE=git_api_service_db" >> source/.env; \
		echo "DB_USERNAME=user" >> source/.env; \
		echo "DB_PASSWORD=password123" >> source/.env; \
		echo "GITHUB_API_BASE_URL=https://api.github.com" >> source/.env; \
		echo ".env file created"; \
	else \
		echo ".env file already exists"; \
	fi

sync-gh-commits:
	@echo "Syncing commits from GitHub..."
	@docker-compose exec app php scripts/sync_github_commits.php

# Setup complete development environment
setup: setup-env docker-up
	@echo "Waiting for database to be ready..."
	@sleep 10
	@make migrate
	@echo "Development environment is ready!" 