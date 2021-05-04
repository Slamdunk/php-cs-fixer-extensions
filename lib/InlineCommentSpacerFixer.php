<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class InlineCommentSpacerFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Puts a space after every inline comment start.',
            [
                new CodeSample('<?php //Whut' . \PHP_EOL),
            ]
        );
    }

    public function getPriority(): int
    {
        return 30;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            if (! $token->isComment() || '//' !== mb_substr($content, 0, 2) || '// ' === mb_substr($content, 0, 3)) {
                continue;
            }

            $content        = substr_replace($content, ' ', 2, 0);
            $tokens[$index] = new Token([$token->getId(), $content]);
        }
    }
}
