<% $namespace = str_replace('\\', '\\\\', $namespace); %>
{
    "name": "<%= $package %>",
    "description": "<%= $plugin %> plugin for BEdita4",
    "type": "cakephp-plugin",
    "require": {
        "php": ">=5.6.0",
        "cakephp/cakephp": "~3.4.4"
    },
    "require-dev": {
        "phpunit/phpunit": "^5.7|^6.0"
    },
    "autoload": {
        "psr-4": {
            "<%= $namespace %>\\": "src"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "<%= $namespace %>\\Test\\": "tests",
            "Cake\\Test\\": "./vendor/cakephp/cakephp/tests"
        }
    }
}
