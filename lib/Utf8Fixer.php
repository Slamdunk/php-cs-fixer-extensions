<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class Utf8Fixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Converts files from Windows-1252 to UTF8.',
            [
                new CodeSample(mb_convert_encoding('<?php return \'è\';' . \PHP_EOL, 'Windows-1252', 'UTF-8')),
            ],
            null,
            'Risky when files are encoded different from UTF-8 and Windows-1252.'
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $content = $tokens->generateCode();
        if (false === mb_check_encoding($content, 'UTF-8')) {
            $tokens->setCode(mb_convert_encoding($content, 'UTF-8', 'Windows-1252'));
        }
    }

    public function getPriority(): int
    {
        // Should run after encoding
        return 99;
    }
}
