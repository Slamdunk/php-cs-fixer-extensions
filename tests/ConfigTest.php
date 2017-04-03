<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PHPUnit\Framework\TestCase;
use SlamCsFixer\Config;

final class ConfigTest extends TestCase
{
    public function testConfig()
    {
        $config = new Config();

        $this->assertInstanceOf(ConfigInterface::class, $config);
        $this->assertNotEmpty($config->getCustomFixers());

        $config1 = new Config(false);

        $this->assertNotSame($config->getRules(), $config1->getRules());
    }

    public function testAllDefaultRulesAreSpecified()
    {
        $config = new Config();
        $currentRules = array_keys($config->getRules());

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixerFactory->registerCustomFixers($config->getCustomFixers());
        $fixers = $fixerFactory->getFixers();

        $availableRules = array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $fixers);
        sort($availableRules);

        $diff = array_diff($availableRules, $currentRules);
        $this->assertEmpty($diff, sprintf("Mancano tra le specifiche i seguenti fixer:\n- %s", implode(PHP_EOL . '- ', $diff)));
    }
}
