FROM php:7.4-fpm-alpine

RUN apk update; \
    apk upgrade; \
    apk add --no-cache tzdata;\
    docker-php-ext-install mysqli pdo pdo_mysql;

ENV TZ=Asia/Taipei

COPY scrapyShedule /etc/cron.d/scrapyShedule

RUN chmod 755 /etc/cron.d/scrapyShedule

RUN touch /var/log/cron.log

RUN crontab /etc/cron.d/scrapyShedule

CMD ["crond", "-f"]