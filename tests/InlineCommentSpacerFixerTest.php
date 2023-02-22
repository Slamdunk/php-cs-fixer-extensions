<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SlamCsFixer\InlineCommentSpacerFixer;

#[CoversClass(InlineCommentSpacerFixer::class)]
final class InlineCommentSpacerFixerTest extends AbstractFixerTestCase
{
    public function testDefinition(): void
    {
        self::assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
    }

    #[DataProvider('provideCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public static function provideCases(): array
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
