# php-cs-fixer-extensions

[![Build Status](https://travis-ci.org/Slamdunk/php-cs-fixer-extensions.svg?branch=master)](https://travis-ci.org/Slamdunk/php-cs-fixer-extensions)

PHP-CS-Fixer extensions and configurations

## Usage

Execute:

`composer require --dev slam/php-cs-fixer-extensions`

And then, in your `.php_cs` file:

```php
<?php

$config = new PhpCsFixer\Config();

$config->setRiskyAllowed(true);

$config->registerCustomFixers(array(
    new SlamCsFixer\FinalAbstractPublicFixer(),
    new SlamCsFixer\FinalInternalClassFixer(),
    new SlamCsFixer\FunctionReferenceSpaceFixer(),
    new SlamCsFixer\InlineCommentSpacerFixer(),
    new SlamCsFixer\PhpFileOnlyProxyFixer(new PhpCsFixer\Fixer\Basic\BracesFixer()),
    new SlamCsFixer\Utf8Fixer(),
));

$this->setRules(array(
    'Slam/final_abstract_public' => true,
    'Slam/final_internal_class' => true,
    'Slam/function_reference_space' => true,
    'Slam/inline_comment_spacer' => true,
    'Slam/php_only_braces' => true,
    'Slam/utf8' => true,
));

$config->getFinder()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/tests')
    ->name('*.phtml')
;

return $config;

```
