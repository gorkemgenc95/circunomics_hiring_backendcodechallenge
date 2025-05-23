.PHONY: test test-unit test-integration test-coverage cs-fix cs-check fetch-github-commits

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

# Run tests with coverage report
test-coverage:
	@echo "Running tests with coverage report..."
	cd source && XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage --coverage-text

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

# Fetch GitHub commits
# Usage: make fetch-commits [OWNER=owner] [REPO=repo] [LIMIT=limit]
# Example: make fetch-commits OWNER=symfony REPO=symfony LIMIT=20
fetch-github-commits:
	@cd source && php scripts/fetch_github_commits.php \
		$(if $(OWNER),--owner=$(OWNER)) \
		$(if $(REPO),--repo=$(REPO)) \
		$(if $(LIMIT),--limit=$(LIMIT)) 