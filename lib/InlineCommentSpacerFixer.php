<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class InlineCommentSpacerFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $token) {
            $content = $token->getContent();
            if (! $token->isComment() or mb_substr($content, 0, 2) !== '//' or mb_substr($content, 0, 3) === '// ') {
                continue;
            }

            $content = substr_replace($content, ' ', 2, 0);
            $token->setContent($content);
        }
    }

    public function getPriority()
    {
        return 30;
    }

    public function supports(\SplFileInfo $file)
    {
        return pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php';
    }

    public function getDefinition()
    {
        return new FixerDefinition('Puts a space after every inline comment start');
    }
}
