FROM registry.gitlab.com/froscon/php-track-web/alpine-node-builder:1.0.0 as node_builder

ENV NPM_CONFIG_CACHE /cache/npm

COPY /app/src/package.json /app/src/webpack.config.js /app/src/
WORKDIR /app/src/
RUN yarn install
COPY /app/src/assets/ /app/src/assets
RUN mkdir -p /app/src/public/build && yarn build

FROM registry.gitlab.com/froscon/php-track-web/alpine-php7.2-builder:1.0.0 as composer_builder

ARG composer_cache_dir="/build_cache/composer/"
ENV COMPOSER_HOME $composer_cache_dir
WORKDIR /app/src/
COPY /app/src/ /app/src/

RUN apk update \
    && apk add --no-cache \
            libpng \
            libpng-dev \
            gnupg \
            openssl \
            git \
            curl \
            mysql-client \
            icu-dev \
            gettext-dev && \
    apk add --update  php7-gd \
            php7-gettext \
            # php7-exif \
            php7-dom \
            php7-pdo_mysql \
            php7-pdo_sqlite \
            php7-bz2 \
            php7-opcache \
            php7-tokenizer && \
    /usr/local/bin/composer-install-wrapper.sh

# Build the PHP container
FROM registry.gitlab.com/froscon/php-track-web/alpine-php-fpm7.2-nginx:1.0.0

COPY / /
COPY --from=node_builder /app/src/public/build/ /app/src/public/build
COPY --from=composer_builder /app/src/vendor/ /app/src/vendor/
COPY --from=composer_builder /app/src/composer.lock /app/src/composer.lock

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
      && apk del libpng-dev \
      && cd /app/src/


WORKDIR /app/src

ENV VERSION_TAG 3.5.3
LABEL image.name=frontend \
      image.version=3.5.3 \
      image.tag=registry.gitlab.com/developersforfuture/registry/app-production \
      image.scm.commit=$commit \
      image.scm.url=git@github.com:developersforfuture/website.git \
      image.author="Maximilian Berghoff <maximilian.berghoff@gmx.de>"
