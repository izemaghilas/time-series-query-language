FROM php:8.1.0RC5-fpm-alpine3.14

# install composer
COPY --from=composer:2.1.11 /usr/bin/composer /usr/bin/composer

# install xdebug
RUN apk add --no-cache \
		$PHPIZE_DEPS \
    && pecl install xdebug-3.1.1 \
    && docker-php-ext-enable xdebug

WORKDIR /home/dsql-interpreter/