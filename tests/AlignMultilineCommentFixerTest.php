<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\WhitespacesFixerConfig;

final class AlignMultilineCommentFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration()
    {
        $this->setExpectedException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);

        $this->fixer->configure(['a' => 1]);
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
        return [
            [
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
            ],
            [
                '<?php
    /**
     * Document start
     */',
                '<?php
    /**
* Document start
    */',
            ],
            [
                "<?php\n \n /**\n  * Two new lines\n  */",
                "<?php\n \n /**\n* Two new lines\n*/",
            ],
            [
                "<?php
\t/**
\t * Tabs as indentation
\t */",
                "<?php
\t/**
* Tabs as indentation
        */",
            ],
            [
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
            ],
            [
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
            ],

            // Untouched cases
            [
                '<?php
    /*
     * Multiline comment
       *
*
   */',
            ],
            [
                '<?php
    /** inline doc comment */',
            ],
            [
                '<?php
 $a=1;  /**
*
 doc comment that doesn\'t start in a new line

*/',
            ],
            [
                '<?php
    # Hash single line comments are untouched
     #
   #
      #',
            ],
            [
                '<?php
    // Slash single line comments are untouched
     //
   //
      //',
            ],
        ];
    }

    /**
     * @dataProvider provideDocLikeMultilineComments
     */
    public function testDocLikeMultilineComments($expected, $input = null)
    {
        $this->fixer->configure(['comment_type' => 'phpdocs_like']);
        $this->doTest($expected, $input);
    }

    public function provideDocLikeMultilineComments()
    {
        return [
            [
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
            ],
            [
                '<?php
    /*
     * Multiline comment with mixed content
       *
  Line without an asterisk
*
   */',
            ],
        ];
    }

    /**
     * @dataProvider provideMixedContentMultilineComments
     */
    public function testMixedContentMultilineComments($expected, $input = null)
    {
        $this->fixer->configure(['comment_type' => 'all_multiline']);
        $this->doTest($expected, $input);
    }

    public function provideMixedContentMultilineComments()
    {
        return [
            [
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
            ],
        ];
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
