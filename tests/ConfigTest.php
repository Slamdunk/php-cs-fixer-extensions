<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
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

        static::assertInstanceOf(ConfigInterface::class, $config);
        static::assertNotEmpty($config->getCustomFixers());
    }

    public function testAllDefaultRulesAreSpecified()
    {
        $config      = new Config();
        $configRules = $config->getRules();
        $ruleSet     = new RuleSet($configRules);
        $rules       = $ruleSet->getRules();
        // RuleSet strips all disabled rules
        foreach ($configRules as $name => $value) {
            if ('@' === $name[0]) {
                continue;
            }
            $rules[$name] = $value;
        }

        $currentRules = \array_keys($rules);
        \sort($currentRules);

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixerFactory->registerCustomFixers($config->getCustomFixers());
        $fixers = $fixerFactory->getFixers();

        $availableRules = \array_filter($fixers, function (FixerInterface $fixer) {
            return ! $fixer instanceof DeprecatedFixerInterface;
        });
        $availableRules = \array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $availableRules);
        \sort($availableRules);

        $diff = \array_diff($availableRules, $currentRules);
        static::assertEmpty($diff, \sprintf("Mancano tra le specifiche i seguenti fixer:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

        $diff = \array_diff($currentRules, $availableRules);
        static::assertEmpty($diff, \sprintf("I seguenti fixer sono di troppo:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

        $currentRules        = \array_keys($configRules);
        $orderedCurrentRules = $currentRules;
        \sort($orderedCurrentRules);
        static::assertEquals($orderedCurrentRules, $currentRules, 'Order the rules alphabetically please');
    }

    public function testFutureMode()
    {
        \putenv('PHP_CS_FIXER_FUTURE_MODE');

        static::assertFalse(\getenv('PHP_CS_FIXER_FUTURE_MODE'));

        new Config();

        static::assertNotEmpty(\getenv('PHP_CS_FIXER_FUTURE_MODE'));
    }

    public function testTypes()
    {
        $rules = (new Config(Config::APP_V1))->getRules();
        static::assertFalse($rules['declare_strict_types']);
        static::assertFalse($rules['native_constant_invocation']);

        $rules = (new Config(Config::APP_V2))->getRules();
        static::assertTrue($rules['declare_strict_types']);
        static::assertFalse($rules['native_constant_invocation']);

        $rules = (new Config(Config::LIB))->getRules();
        static::assertTrue($rules['native_function_invocation']);
        static::assertTrue($rules['native_constant_invocation']);

        static::assertSame((new Config())->getRules(), (new Config(Config::APP_V2))->getRules());
    }

    public function testOverwrite()
    {
        $rules = (new Config(Config::APP_V2))->getRules();
        static::assertTrue($rules['declare_strict_types']);
        static::assertFalse($rules['native_constant_invocation']);

        $overriddenRules = [
            'declare_strict_types'            => false,
            'Slam/native_constant_invocation' => true,
        ];

        $newRules = (new Config(Config::APP_V2, $overriddenRules))->getRules();
        static::assertFalse($newRules['declare_strict_types']);
        static::assertTrue($newRules['Slam/native_constant_invocation']);
    }
}
