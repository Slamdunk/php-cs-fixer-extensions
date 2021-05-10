<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

/**
 * @covers \SlamCsFixer\Utf8Fixer
 */
final class Utf8FixerTest extends AbstractFixerTestCase
{
    public function testFix(): void
    {
        $expected = <<<'EOF'
1234567890
abcdefghijklmnopqrstuvwxyz
\|!"£$%&/()=<>,;.:-_òçà°ù§èé+*ì^@#[]{}
€
EOF;

        $this->doTest($expected, \file_get_contents(__DIR__ . '/_files/utf8-ansi.php'));
    }
}
