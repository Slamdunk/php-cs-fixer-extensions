<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class FinalInternalClassFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'All internal classes should be final except abstract ones.',
            array(
                new CodeSample('<?php class MyApp {}' . PHP_EOL),
            ),
            null,
            'Risky when subclassing non-abstract classes.'
        );
    }

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
            if ($docToken->isGivenKind(T_DOC_COMMENT) && false !== mb_strpos($docToken->getContent(), '@ORM\Entity')) {
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
}
