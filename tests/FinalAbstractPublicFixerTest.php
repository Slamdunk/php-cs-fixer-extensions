<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SlamCsFixer\FinalAbstractPublicFixer;

#[CoversClass(FinalAbstractPublicFixer::class)]
final class FinalAbstractPublicFixerTest extends AbstractFixerTestCase
{
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /** @return string[][] */
    public static function provideFixCases(): iterable
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
            'final-class'        => ["<?php final class MyClass { {$original} }"],
            'trait'              => ["<?php trait MyClass { {$original} }"],
            'interface'          => ['<?php interface MyClass {
public function f1();
public static function f4();
            }'],
            'anonymous-class'    => ["<?php abstract class MyClass { private function test() { \$a = new class { {$original} }; } }"],
            'magic-methods'      => ['<?php abstract class MyClass {
public function __construct() {}
public function __destruct() {}
public function __call($a, $b) {}
public static function __callStatic($a, $b) {}
public function __get($a) {}
public function __set($a, $b) {}
public function __isset($a) {}
public function __unset($a) {}
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
abstract protected function bar();
            }'],
            'abstract-class'     => [
                "<?php abstract class MyClass { {$fixed} }",
                "<?php abstract class MyClass { {$original} }",
            ],
        ];
    }
}
