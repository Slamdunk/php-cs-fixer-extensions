<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\InlineCommentSpacerFixer
 */
final class InlineCommentSpacerFixerTest extends AbstractFixerTestCase
{
    public function testDefinition()
    {
        static::assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
    }

    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return [
            [
                '<?php // test',
                '<?php //test',
            ],
            [
                '<?php //  test',
            ],
            [
                '<?php /*test*/',
            ],
        ];
    }
}
