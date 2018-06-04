<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PhpCsFixer\Test\AbstractFixerTestCase as PhpCsFixerAbstractFixerTestCase;

abstract class AbstractFixerTestCase extends PhpCsFixerAbstractFixerTestCase
{
    final protected function createFixer()
    {
        $fixerClass = \get_class($this);
        $fixerClass = \str_replace('\\Tests\\', '\\', $fixerClass);
        $fixerClass = \preg_replace('/Test$/', '', $fixerClass);

        return new $fixerClass();
    }

    final protected function getFixerName()
    {
        return \sprintf('Slam/%s', parent::getFixerName());
    }
}
