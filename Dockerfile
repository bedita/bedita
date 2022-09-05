ARG PHP_VERSION=7.4
FROM chialab/php:${PHP_VERSION}-apache
LABEL author="dev@chialab.io"

# Default config
ARG DEBUG
ENV DEBUG=${DEBUG:-false} \
    LOG_DEBUG_URL="console:///?stream=php://stdout" \
    LOG_ERROR_URL="console:///?stream=php://stderr" \
    DATABASE_URL="sqlite:////var/www/html/bedita.sqlite"

# Install Wait-for-it, copy entrypoint, configure Apache and PHP
RUN curl -o /wait-for-it.sh https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /wait-for-it.sh \
    && a2enmod headers \
    && echo "[PHP]\noutput_buffering = 4096\nmemory_limit = -1" > /usr/local/etc/php/php.ini
COPY docker-entrypoint.sh /usr/local/bin/

# Copy files and set user to `www-data`
COPY . /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
USER www-data:www-data
VOLUME /var/www/html/webroot/_files

# Install dependencies, ensure permissions are correct, and setup permissive CORS rules
RUN if [ ! "$DEBUG" = "true" ]; then export COMPOSER_ARGS='--no-dev'; fi \
    && composer install $COMPOSER_ARGS --optimize-autoloader --no-interaction \
    && chmod -R ug+rwX tmp logs webroot/_files \
    && tee -a webroot/.htaccess < apache_cors.conf

# Restore user `root` to make sure we can bind to address 0.0.0.0:80
USER root:root

# Configure healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=1m \
    CMD curl -f http://localhost/status -H "X-Api-Key: ${BEDITA_API_KEY}" || exit 1

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
