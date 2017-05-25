<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use SlamCsFixer\PhpFileOnlyProxyFixer;
use SplFileInfo;

final class PhpFileOnlyProxyFixerTest extends TestCase
{
    public function testProxy()
    {
        $fixer = $this->createMock(DefinedFixerInterface::class);

        $proxy = new PhpFileOnlyProxyFixer($fixer);

        $tokens = $this->createMock(Tokens::class);
        $fixer
            ->expects($this->once())
            ->method('isCandidate')
            ->with($this->identicalTo($tokens))
        ;
        $proxy->isCandidate($tokens);

        $fixer->expects($this->once())->method('isRisky');
        $proxy->isRisky();

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

        $this->assertTrue($proxy->supports($file));
        $this->assertFalse($proxy->supports(new SplFileInfo(__DIR__ . '/_files/non-php.txt')));

        $fixerDefinition = $this->createMock(FixerDefinitionInterface::class);

        $summary = uniqid('summary');
        $codeSamples = array();
        $description = uniqid('description');
        $riskyDescription = uniqid('riskyDescription');
        $fixerDefinition->expects($this->once())->method('getSummary')->willReturn($summary);
        $fixerDefinition->expects($this->once())->method('getCodeSamples')->willReturn($codeSamples);
        $fixerDefinition->expects($this->once())->method('getDescription')->willReturn($description);
        $fixerDefinition->expects($this->once())->method('getRiskyDescription')->willReturn($riskyDescription);

        $fixer->expects($this->once())->method('getDefinition')->willReturn($fixerDefinition);

        $definition = $proxy->getDefinition();

        $this->assertContains($summary, $definition->getSummary());
        $this->assertContains('php', $definition->getSummary());
        $this->assertSame($codeSamples, $definition->getCodeSamples());
        $this->assertSame($description, $definition->getDescription());
        $this->assertSame($riskyDescription, $definition->getRiskyDescription());
    }
}
