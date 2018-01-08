# Slam PHP-CS-Fixer extensions

[![Build Status](https://travis-ci.org/Slamdunk/php-cs-fixer-extensions.svg?branch=master)](https://travis-ci.org/Slamdunk/php-cs-fixer-extensions)
[![Code Coverage](https://scrutinizer-ci.com/g/Slamdunk/php-cs-fixer-extensions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Slamdunk/php-cs-fixer-extensions/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/slam/php-cs-fixer-extensions.svg)](https://packagist.org/packages/slam/php-cs-fixer-extensions)

[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) extensions and configurations

## Installation

Execute:

`composer require --dev slam/php-cs-fixer-extensions`

## Usage

In your `.php_cs` file:

```php
<?php

$config = new PhpCsFixer\Config();

$config->setRiskyAllowed(true);

$config->registerCustomFixers([
    new SlamCsFixer\FinalAbstractPublicFixer(),
    new SlamCsFixer\FinalInternalClassFixer(),
    new SlamCsFixer\FunctionReferenceSpaceFixer(),
    new SlamCsFixer\InlineCommentSpacerFixer(),
    new SlamCsFixer\PhpFileOnlyProxyFixer(new PhpCsFixer\Fixer\Basic\BracesFixer()),
    new SlamCsFixer\Utf8Fixer(),
]);

$this->setRules([
    'Slam/final_abstract_public' => true,
    'Slam/final_internal_class' => true,
    'Slam/function_reference_space' => true,
    'Slam/inline_comment_spacer' => true,
    'Slam/php_only_braces' => true,
    'Slam/utf8' => true,
]);

$config->getFinder()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/tests')
    ->name('*.phtml')
;

return $config;
```
