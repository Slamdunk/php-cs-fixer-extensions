<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\InlineCommentSpacerFixer
 */
final class InlineCommentSpacerFixerTest extends AbstractFixerTestCase
{
    public function testDefinition(): void
    {
        self::assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
    }

    /**
     * @dataProvider provideCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public function provideCases(): array
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
