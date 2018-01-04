<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @covers \SlamCsFixer\NativeConstantInvocationFixer
 */
final class NativeConstantInvocationFixerTest extends AbstractFixerTestCase
{
    public function testDefinition()
    {
        $this->assertInstanceOf(FixerDefinition::class, $this->fixer->getDefinition());
    }

    public function testConfigureRejectsUnknownConfigurationKey()
    {
        $key = 'foo';

        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class, \sprintf(
            '[native_constant_invocation] Invalid configuration: The option "%s" does not exist.',
            $key
        ));

        $this->fixer->configure(array(
            $key => 'bar',
        ));
    }

    /**
     * @dataProvider provideInvalidConfigurationElementCases
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidExcludeConfigurationElement($element)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf(
            'Each element must be a non-empty, trimmed string, got "%s" instead.',
            \is_object($element) ? \get_class($element) : \gettype($element)
        ));

        $this->fixer->configure(array(
            'exclude' => array(
                $element,
            ),
        ));
    }

    /**
     * @dataProvider provideInvalidConfigurationElementCases
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidIncludeConfigurationElement($element)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf(
            'Each element must be a non-empty, trimmed string, got "%s" instead.',
            \is_object($element) ? \get_class($element) : \gettype($element)
        ));

        $this->fixer->configure(array(
            'include' => array(
                $element,
            ),
        ));
    }

    /**
     * @return array
     */
    public function provideInvalidConfigurationElementCases()
    {
        return array(
            'null' => array(null),
            'false' => array(false),
            'true' => array(true),
            'int' => array(1),
            'array' => array(array()),
            'float' => array(0.1),
            'object' => array(new \stdClass()),
            'not-trimmed' => array('  M_PI  '),
        );
    }

    public function testConfigureResetsExclude()
    {
        $this->fixer->configure(array(
            'exclude' => array(
                'M_PI',
            ),
        ));

        $before = '<?php var_dump(m_pi, M_PI);';
        $after = '<?php var_dump(m_pi, \\M_PI);';

        $this->doTest($before);

        $this->fixer->configure(array());

        $this->doTest($after, $before);
    }

    public function testIsRisky()
    {
        $fixer = $this->createFixer();

        $this->assertTrue($fixer->isRisky());
    }

    /**
     * @dataProvider provideFixWithDefaultConfigurationCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithDefaultConfigurationCases()
    {
        return array(
            array('<?php var_dump(NULL, FALSE, TRUE, 1);'),
            array('<?php echo CUSTOM_DEFINED_CONSTANT_123;'),
            array('<?php echo m_pi; // Constant are case sensitive'),
            array('<?php namespace M_PI;'),
            array('<?php namespace Foo; use M_PI;'),
            array('<?php class M_PI {}'),
            array('<?php class Foo extends M_PI {}'),
            array('<?php class Foo implements M_PI {}'),
            array('<?php interface M_PI {};'),
            array('<?php trait M_PI {};'),
            array('<?php class Foo { const M_PI = 1; }'),
            array('<?php class Foo { use M_PI; }'),
            array('<?php class Foo { public $M_PI = 1; }'),
            array('<?php class Foo { function M_PI($M_PI) {} }'),
            array('<?php class Foo { function bar() { $M_PI = M_PI() + self::M_PI(); } }'),
            array('<?php class Foo { function bar() { $this->M_PI(self::M_PI); } }'),
            array(
                '<?php echo \\M_PI;',
                '<?php echo M_PI;',
            ),
            array(
                '<?php namespace Foo; use M_PI; echo \\M_PI;',
                '<?php namespace Foo; use M_PI; echo M_PI;',
            ),
            array(
                // Here we are just testing the algorithm.
                // A user likely would add this M_PI to its excluded list.
                '<?php namespace M_PI; const M_PI = 1; return \\M_PI;',
                '<?php namespace M_PI; const M_PI = 1; return M_PI;',
            ),
            array(
                '<?php
echo \\/**/M_PI;
echo \\ M_PI;
echo \\#
#
M_PI;
echo \\M_PI;
',
                '<?php
echo \\/**/M_PI;
echo \\ M_PI;
echo \\#
#
M_PI;
echo M_PI;
',
            ),
        );
    }

    /**
     * @dataProvider provideFixWithConfiguredCustomIncludeCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConfiguredCustomInclude($expected, $input = null)
    {
        $this->fixer->configure(array(
            'include' => array(
                'FOO_BAR_BAZ',
            ),
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithConfiguredCustomIncludeCases()
    {
        return array(
            array(
                '<?php echo \\FOO_BAR_BAZ . \\M_PI;',
                '<?php echo FOO_BAR_BAZ . M_PI;',
            ),
            array(
                '<?php class Foo { public function bar($foo) { return \\FOO_BAR_BAZ . \\M_PI; } }',
                '<?php class Foo { public function bar($foo) { return FOO_BAR_BAZ . M_PI; } }',
            ),
        );
    }

    /**
     * @dataProvider provideFixWithConfiguredOnlyIncludeCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConfiguredOnlyInclude($expected, $input = null)
    {
        $this->fixer->configure(array(
            'fix_built_in' => false,
            'include' => array(
                'M_PI',
            ),
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithConfiguredOnlyIncludeCases()
    {
        return array(
            array(
                '<?php echo PHP_SAPI . FOO_BAR_BAZ . \\M_PI;',
                '<?php echo PHP_SAPI . FOO_BAR_BAZ . M_PI;',
            ),
            array(
                '<?php class Foo { public function bar($foo) { return PHP_SAPI . FOO_BAR_BAZ . \\M_PI; } }',
                '<?php class Foo { public function bar($foo) { return PHP_SAPI . FOO_BAR_BAZ . M_PI; } }',
            ),
        );
    }

    /**
     * @dataProvider provideFixWithConfiguredExcludeCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConfiguredExclude($expected, $input = null)
    {
        $this->fixer->configure(array(
            'exclude' => array(
                'M_PI',
            ),
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithConfiguredExcludeCases()
    {
        return array(
            array(
                '<?php echo \\PHP_SAPI . M_PI;',
                '<?php echo PHP_SAPI . M_PI;',
            ),
            array(
                '<?php class Foo { public function bar($foo) { return \\PHP_SAPI . M_PI; } }',
                '<?php class Foo { public function bar($foo) { return PHP_SAPI . M_PI; } }',
            ),
        );
    }

    public function testNullTrueFalseAreCaseInsensitive()
    {
        $this->fixer->configure(array(
            'fix_built_in' => false,
            'include' => array(
                'null',
                'false',
                'M_PI',
                'M_pi',
            ),
            'exclude' => array(),
        ));

        $expected = <<<'EOT'
<?php
var_dump(
    \null,
    \NULL,
    \Null,
    \nUlL,
    \false,
    \FALSE,
    true,
    TRUE,
    \M_PI,
    \M_pi,
    m_pi,
    m_PI
);
EOT;

        $input = <<<'EOT'
<?php
var_dump(
    null,
    NULL,
    Null,
    nUlL,
    false,
    FALSE,
    true,
    TRUE,
    M_PI,
    M_pi,
    m_pi,
    m_PI
);
EOT;

        $this->doTest($expected, $input);
    }
}
