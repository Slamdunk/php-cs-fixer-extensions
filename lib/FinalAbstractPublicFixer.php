<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class FinalAbstractPublicFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'All public methods of abstract classes should be final.',
            [
                new CodeSample(
                    <<<'EOT'
<?php

abstract class AbstractMachine
{
    public function start()
    {}
}

EOT
                ),
            ],
            null,
            'Risky when overriding public methods of abstract classes.'
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_CLASS);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $classes = \array_keys($tokens->findGivenKind(\T_CLASS));

        while ($classIndex = \array_pop($classes)) {
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($classIndex)];
            if (! $prevToken->isGivenKind([\T_ABSTRACT])) {
                continue;
            }

            $classOpen  = $tokens->getNextTokenOfKind($classIndex, ['{']);
            $classClose = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);

            $this->fixClass($tokens, $classOpen, $classClose);
        }
    }

    private function fixClass(Tokens $tokens, int $classOpenIndex, int $classCloseIndex): void
    {
        for ($index = $classCloseIndex - 1; $index > $classOpenIndex; --$index) {
            if ($tokens[$index]->equals('}')) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }
            if (! $tokens[$index]->isGivenKind(\T_PUBLIC)) {
                continue;
            }
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            $nextToken = $tokens[$nextIndex];
            if ($nextToken->isGivenKind(\T_STATIC)) {
                $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
                $nextToken = $tokens[$nextIndex];
            }
            if (! $nextToken->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
            $nextToken = $tokens[$nextIndex];
            if (! $nextToken->isGivenKind(\T_STRING) || 0 === \mb_strpos($nextToken->getContent(), '__')) {
                continue;
            }
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            if ($prevToken->isGivenKind([\T_FINAL, \T_ABSTRACT])) {
                continue;
            }

            $tokens->insertAt(
                $index,
                [
                    new Token([\T_FINAL, 'final']),
                    new Token([\T_WHITESPACE, ' ']),
                ]
            );
        }
    }
}
