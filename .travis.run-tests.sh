#!/bin/sh
set -ev
if [ "$PHP_LATEST_VERSION" = 1 ]
then
    vendor/bin/phpunit --coverage-clover ./clover.xml
    phpenv config-rm xdebug.ini || return 0
    vendor/bin/php-cs-fixer --diff --dry-run --verbose fix
    wget https://scrutinizer-ci.com/ocular.phar || return 0
    php ocular.phar code-coverage:upload --format=php-clover ./clover.xml || return 0
else
    vendor/bin/phpunit
fi
