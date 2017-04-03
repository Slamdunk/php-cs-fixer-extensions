<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class FunctionReferenceSpaceFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (! $tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, array('('));
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);

            $previous = null;
            for ($iter = $startParenthesisIndex - 1; $iter < $endParenthesisIndex; ++$iter) {
                $token = $tokens[$iter];

                if ($previous) {
                    if ($token->isWhitespace()) {
                        $token->setContent(' ');
                    } else {
                        $tokens->insertAt($iter, new Token(array(T_WHITESPACE, ' ')));
                        ++$endParenthesisIndex;
                    }

                    $previous = null;
                    continue;
                }
                if ($token->equals('&')) {
                    $previous = $token;
                    continue;
                }
            }
        }
    }

    protected function getDescription()
    {
        return 'Ensure space between & and variable name';
    }
}
