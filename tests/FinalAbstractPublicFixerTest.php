<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\FinalAbstractPublicFixer
 */
final class FinalAbstractPublicFixerTest extends AbstractFixerTestCase
{
    public function testIsRisky()
    {
        static::assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
        static::assertTrue($this->fixer->isRisky());
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
        $original = '
public $a1;
protected $a2;
private $a3;
public static $a4;
protected static $a5;
private static $a6;
public function f1(){}
protected function f2(){}
private function f3(){}
public static function f4(){}
protected static function f5(){}
private static function f6(){}
';
        $fixed = $original;
        $fixed = \str_replace('public static function', 'final public static function', $fixed);
        $fixed = \str_replace('public function', 'final public function', $fixed);

        return [
            'final-class'     => ["<?php final class MyClass { ${original} }"],
            'trait'           => ["<?php trait MyClass { ${original} }"],
            'interface'       => ["<?php interface MyClass { ${original} }"],
            'anonymous-class' => ["<?php abstract class MyClass { private function test() { \$a = new class { ${original} }; } }"],
            'magic-methods'   => ['<?php abstract class MyClass {
public function __construct() {}
public function __destruct() {}
public function __call() {}
public function __callStatic() {}
public function __get() {}
public function __set() {}
public function __isset() {}
public function __unset() {}
public function __sleep() {}
public function __wakeup() {}
public function __toString() {}
public function __invoke() {}
public function __set_state() {}
public function __clone() {}
public function __debugInfo() {}
            }'],
            'abstract-methods'   => ['<?php abstract class MyClass {
abstract public function foo();
abstract protected function foo();
            }'],
            'abstract-class' => [
                "<?php abstract class MyClass { ${fixed} }",
                "<?php abstract class MyClass { ${original} }",
            ],
        ];
    }
}
