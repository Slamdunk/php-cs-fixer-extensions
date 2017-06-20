<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\WhitespacesFixerConfig;

final class AlignMultilineCommentFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration()
    {
        $this->setExpectedException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);

        $this->fixer->configure(array('a' => 1));
    }

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
$a = 1;
    /**
     * Doc comment
     *
     *
     *
     * first without an asterisk
     * second without an asterisk or space
     */',
                '<?php
$a = 1;
    /**
     * Doc comment
       *

*
    first without an asterisk
second without an asterisk or space
   */',
            ),
            array(
                '<?php
    /**
     * Document start
     */',
                '<?php
    /**
* Document start
    */',
            ),
            array(
                "<?php\n \n /**\n  * Two new lines\n  */",
                "<?php\n \n /**\n* Two new lines\n*/",
            ),
            array(
                "<?php
\t/**
\t * Tabs as indentation
\t */",
                "<?php
\t/**
* Tabs as indentation
        */",
            ),
            array(
                '<?php
$a = 1;
/**
 * Doc command without prior indentation
 */',
                '<?php
$a = 1;
/**
* Doc command without prior indentation
*/',
            ),
            array(
                '<?php
/**
 * Doc command without prior indentation
 * Document start
 */',
                '<?php
/**
* Doc command without prior indentation
* Document start
*/',
            ),

            // Untouched cases
            array(
                '<?php
    /*
     * Multiline comment
       *
*
   */',
            ),
            array(
                '<?php
    /** inline doc comment */',
            ),
            array(
                '<?php
 $a=1;  /**
*
 doc comment that doesn\'t start in a new line

*/',
            ),
            array(
                '<?php
    # Hash single line comments are untouched
     #
   #
      #',
            ),
            array(
                '<?php
    // Slash single line comments are untouched
     //
   //
      //',
            ),
        );
    }

    /**
     * @dataProvider provideDocLikeMultilineComments
     */
    public function testDocLikeMultilineComments($expected, $input = null)
    {
        $this->fixer->configure(array('comment_type' => 'phpdocs_like'));
        $this->doTest($expected, $input);
    }

    public function provideDocLikeMultilineComments()
    {
        return array(
            array(
                '<?php
    /*
     * Doc-like Multiline comment
     *
     *
     */',
                '<?php
    /*
     * Doc-like Multiline comment
       *
*
   */',
            ),
            array(
                '<?php
    /*
     * Multiline comment with mixed content
       *
  Line without an asterisk
*
   */',
            ),
        );
    }

    /**
     * @dataProvider provideMixedContentMultilineComments
     */
    public function testMixedContentMultilineComments($expected, $input = null)
    {
        $this->fixer->configure(array('comment_type' => 'all_multiline'));
        $this->doTest($expected, $input);
    }

    public function provideMixedContentMultilineComments()
    {
        return array(
            array(
                '<?php
    /*
     * Multiline comment with mixed content
     *
  Line without an asterisk
     *
     */',
                '<?php
    /*
     * Multiline comment with mixed content
       *
  Line without an asterisk
*
   */',
            ),
        );
    }

    /**
     * @dataProvider provideDefaultCases
     */
    public function testWhitespaceAwareness($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $expected = str_replace("\n", "\r\n", $expected);
        if ($input !== null) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->doTest($expected, $input);
    }
}
