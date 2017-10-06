<?php
/**
 * This file may be used to set some environment variables
 *
 * You may use indifferently $_ENV or putenv()
 * Since this is really implementation dependent no reasonable default is proposed
 * but only a simple list of possible settings.
 */

// Cache environment variables examples.
//$_ENV['CACHE_DEFAULT_URL'] = 'file://tmp/cache/?prefix=my_app_default&duration=3600'
//$_ENV['CACHE_CAKECORE_URL'] = 'file://tmp/cache/persistent?prefix=my_app_cake_core&serialize=true&duration=3600'
//$_ENV['CACHE_CAKEMODEL_URL'] = 'file://tmp/cache/models?prefix=my_app_cake_model&serialize=true&duration=3600'

// Database configuration DSN settings examples.
//$_ENV[DATABASE_URL="mysql://my_app:secret@localhost/${APP_NAME}?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"
//$_ENV[DATABASE_TEST_URL="mysql://my_app:secret@localhost/test_${APP_NAME}?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"

// Other useful environment variables are: EMAIL_TRANSPORT_DEFAULT_URL, LOG_DEBUG_URL, LOG_ERROR_URL
// Have a look at config/app.php end check `env(...)` calls to find out all possible variables
