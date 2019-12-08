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
    public function testConfig(): void
    {
        $config = new Config();

        self::assertInstanceOf(ConfigInterface::class, $config);
        self::assertNotEmpty($config->getCustomFixers());
    }

    public function testAllDefaultRulesAreSpecified(): void
    {
        $config      = new Config();
        /** @var array<string, mixed> $configRules */
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

        $availableRules = \array_filter($fixers, static function (FixerInterface $fixer) {
            return ! $fixer instanceof DeprecatedFixerInterface;
        });
        $availableRules = \array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $availableRules);
        \sort($availableRules);

        $diff = \array_diff($availableRules, $currentRules);
        self::assertEmpty($diff, \sprintf("Mancano tra le specifiche i seguenti fixer:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

        $diff = \array_diff($currentRules, $availableRules);
        self::assertEmpty($diff, \sprintf("I seguenti fixer sono di troppo:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

        $currentSets = \array_values(\array_filter(\array_keys($configRules), static function (string $fixerName): bool {
            return isset($fixerName[0]) && '@' === $fixerName[0];
        }));
        $defaultSets   = $ruleSet->getSetDefinitionNames();
        $intersectSets = \array_values(\array_intersect($defaultSets, $currentSets));
        self::assertEquals($intersectSets, $currentSets, \sprintf('Rule sets must be ordered as the appear in %s', RuleSet::class));

        $currentRules = \array_values(\array_filter(\array_keys($configRules), static function (string $fixerName): bool {
            return isset($fixerName[0]) && '@' !== $fixerName[0];
        }));

        $orderedCurrentRules = $currentRules;
        \sort($orderedCurrentRules);
        self::assertEquals($orderedCurrentRules, $currentRules, 'Order the rules alphabetically please');
    }

    public function testFutureMode(): void
    {
        \putenv('PHP_CS_FIXER_FUTURE_MODE');

        self::assertFalse(\getenv('PHP_CS_FIXER_FUTURE_MODE'));

        new Config();

        self::assertNotEmpty(\getenv('PHP_CS_FIXER_FUTURE_MODE'));
    }

    public function testTypes(): void
    {
        $rules = (new Config(Config::APP_V1))->getRules();
        self::assertFalse($rules['declare_strict_types']);
        self::assertFalse($rules['native_constant_invocation']);

        $rules = (new Config(Config::APP_V2))->getRules();
        self::assertTrue($rules['declare_strict_types']);
        self::assertFalse($rules['native_constant_invocation']);

        $rules = (new Config(Config::LIB))->getRules();
        self::assertTrue($rules['native_function_invocation']);
        self::assertTrue($rules['native_constant_invocation']);

        self::assertSame((new Config())->getRules(), (new Config(Config::APP_V2))->getRules());
    }

    public function testOverwrite(): void
    {
        $rules = (new Config(Config::APP_V2))->getRules();
        self::assertTrue($rules['declare_strict_types']);
        self::assertFalse($rules['native_constant_invocation']);

        $overriddenRules = [
            'declare_strict_types'            => false,
            'Slam/native_constant_invocation' => true,
        ];

        $newRules = (new Config(Config::APP_V2, $overriddenRules))->getRules();
        self::assertFalse($newRules['declare_strict_types']);
        self::assertTrue($newRules['Slam/native_constant_invocation']);
    }
}
