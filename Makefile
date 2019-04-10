all: build

check-db:
	@command -v sqlite3 >/dev/null 2>&1 || { echo >&2 "Please install sqlite3.  Aborting."; exit 1; }

install: check-db composer.json $(wildcard composer.lock)
	@echo 'Installing composer dependencies (no dev)...'
	php bin/composer.phar install --no-dev --no-interaction --no-progress --quiet

install-dev: check-db composer.json $(wildcard composer.lock)
	@echo 'Installing composer dependencies (including dev)...'
	php bin/composer.phar install --no-interaction --no-progress --quiet

lint: install-dev
	@echo 'Running code formatter...'
	vendor/bin/phpcbf

phpunit: install-dev
	@echo 'Running phpunit...'
	vendor/bin/phpunit --debug --bootstrap vendor/autoload.php tests/

phan: install-dev
	@echo 'Running phan...'
	vendor/bin/phan

test: phpunit phan

build: lint test

.PHONY: all lint phan phpunit test build check-db
