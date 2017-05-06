<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class FinalInternalClassFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLASS);
    }

    public function isRisky()
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $classes = array_keys($tokens->findGivenKind(T_CLASS));

        while ($classIndex = array_pop($classes)) {
            // ignore class if it is abstract or already final
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($classIndex)];
            if ($prevToken->isGivenKind(array(T_ABSTRACT, T_FINAL, T_NEW))) {
                continue;
            }

            // ignore class if it's a Doctrine Entity
            $docToken = $tokens[$tokens->getPrevNonWhitespace($classIndex)];
            if ($docToken->isGivenKind(T_DOC_COMMENT) and mb_strpos($docToken->getContent(), '@ORM\Entity') !== false) {
                continue;
            }

            $tokens->insertAt(
                $classIndex,
                array(
                    new Token(array(T_FINAL, 'final')),
                    new Token(array(T_WHITESPACE, ' ')),
                )
            );
        }
    }

    public function getDefinition()
    {
        return new FixerDefinition('All internal classes should be final except abstract ones', array());
    }
}
