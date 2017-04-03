<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

final class InlineCommentSpacerFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php // test',
                '<?php //test',
            ),
            array(
                '<?php //  test',
            ),
            array(
                '<?php /*test*/',
            ),
        );
    }
}
