<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use SlamCsFixer\Config;

/**
 * @covers \SlamCsFixer\AbstractFixer
 * @covers \SlamCsFixer\Config
 */
final class ConfigTest extends TestCase
{
    /**
     * @var null|array
     */
    private $setDefinitions;

    public function testConfig(): void
    {
        $config = new Config();

        self::assertInstanceOf(ConfigInterface::class, $config);
        self::assertNotEmpty($config->getCustomFixers());
    }

    public function testAllRulesAreSpecifiedAndDifferentFromRuleSets(): void
    {
        $config      = new Config();
        /** @var array<string, mixed> $configRules */
        $configRules           = $config->getRules();
        $ruleSet               = new RuleSet($configRules);
        $rules                 = $ruleSet->getRules();
        $defaultSetDefinitions = [];
        // RuleSet strips all disabled rules
        foreach ($configRules as $name => $value) {
            if ('@' === $name[0]) {
                $defaultSetDefinitions[$name] = $this->resolveSubset($name);

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

        $availableRules = \array_filter($fixers, static function (FixerInterface $fixer): bool {
            return ! $fixer instanceof DeprecatedFixerInterface;
        });
        $availableRules = \array_map(function (FixerInterface $fixer): string {
            return $fixer->getName();
        }, $availableRules);
        \sort($availableRules);

        $diff = \array_diff($availableRules, $currentRules);
        self::assertEmpty($diff, \sprintf("The following fixers are missing:\n- %s", \implode(\PHP_EOL . '- ', $diff)));

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

    public function testOverwrite(): void
    {
        $rules = (new Config())->getRules();
        self::assertTrue($rules['psr0']);

        $newRules = (new Config([
            'psr0' => false,
        ]))->getRules();
        self::assertFalse($newRules['psr0']);
    }

    private function resolveSubset(string $setName): array
    {
        $rules = $this->getSetDefinition($setName);
        foreach ($rules as $name => $value) {
            if ('@' === $name[0]) {
                $set = $this->resolveSubset($name);
                unset($rules[$name]);
                $rules = \array_merge($rules, $set);
            } else {
                $rules[$name] = $value;
            }
        }

        return $rules;
    }

    private function getSetDefinition(string $name): array
    {
        if (null === $this->setDefinitions) {
            $refProp = (new ReflectionProperty(RuleSet::class, 'setDefinitions'));
            $refProp->setAccessible(true);
            $this->setDefinitions = $refProp->getValue(new RuleSet());
            $refProp->setAccessible(false);
        }

        return $this->setDefinitions[$name];
    }
}
