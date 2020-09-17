FROM httpd:2.4.46-alpine

RUN apk update; \
    apk upgrade; \
    apk add --no-cache tzdata;

ENV TZ=Asia/Taipei

COPY stock.apache.conf /usr/local/apache2/conf/stock.apache.conf
RUN echo "Include /usr/local/apache2/conf/stock.apache.conf" \
    >> /usr/local/apache2/conf/httpd.conf

RUN echo '' > /usr/local/apache2/logs/access.log;\
    echo '' > /usr/local/apache2/logs/error.log
