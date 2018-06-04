<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
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
    public function testFixerInterfaceProxy()
    {
        $fixer = $this->createMock(FixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $tokens = $this->createMock(Tokens::class);
        $fixer
            ->expects(static::once())
            ->method('isCandidate')
            ->with(static::identicalTo($tokens))
            ->willReturn($candidate = (bool) \random_int(0, 1))
        ;
        static::assertSame($candidate, $proxy->isCandidate($tokens));

        $fixer
            ->expects(static::once())
            ->method('isRisky')
            ->willReturn($risky = (bool) \random_int(0, 1))
        ;
        static::assertSame($risky, $proxy->isRisky());

        $file = new SplFileInfo(__FILE__);
        $fixer
            ->expects(static::once())
            ->method('fix')
            ->with(
                static::identicalTo($file),
                static::identicalTo($tokens)
            )
        ;
        $proxy->fix($file, $tokens);

        $fixer
            ->expects(static::once())
            ->method('getName')
            ->willReturn($name = \uniqid('_name'))
        ;
        $proxyName = $proxy->getName();
        static::assertContains('Slam', $proxyName);
        static::assertContains($name, $proxyName);

        $fixer
            ->expects(static::once())
            ->method('getPriority')
            ->willReturn($priority = \random_int(-100, 100))
        ;
        static::assertSame($priority, $proxy->getPriority());

        static::assertTrue($proxy->supports($file));
        static::assertFalse($proxy->supports(new SplFileInfo(__DIR__ . '/_files/non-php.txt')));

        static::assertNull($proxy->configure([]));
        static::assertNull($proxy->getConfigurationDefinition());
        static::assertNull($proxy->setWhitespacesConfig(new WhitespacesFixerConfig()));
        static::assertNull($proxy->getDefinition());
    }

    public function testGetDefinitionIsProxied()
    {
        $fixer = $this->createMock(DefinedFixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixerDefinition = $this->createMock(FixerDefinitionInterface::class);
        $fixerDefinition->expects(static::once())->method('getSummary')->willReturn($summary = \uniqid('summary'));
        $fixerDefinition->expects(static::once())->method('getCodeSamples')->willReturn($codeSamples = []);
        $fixerDefinition->expects(static::once())->method('getDescription')->willReturn($description = \uniqid('description'));
        $fixerDefinition->expects(static::once())->method('getRiskyDescription')->willReturn($riskyDescription = \uniqid('riskyDescription'));

        $fixer
            ->expects(static::once())
            ->method('getDefinition')
            ->willReturn($fixerDefinition)
        ;

        $definition = $proxy->getDefinition();

        static::assertContains($summary, $definition->getSummary());
        static::assertContains('PHP', $definition->getSummary());
        static::assertSame($codeSamples, $definition->getCodeSamples());
        static::assertSame($description, $definition->getDescription());
        static::assertSame($riskyDescription, $definition->getRiskyDescription());
    }

    public function testConfigureIsProxied()
    {
        $fixer         = $this->createMock(ConfigurationDefinitionFixerInterface::class);
        $configuration = [\uniqid()];

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixer
            ->expects(static::once())
            ->method('configure')
            ->with(static::identicalTo($configuration))
        ;

        $proxy->configure($configuration);
    }

    public function testGetConfigurationDefinitionIsProxied()
    {
        $fixer = $this->createMock(ConfigurationDefinitionFixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $definition = $this->createMock(FixerConfigurationResolverInterface::class);
        $fixer
            ->expects(static::once())
            ->method('getConfigurationDefinition')
            ->willReturn($definition)
        ;

        static::assertSame($definition, $proxy->getConfigurationDefinition());
    }

    public function testSetWhitespacesConfigIsProxied()
    {
        $fixer  = $this->createMock(WhitespacesAwareFixerInterface::class);
        $config = new WhitespacesFixerConfig();

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixer
            ->expects(static::once())
            ->method('setWhitespacesConfig')
            ->with(static::identicalTo($config))
        ;

        $proxy->setWhitespacesConfig($config);
    }
}
