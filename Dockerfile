ARG PHP_VERSION=7.1
FROM chialab/php:${PHP_VERSION}-apache
MAINTAINER dev@chialab.io

# Install Wait-for-it and configure PHP
RUN curl -o /wait-for-it.sh https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /wait-for-it.sh \
    && echo "[PHP]\noutput_buffering = 4096\nmemory_limit = -1" > /usr/local/etc/php/php.ini

# Copy files
COPY . /var/www/html

ARG DEBUG
ENV DEBUG ${DEBUG:-false}

# Setup `webroot/_files` for media files
WORKDIR /var/www/html
RUN chmod a+rwx /var/www/html/webroot/_files
RUN chown -R www-data:www-data /var/www/html/webroot/_files
VOLUME /var/www/html/webroot/_files

# Install dependencies
RUN if [ ! "$DEBUG" = "true" ]; then export COMPOSER_ARGS='--no-dev'; fi \
    && composer install $COMPOSER_ARGS --optimize-autoloader --no-interaction

# Activate headers module
RUN a2enmod headers

# Set CORS headers in .htaccess
RUN echo "Header Set Access-Control-Allow-Origin \"*\"" >> /var/www/html/webroot/.htaccess \
    && echo "Header Set Access-Control-Allow-Headers \"content-type, origin, x-api-key, x-requested-with, authorization\"" >> /var/www/html/webroot/.htaccess \
    && echo "Header Set Access-Control-Allow-Methods \"PUT, GET, POST, PATCH, DELETE, OPTIONS\"" >> /var/www/html/webroot/.htaccess

ENV LOG_DEBUG_URL="console:///?stream=php://stdout" \
    LOG_ERROR_URL="console:///?stream=php://stderr" \
    DATABASE_URL="sqlite:////var/www/html/bedita.sqlite"

COPY docker-entrypoint.sh /usr/local/bin/
ENTRYPOINT ["docker-entrypoint.sh"]
HEALTHCHECK --interval=30s --timeout=3s --start-period=1m \
    CMD curl -f http://localhost/status || exit 1

CMD ["apache2-foreground"]
