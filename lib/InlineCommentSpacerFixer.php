<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class InlineCommentSpacerFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            if (! $token->isComment() || '//' !== mb_substr($content, 0, 2) || '// ' === mb_substr($content, 0, 3)) {
                continue;
            }

            $content = substr_replace($content, ' ', 2, 0);
            $tokens[$index] = new Token(array($token->getId(), $content));
        }
    }

    public function getPriority()
    {
        return 30;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getDefinition()
    {
        return new FixerDefinition('Puts a space after every inline comment start', array());
    }
}
