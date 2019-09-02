VERSION ?= 0.1.0
CACHE ?= --no-cache=1
FULLVERSION ?= ${VERSION}

.PHONY: test

vendor:
	docker run --rm -ti -v "${PWD}:/app" composer install

test: vendor
	docker run --rm --name guzzlecloudtest -ti -v ${PWD}:/app/ -w /app php:7.2-alpine php -d max_execution_time=5 vendor/bin/phpunit
