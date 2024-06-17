<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class FinalInternalClassFixer extends AbstractFixer
{
    private const REGEX = '(?:final|\\\Doctrine\\\ORM\\\Mapping\\\Entity|ORM\\\Mapping\\\Entity|ORM\\\Entity|Entity)';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'All internal classes should be final except abstract ones.',
            [
                new CodeSample('<?php class MyApp {}' . \PHP_EOL),
            ],
            null,
            'Risky when subclassing non-abstract classes.'
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
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($classIndex);
            if (\defined('T_READONLY') && $tokens[$prevTokenIndex]->isGivenKind([\T_READONLY])) {
                $classIndex = $prevTokenIndex;
            }

            // ignore class if it is abstract or already final
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($classIndex)];
            if ($prevToken->isGivenKind([\T_ABSTRACT, \T_FINAL, \T_NEW])) {
                continue;
            }

            // ignore class if it's a Doctrine Entity
            if (self::isDoctrineEntity($tokens, $classIndex)) {
                continue;
            }

            $tokens->insertAt(
                $classIndex,
                [
                    new Token([\T_FINAL, 'final']),
                    new Token([\T_WHITESPACE, ' ']),
                ]
            );
        }
    }

    private static function isDoctrineEntity(Tokens $tokens, int $classIndex): bool
    {
        $docToken = $tokens[$tokens->getPrevNonWhitespace($classIndex)];
        if ($docToken->isGivenKind(\T_DOC_COMMENT) && 1 === \preg_match(\sprintf('/@%s/', self::REGEX), $docToken->getContent())) {
            return true;
        }

        while ($classIndex > 0 && $tokens[$tokens->getPrevNonWhitespace($classIndex)]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
            $attributeOpenIndex = $tokens->getPrevTokenOfKind($classIndex, [[\T_ATTRIBUTE]]);
            \assert(null !== $attributeOpenIndex);
            $content = '';
            for ($index = $attributeOpenIndex; $index < $classIndex; ++$index) {
                $content .= $tokens[$index]->getContent();
            }
            if (1 === \preg_match(\sprintf('/^#\[%s/', self::REGEX), $content)) {
                return true;
            }

            $classIndex = $attributeOpenIndex - 1;
        }

        return false;
    }
}
