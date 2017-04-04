<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class Utf8Fixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    public function isRisky()
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

    public function getDefinition()
    {
        return new FixerDefinition('Force files to be UTF8 without BOM', array());
    }
}
