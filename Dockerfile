FROM chialab/php:7.1-apache
MAINTAINER dev@chialab.io

# Install Wait-for-it and configure PHP
RUN curl -o /wait-for-it.sh https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /wait-for-it.sh \
    && echo "[PHP]\noutput_buffering = 4096\nmemory_limit = -1" > /usr/local/etc/php/php.ini

# Copy files
COPY . /var/www/html

ARG DEBUG
ENV DEBUG ${DEBUG:-false}

# Install libraries
WORKDIR /var/www/html
RUN if [ ! "$DEBUG" = "true" ]; then export COMPOSER_ARGS='--no-dev'; fi \
    && composer install $COMPOSER_ARGS --optimize-autoloader --no-interaction --quiet

ENV LOG_DEBUG_URL="console:///?stream=php://stdout" \
    LOG_ERROR_URL="console:///?stream=php://stderr" \
    DATABASE_URL="sqlite:////var/www/html/bedita.sqlite"

COPY docker-entrypoint.sh /usr/local/bin/
ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["apache2-foreground"]
