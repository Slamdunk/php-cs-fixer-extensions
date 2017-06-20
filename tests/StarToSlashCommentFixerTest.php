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
        return [
            [
                '<?php
// lonely line
',
                '<?php
/* lonely line */
',
            ],
            [
                '<?php
   // indented line
',
                '<?php
   /* indented line */
',
            ],
            [
                '<?php
   // weird-spaced line
',
                '<?php
   /*   weird-spaced line*/
',
            ],
            [
                '<?php // start-end',
                '<?php /* start-end */',
            ],
            [
                "<?php\n \t \n \t // weird indent\n",
                "<?php\n \t \n \t /* weird indent */\n",
            ],
            [
                "<?php\n// with spaces after\n \t ",
                "<?php\n/* with spaces after */ \t \n \t ",
            ],
            [
                '<?php
$a = 1; // after code
',
                '<?php
$a = 1; /* after code */
',
            ],
            [
                '<?php
   /* first */ // second
',
                '<?php
   /* first */ /* second */
',
            ],
            [
                '<?php
    // one line',
                '<?php
    /*one line

     */',
            ],
            [
                '<?php
    // one line',
                '<?php
    /*

    one line*/',
            ],
            [
                '<?php
    // one line',
                "<?php
    /* \t "."
 \t   * one line ".'
     *
     */',
            ],

            // Untouched cases
            [
                '<?php
$a = 1; /* in code */ $b = 2;
',
            ],
            [
                '<?php
    /*
     * in code 2
     */ $a = 1;
',
            ],
            [
                '<?php
    /*
     * first line
     * second line
     */',
            ],
            [
                '<?php
    /*
     * first line
     *
     * second line
     */',
            ],
            [
                '<?php
    /*first line
second line*/',
            ],
            [
                '<?php /** inline doc comment */',
            ],
            [
                '<?php
    /**
     * Doc comment
     */',
            ],
        ];
    }
}
