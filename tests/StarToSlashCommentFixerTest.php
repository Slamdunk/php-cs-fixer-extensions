<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

final class StarToSlashCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideDefaultCases
     */
    public function testDefaults($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDefaultCases()
    {
        return array(
            array(
                '<?php
// lonely line
',
                '<?php
/* lonely line */
',
            ),
            array(
                '<?php
   // indented line
',
                '<?php
   /* indented line */
',
            ),
            array(
                '<?php
   // weird-spaced line
',
                '<?php
   /*   weird-spaced line*/
',
            ),
            array(
                '<?php // start-end',
                '<?php /* start-end */',
            ),
            array(
                "<?php\n \t \n \t // weird indent\n",
                "<?php\n \t \n \t /* weird indent */\n",
            ),
            array(
                "<?php\n// with spaces after\n \t ",
                "<?php\n/* with spaces after */ \t \n \t ",
            ),
            array(
                '<?php
$a = 1; // after code
',
                '<?php
$a = 1; /* after code */
',
            ),
            array(
                '<?php
   /* first */ // second
',
                '<?php
   /* first */ /* second */
',
            ),
            array(
                '<?php
    // one line',
                '<?php
    /*one line

     */',
            ),
            array(
                '<?php
    // one line',
                '<?php
    /*

    one line*/',
            ),
            array(
                '<?php
    // one line',
                "<?php
    /* \t " . "
 \t   * one line " . '
     *
     */',
            ),

            // Untouched cases
            array(
                '<?php
$a = 1; /* in code */ $b = 2;
',
            ),
            array(
                '<?php
    /*
     * in code 2
     */ $a = 1;
',
            ),
            array(
                '<?php
    /*
     * first line
     * second line
     */',
            ),
            array(
                '<?php
    /*
     * first line
     *
     * second line
     */',
            ),
            array(
                '<?php
    /*first line
second line*/',
            ),
            array(
                '<?php /** inline doc comment */',
            ),
            array(
                '<?php
    /**
     * Doc comment
     */',
            ),
        );
    }
}
