<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Tokenizer\Tokens;

final class Utf8Fixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $content = $tokens->generateCode();
        if (mb_check_encoding($content, 'UTF-8') === false) {
            $tokens->setCode(mb_convert_encoding($content, 'UTF-8', 'Windows-1252'));
        }
    }

    public function getPriority()
    {
        return 99;
    }

    protected function getDescription()
    {
        return 'Force files to be UTF8 without BOM';
    }
}
