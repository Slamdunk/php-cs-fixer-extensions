<?php

$config = new SlamCsFixer\Config();
$config->getFinder()
    ->notPath('_files/utf8-ansi.php')
    ->in(__DIR__ . '/lib')
    ->in(__DIR__ . '/tests')
;
$rules = $config->getRules();
$rules['@PHP71Migration'] = false;
$rules['@PHP71Migration:risky'] = false;
$config->setRules($rules);

return $config;
