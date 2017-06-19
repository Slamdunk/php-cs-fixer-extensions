<?php

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
        return [
            [
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
            ],
            [
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
            ],
            [
                '<?php
 $a=1; /** test */   /**
                      */',
                '<?php
 $a=1; /** test */   /**
*/',
            ],
            [
                '<?php
    /*
  Lines not prefixed with
asterisk are left untouched
     */',
            ],
            [
                '<?php
    # Single line comments are untouched
     #
   #
      #',
            ],
            [
                '<?php
    // Single line comments are untouched
     //
   //
      //',
            ],
        ];
    }
}
