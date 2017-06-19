<?php

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class AlignMultilineCommentFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'Multiline comments and docblocks MUST be aligned with comment opening.',
            [new CodeSample('<?php
    /*
     * Multiline comment
  *
 Lines not prefixed with asterisk are left untouched
       *
   */')]
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_COMMENT, T_DOC_COMMENT]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $intendationChars = 0;
        foreach ($tokens as $index => $token) {
            $newlinePosition = mb_strrpos($token->getContent(), "\n");

            if ($token->isComment() && '/*' === substr($token->getContent(), 0, 2) && false !== $newlinePosition) {
                $content = $token->getContent();
                $content = preg_replace('/^[ \t]*\*/m', str_repeat(' ', $intendationChars).'*', $content);
                $content = ltrim($content, ' ');
                $tokens[$index]->setContent($content);
                continue;
            }

            if (false !== $newlinePosition) {
                $intendationChars = mb_strlen($token->getContent()) - $newlinePosition;
                continue;
            }

            $intendationChars += mb_strlen($token->getContent());
        }
    }
}
