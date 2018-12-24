VERSION ?= 0.1.0
CACHE ?= --no-cache=1
FULLVERSION ?= ${VERSION}

install:
	docker run --rm -ti -v "${PWD}:/app" composer install
