<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\FunctionReferenceSpaceFixer
 */
final class FunctionReferenceSpaceFixerTest extends AbstractFixerTestCase
{
    public function testIsRisky()
    {
        static::assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
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
        $same = function (string $content): string {
            $use = $content;
            $use = \str_replace('array &', '&', $use);
            $use = \str_replace(' = array()', '', $use);

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
                $same(\implode(',', \array_fill(0, 30, '& $array'))),
                $same(\implode(',', \array_fill(0, 30, '&$array'))),
            ],
        ];
    }
}
