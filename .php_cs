<?php

$config = new SlamCsFixer\Config();
$config->getFinder()
    ->notPath('_files/utf8-ansi.php')
    ->in(__DIR__ . '/lib')
    ->in(__DIR__ . '/tests')
;

return $config;
