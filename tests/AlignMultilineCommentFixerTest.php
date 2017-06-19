<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

final class AlignMultilineCommentFixerTest extends AbstractFixerTestCase
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
        return array(
            array(
                '<?php
    /*
     * Multiline comment
     *
     *
     */',
                '<?php
    /*
     * Multiline comment
       *
*
   */',
            ),
            array(
                '<?php
    /**
     * Multiline doc comment
     *
     *
     */',
                '<?php
    /**
     * Multiline doc comment
       *
*
   */',
            ),
            array(
                '<?php
 $a=1; /** test */   /**
                      */',
                '<?php
 $a=1; /** test */   /**
*/',
            ),
            array(
                '<?php
    /*
  Lines not prefixed with
asterisk are left untouched
     */',
            ),
            array(
                '<?php
    # Single line comments are untouched
     #
   #
      #',
            ),
            array(
                '<?php
    // Single line comments are untouched
     //
   //
      //',
            ),
        );
    }
}
