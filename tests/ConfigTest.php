<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PHPUnit\Framework\TestCase;
use SlamCsFixer\Config;

/**
 * @covers \SlamCsFixer\AbstractFixer
 * @covers \SlamCsFixer\Config
 */
final class ConfigTest extends TestCase
{
    public function testConfig()
    {
        $config = new Config();

        $this->assertInstanceOf(ConfigInterface::class, $config);
        $this->assertNotEmpty($config->getCustomFixers());
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

        $currentRules = \array_keys($rules);

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixerFactory->registerCustomFixers($config->getCustomFixers());
        $fixers = $fixerFactory->getFixers();

        $availableRules = \array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $fixers);
        \sort($availableRules);

        $diff = \array_diff($availableRules, $currentRules);
        $this->assertEmpty($diff, \sprintf("Mancano tra le specifiche i seguenti fixer:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

        $currentRules = \array_keys($configRules);
        $orderedCurrentRules = $currentRules;
        \sort($orderedCurrentRules);
        $this->assertEquals($orderedCurrentRules, $currentRules, 'Order the rules alphabetically please');
    }

    public function testFutureMode()
    {
        \putenv('PHP_CS_FIXER_FUTURE_MODE');

        $this->assertFalse(\getenv('PHP_CS_FIXER_FUTURE_MODE'));

        $config = new Config();

        $this->assertNotEmpty(\getenv('PHP_CS_FIXER_FUTURE_MODE'));
    }

    public function testTypes()
    {
        $rules = (new Config(Config::APP_V1))->getRules();
        $this->assertFalse($rules['declare_strict_types']);
        $this->assertFalse($rules['Slam/native_constant_invocation']);

        $rules = (new Config(Config::APP_V2))->getRules();
        $this->assertTrue($rules['declare_strict_types']);
        $this->assertFalse($rules['Slam/native_constant_invocation']);

        $rules = (new Config(Config::LIB))->getRules();
        $this->assertTrue($rules['native_function_invocation']);
        $this->assertTrue($rules['Slam/native_constant_invocation']);

        $this->assertSame((new Config())->getRules(), (new Config(Config::APP_V2))->getRules());
    }

    public function testOverwrite()
    {
        $rules = (new Config(Config::APP_V2))->getRules();
        $this->assertTrue($rules['declare_strict_types']);
        $this->assertFalse($rules['Slam/native_constant_invocation']);

        $overriddenRules = [
            'declare_strict_types' => false,
            'Slam/native_constant_invocation' => true,
        ];

        $newRules = (new Config(Config::APP_V2, $overriddenRules))->getRules();
        $this->assertFalse($newRules['declare_strict_types']);
        $this->assertTrue($newRules['Slam/native_constant_invocation']);
    }
}
