FROM phpswoole/swoole

RUN apt-get update \
    && pecl install redis \
    && docker-php-ext-enable redis
COPY ./rootfilesystem/ /
