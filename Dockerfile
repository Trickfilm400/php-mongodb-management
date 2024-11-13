FROM trafex/php-nginx

RUN rm -r /var/www/html/*

COPY public /var/www/html/public
COPY internal /var/www/html/internal
COPY vendor /var/www/html/vendor
COPY public/index.html /var/www/html/public/
COPY composer.phar /var/www/html
COPY composer.json /var/www/html
USER root
RUN apk add php83-mongodb
RUN php composer.phar install
RUN sed -i "s/\/var\/www\/html/\/var\/www\/html\/public/g" /etc/nginx/conf.d/default.conf

USER nobody