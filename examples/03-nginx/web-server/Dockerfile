FROM ciandtchina/drupal-web:php7.4-apache-devpack

RUN apt-get update \
    && pecl install redis \
    && docker-php-ext-enable redis