<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\AbstractFixer as PhpCsFixerAbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;

abstract class AbstractFixer extends PhpCsFixerAbstractFixer
{
    final public function getName()
    {
        return sprintf('Slam/%s', parent::getName());
    }

    final public function getDefinition()
    {
        return new FixerDefinition($this->getDescription(), array());
    }

    abstract protected function getDescription();
}
