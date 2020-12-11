<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase as PhpCsFixerAbstractFixerTestCase;

abstract class AbstractFixerTestCase extends PhpCsFixerAbstractFixerTestCase
{
    /**
     * @var DefinedFixerInterface
     */
    protected $fixer;

    final protected function createFixer()
    {
        $fixerClass = static::class;
        $fixerClass = \str_replace('\\Tests\\', '\\', $fixerClass);
        $fixerClass = \preg_replace('/Test$/', '', $fixerClass);

        return new $fixerClass();
    }

    protected function assertMatchesRegularExpression(string $format, string $string, string $message = ''): void
    {
        static::assertRegExp($format, $string, $message);
    }
}
