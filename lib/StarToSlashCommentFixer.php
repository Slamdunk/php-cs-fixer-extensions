<?php

namespace SlamCsFixer;

use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class StarToSlashCommentFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'Converts multi-line comments that have only one line of actual content into single-line comments.',
            [new CodeSample("<?php\n/* first comment */\n\$a = 1;\n/*\n * second comment\n */\n\$b = 2;\n/*\n * third\n * comment\n */\n\$c = 3;")]
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            if (!$token->isGivenKind(T_COMMENT) || '/*' !== substr($content, 0, 2) || preg_match('/[^\s\*].*\R.*[^\s\*]/s', substr($content, 2, -2))) {
                continue;
            }
            $nextTokenIndex = $index + 1;
            if (isset($tokens[$nextTokenIndex])) {
                $nextToken = $tokens[$nextTokenIndex];
                if (false === strpos($nextToken->getContent(), $lineEnding)) {
                    continue;
                }

                $tokens[$nextTokenIndex] = new Token([$nextToken->getId(), ltrim($nextToken->getContent(), " \t")]);
            }

            $content = '// '.preg_replace('/[\s\*]*([^\s\*].+[^\s\*])[\s\*]*/', '\1', substr($content, 2, -2));
            $tokens[$index] = new Token([$token->getId(), $content]);
        }
    }
}
