<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SlamCsFixer\FunctionReferenceSpaceFixer;

#[CoversClass(FunctionReferenceSpaceFixer::class)]
final class FunctionReferenceSpaceFixerTest extends AbstractFixerTestCase
{
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
        $same = static function (string $content): string {
            $use = $content;
            $use = \str_replace('array &', '&', $use);
            $use = \str_replace(' = array()', '', $use);
            $use = \str_replace('$array', '$secondArray', $use);

            $invariant = \PHP_EOL . \preg_replace('/\s+/', ' ', '
                $var =&  $var;
                $var =& $var;
                $var =&$var;
                $var &&  $var;
                $var && $var;
                $var &&$var;
                $var &  $var;
                $var & $var;
                $var &$var;
                $var = "& ";
                $var = "&";
            ') . \PHP_EOL;

            return \sprintf(
                '<?php

function test(%1$s) {
    %3$s
    $test = function (%1$s) use (%2$s) {
        %3$s
    };
}

class Foo
{
    function bar(%1$s) {
        %3$s
    }
    function baz() {
        %3$s
        return new class () {
            function xyz(%1$s) {
                %3$s
            }
        };
    }
}

',
                $content,
                $use,
                $invariant
            );
        };

        $inc1 = $inc2 = 0;

        return [
            [
                $same('array & $array = array()'),
                $same('array &$array = array()'),
            ],
            [
                $same('array & $array = array()'),
                $same('array &  $array = array()'),
            ],
            [
                $same('array & $array = array()'),
                $same("array & \n \$array = array()"),
            ],
            [
                $same(\implode(',', \array_map(static function (string $var) use (& $inc1): string {
                    return $var . ++$inc1;
                }, \array_fill(0, 30, '& $array')))),
                $same(\implode(',', \array_map(static function (string $var) use (& $inc2): string {
                    return $var . ++$inc2;
                }, \array_fill(0, 30, '&$array')))),
            ],
        ];
    }
}
