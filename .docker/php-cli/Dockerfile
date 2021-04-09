# Dockerfile for php-cli
# 2019-07-10 - v1 - James K

FROM php:7.4

# install php modules, removed --with-libzip from zip line
RUN apt-get update -yq \
    && apt-get install -y zip libzip-dev libpng-dev libxml2-dev git wget nano \
    && pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip soap

# install composer globally
RUN curl -sSL https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# ini settings for xdebug
RUN echo "xdebug.mode = debug" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host = 1" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey = \"PHPSTORM\"" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port = 9003" \
        >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# mailhog smtp server
RUN wget https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 \
    && chmod +x mhsendmail_linux_amd64 \
    && mv mhsendmail_linux_amd64 /usr/local/bin/mhsendmail

WORKDIR /tmp
