#!/bin/sh

set -ev

PHPUNIT_ARGS=""
if [ "$CODE_COVERAGE" = 1 ]
then
    PHPUNIT_ARGS="--coverage-clover ./clover.xml"
fi

vendor/bin/phpunit "$PHPUNIT_ARGS"
phpenv config-rm xdebug.ini || return 0

if [ "$CS_CHECK" = 1 ]
then
    vendor/bin/php-cs-fixer --diff --dry-run --verbose fix
fi

if [ "$CODE_COVERAGE" = 1 ]
then
    wget https://scrutinizer-ci.com/ocular.phar || return 0
    php ocular.phar code-coverage:upload --format=php-clover ./clover.xml || return 0
fi
