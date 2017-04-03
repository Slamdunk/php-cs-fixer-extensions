<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

final class FunctionReferenceSpaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        $same = function ($content) {
            return sprintf(
'<?php

function test(%s) {
$test = function (%s) {
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
};
}
',
                $content,
                $content
            );
        };

        return array(
            array(
                $same('array & $array = array()'),
            ),
            array(
                $same('array & $array = array()'),
                $same('array &$array = array()'),
            ),
            array(
                $same('array & $array = array()'),
                $same('array &  $array = array()'),
            ),
            array(
                $same('array & $array = array()'),
                $same("array & \n \$array = array()"),
            ),
            array(
                $same(implode(',', array_fill(0, 30, '& $array'))),
                $same(implode(',', array_fill(0, 30, '&$array'))),
            ),
        );
    }
}
