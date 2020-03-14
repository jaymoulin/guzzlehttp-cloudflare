COMPOSER ?= install
.PHONY: test build

build:
	docker build -t guzzlecloudtest .
composer:
	docker run --rm -ti -v "${PWD}:/app" -w /app guzzlecloudtest ./composer.phar "${COMPOSER}"
vendor:
	make composer
test: vendor
	docker run --rm --name guzzlecloudtest -ti -v ${PWD}:/app/ -w /app guzzlecloudtest php -d max_execution_time=5 vendor/bin/phpunit
