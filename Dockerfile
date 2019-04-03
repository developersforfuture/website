FROM registry.gitlab.com/froscon/php-track-web/alpine-node-builder:1.0.0 as node_builder

COPY / /

#ENV NPM_CONFIG_CACHE /cache/npm
#RUN cd /app/src/ \
#    && mkdir -p /app/src/web/build \
#    && yarn install \
#    && yarn run build:prod

FROM registry.gitlab.com/froscon/php-track-web/alpine-php7.2-builder:1.0.0 as composer_builder

ARG composer_cache_dir="/build_cache/composer/"
ENV COMPOSER_HOME $composer_cache_dir

COPY / /

FROM registry.gitlab.com/froscon/php-track-web/alpine-php-fpm7.2-nginx:1.0.0

COPY / /
COPY --from=composer_builder /usr/local/bin/composer /usr/local/bin/composer
COPY --from=composer_builder /usr/local/bin/composer-install-wrapper.sh /usr/local/bin/composer-install-wrapper.sh

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

LABEL image.name= \
      image.version=0.1.17 \
      image.tag=registry.gitlab.com/developersforfuture/registry/app-production:0.1.17 \
      image.scm.commit=$commit \
      image.scm.url=git@github.com:developersforfuture/website.git \
      image.author="Maximilian Berghoff <maximilian.berghoff@gmx.de>"
