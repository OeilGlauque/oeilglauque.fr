FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php:8.2.10-fpm-alpine3.17

RUN docker-php-ext-install pdo_mysql

# installation des dépendances et des outils weasyprint et pikepdf pour l'export en pdf des parties
RUN apk add py3-pip py3-pillow py3-cffi py3-brotli gcc musl-dev python3-dev pango

RUN pip install weasyprint pikepdf

# installation des polices de texte
RUN apk --update --upgrade --no-cache add fontconfig ttf-freefont font-noto terminus-font \ 
   && fc-cache -f \ 
   && fc-list | sort 

COPY --from=php_extension_installer --link /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions apcu gmp xml zip intl

# installation de composer
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
