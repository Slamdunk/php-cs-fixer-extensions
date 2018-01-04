<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class FunctionReferenceSpaceFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'Ensure space between & and variable name in function declarations and lambda uses.',
            array(
                new CodeSample('<?php $foo = function (&$bar) use (&  $baz) {};' . PHP_EOL),
            )
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (! $tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, array('('));
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $useIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
            if ($tokens[$useIndex]->isGivenKind(CT::T_USE_LAMBDA)) {
                $startUseIndex = $tokens->getNextTokenOfKind($useIndex, array('('));
                $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startUseIndex);
            }

            $previous = null;
            for ($iter = $endParenthesisIndex; $iter > $startParenthesisIndex; --$iter) {
                $token = $tokens[$iter];

                if (! $token->equals('&')) {
                    continue;
                }

                $nextTokenIndex = $iter + 1;
                $nextToken = $tokens[$nextTokenIndex];

                if ($nextToken->isWhitespace()) {
                    $tokens[$nextTokenIndex] = new Token(array($nextToken->getId(), ' '));
                } else {
                    $tokens->insertAt($nextTokenIndex, new Token(array(T_WHITESPACE, ' ')));
                }
            }
        }
    }
}
