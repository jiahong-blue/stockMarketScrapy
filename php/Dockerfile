FROM php:7.4-fpm-alpine

RUN apk update; \
    apk upgrade; \
    apk add --no-cache tzdata; \
    docker-php-ext-install mysqli pdo pdo_mysql;

ENV TZ=Asia/Taipei

