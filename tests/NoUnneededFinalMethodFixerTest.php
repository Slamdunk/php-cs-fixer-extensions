<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

final class NoUnneededFinalMethodFixerTest extends AbstractFixerTestCase
{
    public function testDefinition()
    {
        $this->assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
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
            'default' => array(
                '<?php
final class Foo {
    public function foo() {}
    protected function bar() {}
    private function baz() {}
}',
                '<?php
final class Foo {
    final public function foo() {}
    final protected function bar() {}
    final private function baz() {}
}',
            ),
            'preserve-comment' => array(
                '<?php final class Foo { /* comment */public function foo() {} }',
                '<?php final class Foo { final/* comment */public function foo() {} }',
            ),
            'multiple-classes-per-file' => array(
                '<?php final class Foo { public function foo() {} } abstract class Bar { final public function bar() {} }',
                '<?php final class Foo { final public function foo() {} } abstract class Bar { final public function bar() {} }',
            ),

            'non-final' => array(
                '<php class Foo { final public function foo() {} }',
            ),
            'abstract-class' => array(
                '<php abstract class Foo { final public function foo() {} }',
            ),
            'trait' => array(
                '<php trait Foo { final public function foo() {} }',
            ),

            'anonymous-class-inside' => array(
                '<?php
final class Foo
{
    public function foo()
    {
    }

    private function bar()
    {
        new class {
            final public function baz()
            {
            }
        };
    }
}
',
                '<?php
final class Foo
{
    final public function foo()
    {
    }

    private function bar()
    {
        new class {
            final public function baz()
            {
            }
        };
    }
}
',
            ),
        );
    }
}
