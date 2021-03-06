FROM php:7.2-alpine

RUN \
    apk add wget --no-cache --virtual .build-deps && \
    docker-php-ext-install -j$(nproc) bcmath && \
    wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet && \
    apk del wget --purge .build-deps
