#!/usr/bin/env bash
set -eo pipefail
shopt -s nullglob

if [ ! -z "${DATABASE_URL}" ]; then
    DATABASE_HOST=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_HOST) . ':' . parse_url(getenv('DATABASE_URL'), PHP_URL_PORT);")
    if [ "$DATABASE_HOST" != ":" ]; then
        /wait-for-it.sh ${DATABASE_HOST} -s -t 0 -- echo '=====> Database Ready'
    fi
    bin/cake migrations migrate -p BEdita/Core
    bin/cake migrations seed -p BEdita/Core --seed InitialSeed

    if [ ! -z "${BEDITA_API_KEY}" ]; then
        bin/cake migrations seed -p BEdita/Core --seed ApplicationFromEnvSeed
    fi

    if [[ ! -z "${BEDITA_ADMIN_USR}" && ! -z "${BEDITA_ADMIN_PWD}" ]]; then
        bin/cake migrations seed -p BEdita/Core --seed AdminFromEnvSeed
    fi

    chmod -R a+rwX tmp
    chmod -R a+rwX logs

    DATABASE_VENDOR=$(php -r "echo explode('://', getenv('DATABASE_URL'))[0];")
    echo "=====> Vendor: ${DATABASE_VENDOR}"
    if [ "$DATABASE_VENDOR" = "sqlite" ]; then
        DATABASE_PATH=$(php -r "echo substr(parse_url(preg_replace('/^([\\w\\\\\\]+)/', 'file', getenv('DATABASE_URL')), PHP_URL_PATH), 1);")
        echo "=====> Path: ${DATABASE_PATH}"
        chmod a+rwx logs ${DATABASE_PATH}
    fi
fi

exec "$@"
