<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\TestCase;
use SlamCsFixer\PhpFileOnlyProxyFixer;
use SplFileInfo;

/**
 * @covers \SlamCsFixer\PhpFileOnlyProxyFixer
 */
final class PhpFileOnlyProxyFixerTest extends TestCase
{
    public function testFixerInterfaceProxy(): void
    {
        $fixer = $this->createMock(FixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $tokens = $this->createMock(Tokens::class);
        $fixer
            ->expects(self::once())
            ->method('isCandidate')
            ->with(self::identicalTo($tokens))
            ->willReturn($candidate = (bool) random_int(0, 1))
        ;
        self::assertSame($candidate, $proxy->isCandidate($tokens));

        $fixer
            ->expects(self::once())
            ->method('isRisky')
            ->willReturn($risky = (bool) random_int(0, 1))
        ;
        self::assertSame($risky, $proxy->isRisky());

        $file = new SplFileInfo(__FILE__);
        $fixer
            ->expects(self::once())
            ->method('fix')
            ->with(
                self::identicalTo($file),
                self::identicalTo($tokens)
            )
        ;
        $proxy->fix($file, $tokens);

        $fixer
            ->expects(self::once())
            ->method('getName')
            ->willReturn($name = uniqid('_name'))
        ;
        $proxyName = $proxy->getName();
        self::assertStringContainsString('Slam', $proxyName);
        self::assertStringContainsString($name, $proxyName);

        $fixer
            ->expects(self::once())
            ->method('getPriority')
            ->willReturn($priority = random_int(-100, 100))
        ;
        self::assertSame($priority, $proxy->getPriority());

        self::assertTrue($proxy->supports($file));
        self::assertFalse($proxy->supports(new SplFileInfo(__DIR__ . '/_files/non-php.txt')));

        $proxy->configure([]);
        self::assertEmpty($proxy->getConfigurationDefinition()->getOptions());
        $proxy->setWhitespacesConfig(new WhitespacesFixerConfig());
        self::assertEmpty($proxy->getDefinition()->getCodeSamples());
    }

    public function testGetDefinitionIsProxied(): void
    {
        $fixer = $this->createMock(FixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixerDefinition = $this->createMock(FixerDefinitionInterface::class);
        $fixerDefinition->expects(self::once())->method('getSummary')->willReturn($summary = uniqid('summary'));
        $fixerDefinition->expects(self::once())->method('getCodeSamples')->willReturn($codeSamples = []);
        $fixerDefinition->expects(self::once())->method('getDescription')->willReturn($description = uniqid('description'));
        $fixerDefinition->expects(self::once())->method('getRiskyDescription')->willReturn($riskyDescription = uniqid('riskyDescription'));

        $fixer
            ->expects(self::once())
            ->method('getDefinition')
            ->willReturn($fixerDefinition)
        ;

        $definition = $proxy->getDefinition();

        self::assertStringContainsString($summary, $definition->getSummary());
        self::assertStringContainsString('PHP', $definition->getSummary());
        self::assertSame($codeSamples, $definition->getCodeSamples());
        self::assertSame($description, $definition->getDescription());
        self::assertSame($riskyDescription, $definition->getRiskyDescription());
    }

    public function testConfigureIsProxied(): void
    {
        $fixer         = $this->createMock(ConfigurableFixerInterface::class);
        $configuration = [uniqid()];

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixer
            ->expects(self::once())
            ->method('configure')
            ->with(self::identicalTo($configuration))
        ;

        $proxy->configure($configuration);
    }

    public function testGetConfigurationDefinitionIsProxied(): void
    {
        $fixer = $this->createMock(ConfigurableFixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $definition = $this->createMock(FixerConfigurationResolverInterface::class);
        $fixer
            ->expects(self::once())
            ->method('getConfigurationDefinition')
            ->willReturn($definition)
        ;

        self::assertSame($definition, $proxy->getConfigurationDefinition());
    }

    public function testSetWhitespacesConfigIsProxied(): void
    {
        $fixer  = $this->createMock(WhitespacesAwareFixerInterface::class);
        $config = new WhitespacesFixerConfig();

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixer
            ->expects(self::once())
            ->method('setWhitespacesConfig')
            ->with(self::identicalTo($config))
        ;

        $proxy->setWhitespacesConfig($config);
    }
}
