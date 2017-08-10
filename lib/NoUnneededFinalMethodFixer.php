<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class NoUnneededFinalMethodFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'A final class must not have final methods.',
            array(
                new CodeSample('<?php
final class Foo {
    final public function foo() {}
    final protected function bar() {}
    final private function baz() {}
}'),
            )
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(array(T_CLASS, T_FINAL));
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $end = count($tokens) - 3; // min. number of tokens to form a class candidate to fix
        for ($index = 0; $index < $end; ++$index) {
            if (! $tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $classOpen = $tokens->getNextTokenOfKind($index, array('{'));
            $classClose = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            if ($prevToken->isGivenKind(T_FINAL)) {
                $this->fixClass($tokens, $classOpen, $classClose);
            }

            $index = $classClose;
        }
    }

    private function fixClass(Tokens $tokens, $classOpenIndex, $classCloseIndex)
    {
        for ($index = $classOpenIndex + 1; $index < $classCloseIndex; ++$index) {
            if ($tokens[$index]->equals('{')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if (! $tokens[$index]->isGivenKind(T_FINAL)) {
                continue;
            }

            $tokens->clearAt($index);

            $nextTokenIndex = $index + 1;
            if ($tokens[$nextTokenIndex]->isWhitespace()) {
                $tokens->clearAt($nextTokenIndex);
            }
        }
    }
}
