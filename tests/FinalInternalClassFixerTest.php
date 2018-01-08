<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\FinalInternalClassFixer
 */
final class FinalInternalClassFixerTest extends AbstractFixerTestCase
{
    public function testIsRisky()
    {
        $this->assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
        $this->assertTrue($this->fixer->isRisky());
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
