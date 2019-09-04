.PHONY: test build

build:
	docker build -t guzzlecloudtest .
vendor:
	docker run --rm -ti -v "${PWD}:/app" guzzlecloudtest composer.phar install

test: vendor
	docker run --rm --name guzzlecloudtest -ti -v ${PWD}:/app/ -w /app guzzlecloudtest php -d max_execution_time=5 vendor/bin/phpunit
