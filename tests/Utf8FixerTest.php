<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\Utf8Fixer
 */
final class Utf8FixerTest extends AbstractFixerTestCase
{
    public function testIsRisky()
    {
        $this->assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
        $this->assertTrue($this->fixer->isRisky());
    }

    public function testFix()
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
