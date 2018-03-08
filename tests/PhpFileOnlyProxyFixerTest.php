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
            ->expects($this->once())
            ->method('isCandidate')
            ->with($this->identicalTo($tokens))
            ->willReturn($candidate = (bool) \random_int(0, 1))
        ;
        $this->assertSame($candidate, $proxy->isCandidate($tokens));

        $fixer
            ->expects($this->once())
            ->method('isRisky')
            ->willReturn($risky = (bool) \random_int(0, 1))
        ;
        $this->assertSame($risky, $proxy->isRisky());

        $file = new SplFileInfo(__FILE__);
        $fixer
            ->expects($this->once())
            ->method('fix')
            ->with(
                $this->identicalTo($file),
                $this->identicalTo($tokens)
            )
        ;
        $proxy->fix($file, $tokens);

        $fixer
            ->expects($this->once())
            ->method('getName')
            ->willReturn($name = \uniqid('_name'))
        ;
        $proxyName = $proxy->getName();
        $this->assertContains('Slam', $proxyName);
        $this->assertContains($name, $proxyName);

        $fixer
            ->expects($this->once())
            ->method('getPriority')
            ->willReturn($priority = \random_int(-100, 100))
        ;
        $this->assertSame($priority, $proxy->getPriority());

        $this->assertTrue($proxy->supports($file));
        $this->assertFalse($proxy->supports(new SplFileInfo(__DIR__ . '/_files/non-php.txt')));

        $this->assertNull($proxy->configure([]));
        $this->assertNull($proxy->getConfigurationDefinition());
        $this->assertNull($proxy->setWhitespacesConfig(new WhitespacesFixerConfig()));
        $this->assertNull($proxy->getDefinition());
    }

    public function testGetDefinitionIsProxied()
    {
        $fixer = $this->createMock(DefinedFixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixerDefinition = $this->createMock(FixerDefinitionInterface::class);
        $fixerDefinition->expects($this->once())->method('getSummary')->willReturn($summary = \uniqid('summary'));
        $fixerDefinition->expects($this->once())->method('getCodeSamples')->willReturn($codeSamples = []);
        $fixerDefinition->expects($this->once())->method('getDescription')->willReturn($description = \uniqid('description'));
        $fixerDefinition->expects($this->once())->method('getRiskyDescription')->willReturn($riskyDescription = \uniqid('riskyDescription'));

        $fixer
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($fixerDefinition)
        ;

        $definition = $proxy->getDefinition();

        $this->assertContains($summary, $definition->getSummary());
        $this->assertContains('PHP', $definition->getSummary());
        $this->assertSame($codeSamples, $definition->getCodeSamples());
        $this->assertSame($description, $definition->getDescription());
        $this->assertSame($riskyDescription, $definition->getRiskyDescription());
    }

    public function testConfigureIsProxied()
    {
        $fixer         = $this->createMock(ConfigurationDefinitionFixerInterface::class);
        $configuration = [\uniqid()];

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixer
            ->expects($this->once())
            ->method('configure')
            ->with($this->identicalTo($configuration))
        ;

        $proxy->configure($configuration);
    }

    public function testGetConfigurationDefinitionIsProxied()
    {
        $fixer = $this->createMock(ConfigurationDefinitionFixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $definition = $this->createMock(FixerConfigurationResolverInterface::class);
        $fixer
            ->expects($this->once())
            ->method('getConfigurationDefinition')
            ->willReturn($definition)
        ;

        $this->assertSame($definition, $proxy->getConfigurationDefinition());
    }

    public function testSetWhitespacesConfigIsProxied()
    {
        $fixer  = $this->createMock(WhitespacesAwareFixerInterface::class);
        $config = new WhitespacesFixerConfig();

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $fixer
            ->expects($this->once())
            ->method('setWhitespacesConfig')
            ->with($this->identicalTo($config))
        ;

        $proxy->setWhitespacesConfig($config);
    }
}
