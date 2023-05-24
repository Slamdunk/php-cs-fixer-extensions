<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSets;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SlamCsFixer\AbstractFixer;
use SlamCsFixer\Config;

#[CoversClass(AbstractFixer::class)]
#[CoversClass(Config::class)]
final class ConfigTest extends TestCase
{
    public function testConfig(): void
    {
        $config = new Config();

        self::assertInstanceOf(ConfigInterface::class, $config);
        self::assertNotEmpty($config->getCustomFixers());
    }

    public function testAllRulesAreSpecifiedAndDifferentFromRuleSets(): void
    {
        $config      = new Config();

        $configRules           = $config->getRules();
        $ruleSet               = new RuleSet($configRules);
        $rules                 = $ruleSet->getRules();
        $defaultSetDefinitions = [];
        // RuleSet strips all disabled rules
        foreach ($configRules as $name => $value) {
            if ('@' === $name[0]) {
                $defaultSetDefinitions[$name] = (new RuleSet(RuleSets::getSetDefinition($name)->getRules()))->getRules();

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

        $availableRules = \array_filter($fixers, static fn (FixerInterface $fixer): bool => ! $fixer instanceof DeprecatedFixerInterface);
        $availableRules = \array_map(fn (FixerInterface $fixer): string => $fixer->getName(), $availableRules);
        \sort($availableRules);

        /*
        $diff = \array_diff($availableRules, $currentRules);
        self::assertEmpty($diff, \sprintf("The following fixers are missing:\n- %s", \implode(\PHP_EOL . '- ', $diff)));
         */

        $diff = \array_diff($currentRules, $availableRules);
        self::assertEmpty($diff, \sprintf("The following fixers do not exist:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

        $alreadyDefinedRules = [];
        foreach (Config::RULES as $ruleName => $ruleConfig) {
            foreach ($defaultSetDefinitions as $setName => $rules) {
                if (isset($rules[$ruleName]) && $ruleConfig === $rules[$ruleName] && false !== $ruleConfig) {
                    $alreadyDefinedRules[$ruleName] = $setName;
                }
            }
        }
        self::assertSame([], $alreadyDefinedRules, 'These rules are already defined in the respective set');

        /*
        $currentSets = \array_values(\array_filter(\array_keys($configRules), static function (string $fixerName): bool {
            return isset($fixerName[0]) && '@' === $fixerName[0];
        }));
        $defaultSets   = RuleSets::getSetDefinitionNames();
        $intersectSets = \array_values(\array_intersect($defaultSets, $currentSets));
        self::assertEquals($intersectSets, $currentSets, \sprintf('Rule sets must be ordered as the appear in %s', RuleSet::class));
         */

        $currentRules = \array_values(\array_filter(\array_keys($configRules), static fn (string $fixerName): bool => isset($fixerName[0]) && '@' !== $fixerName[0]));

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

    public function testOverwrite(): void
    {
        $rules = (new Config())->getRules();
        $rule  = 'global_namespace_import';
        self::assertTrue($rules[$rule]);

        $newRules = (new Config([
            $rule => false,
        ]))->getRules();
        self::assertFalse($newRules[$rule]);
    }
}
