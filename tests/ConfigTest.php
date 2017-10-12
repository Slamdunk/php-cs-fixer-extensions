<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
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
        $configRules = $config->getRules();
        $ruleSet = new RuleSet($configRules);
        $rules = $ruleSet->getRules();
        // RuleSet strips all disabled rules
        foreach ($configRules as $name => $value) {
            if ('@' === $name[0]) {
                continue;
            }
            $rules[$name] = $value;
        }

        $currentRules = array_keys($rules);

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

        $currentRules = array_keys($configRules);
        $orderedCurrentRules = $currentRules;
        sort($orderedCurrentRules);
        $this->assertEquals($orderedCurrentRules, $currentRules, 'Order the rules alphabetically please');
    }
}
