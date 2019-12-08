<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\FinalInternalClassFixer
 */
final class FinalInternalClassFixerTest extends AbstractFixerTestCase
{
    public function testIsRisky(): void
    {
        self::assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
        self::assertTrue($this->fixer->isRisky());
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
                '<?php final class MyClass {}',
                '<?php class MyClass {}',
            ],
            [
                '<?php final class MyClass extends MyAbstract {}',
                '<?php class MyClass extends MyAbstract {}',
            ],
            [
                '<?php final class MyClass implements MyInterface {}',
                '<?php class MyClass implements MyInterface {}',
            ],
            [
                "<?php\n/**\n * @codeCoverageIgnore\n */\nfinal class MyEntity {}",
                "<?php\n/**\n * @codeCoverageIgnore\n */\nclass MyEntity {}",
            ],
            [
                "<?php\n/**\n * @ORM\\Entity\n */\nclass MyEntity {}",
            ],
            [
                '<?php abstract class MyAbstract {}',
            ],
            [
                '<?php trait MyTrait {}',
            ],
            [
                '<?php $anonymClass = new class {};',
            ],
        ];
    }
}
