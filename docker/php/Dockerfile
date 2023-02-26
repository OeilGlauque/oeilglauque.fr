FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php:8.1.16-fpm-alpine3.17

RUN docker-php-ext-install pdo_mysql

COPY --from=php_extension_installer --link /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions apcu gmp xml zip intl

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /srv/app

VOLUME /srv/app

COPY --link docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

#ENV COMPOSER_ALLOW_SUPERUSER=1

#RUN composer install --no-dev --optimize-autoloader

RUN PATH=$PATH:/srv/app/oeilglauque.fr/vendor/bin:bin

#RUN php bin/console doctrine:migrations:diff && \
#    php bin/console doctrine:migrations:migrate
