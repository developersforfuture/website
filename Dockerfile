# install php dependencies in intermediate container
FROM composer:latest AS composer

WORKDIR /app/src

COPY /app/src/composer.* /app/src/

RUN composer global require hirak/prestissimo --no-plugins --no-scripts
RUN composer install --apcu-autoloader -o  --no-scripts --ignore-platform-reqs
# --no-dev

# install admin javascript dependencies and build assets in intermediate container
FROM node:12 AS node-admin

COPY /app/src/composer.json /app/src/
COPY /app/src/assets/admin /app/src/assets/admin
COPY --from=composer /app/src/vendor/sulu/sulu /app/src/vendor/sulu/sulu
COPY --from=composer /app/src/vendor/friendsofsymfony/jsrouting-bundle /app/src/vendor/friendsofsymfony/jsrouting-bundle

RUN cd /app/src/assets/admin && npm ci && NODE_OPTIONS="--max_old_space_size=4096" npm run build

FROM node:12 AS node-website

COPY /app/src/assets /app/src/assets
COPY /app/src/package.json /app/src
COPY /app/src/package-lock.json /app/src
#RUN cd /app/src && npm ci && NODE_OPTIONS="--max_old_space_size=4096" npm run build

# build actual application image
FROM registry.gitlab.com/froscon/php-track-web/alpine-php-fpm7.2-nginx:1.1.0 AS server

WORKDIR /app/src

RUN apk update \
    && apk add --no-cache \
            libpng \
            libpng-dev \
            gnupg \
            openssl \
            git \
            curl \
            mysql-client \
    && apk --no-cache upgrade \
    && apk add --update  php7-gd  \
            php7-gettext \
            # php7-exif \
            php7-dom \
            php7-pdo_mysql \
            php7-pdo_sqlite \
            php7-bz2 \
            php7-opcache \
            php7-tokenizer \
      && apk del libpng-dev

# configure crontab
COPY / /
RUN crontab /etc/crontab
#COPY --from=node-website /app/src/public/build /app/src/public/build
COPY --from=node-admin /app/src/public/build/admin /app/src/public/build/admin
COPY --from=composer /app/src/vendor/ /app/src/vendor/
COPY --from=composer /app/src/composer.lock /app/src/composer.lock

FROM server AS production

COPY / /
COPY --from=server /app/src  /app/src

FROM server AS staging
COPY / /
COPY --from=server /app/src  /app/src

FROM server AS development

COPY --from=server /app/src  /app/src