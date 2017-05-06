<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

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
        return array(
            array(
                '<?php final class MyClass {}',
                '<?php class MyClass {}',
            ),
            array(
                '<?php final class MyClass extends MyAbstract {}',
                '<?php class MyClass extends MyAbstract {}',
            ),
            array(
                '<?php final class MyClass implements MyInterface {}',
                '<?php class MyClass implements MyInterface {}',
            ),
            array(
                "<?php\n/**\n * @codeCoverageIgnore\n */\nfinal class MyEntity {}",
                "<?php\n/**\n * @codeCoverageIgnore\n */\nclass MyEntity {}",
            ),
            array(
                "<?php\n/**\n * @ORM\Entity\n */\nclass MyEntity {}",
            ),
            array(
                '<?php abstract class MyAbstract {}',
            ),
            array(
                '<?php trait MyTrait {}',
            ),
            array(
                '<?php $anonymClass = new class {};',
            ),
        );
    }
}
