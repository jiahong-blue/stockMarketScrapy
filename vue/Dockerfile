FROM node:14.8.0

RUN apk update; \
    apk upgrade; \
    apk add --no-cache tzdata;

ENV TZ=Asia/Taipei

RUN npm install -g @vue/cli

WORKDIR /var/vue
