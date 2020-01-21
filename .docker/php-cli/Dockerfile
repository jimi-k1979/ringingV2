# Dockerfile for php-cli
# 2019-07-10 - v1 - James K

FROM php:7.3

# install php modules
RUN apt-get update -yq \
    && apt-get install -y zip libzip-dev libpng-dev libxml2-dev git \
    && pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip soap

# install composer globally
RUN curl -sSL https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# ini settings for xdebug
RUN echo "xdebug.remote_enable = 1" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart = 1" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey = \"PHPSTORM\"" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = docker.for.win.localhost" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port = 9000" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /tmp

## PHPUnit installation
#RUN composer selfupdate \
#    && composer require "phpunit/phpunit" --prefer-source --no-interaction \
#    && ln -s /tmp/vendor/bin/phpunit /usr/local/bin/phpunit