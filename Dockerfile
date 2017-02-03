FROM php:7.1-alpine

# Install inotify
RUN set -xe && \
        apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
        pecl install inotify && docker-php-ext-enable inotify && \
        apk del .build-deps && \
        php -m
