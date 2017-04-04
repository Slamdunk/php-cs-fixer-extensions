<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\AbstractFixer as PhpCsFixerAbstractFixer;

abstract class AbstractFixer extends PhpCsFixerAbstractFixer
{
    final public function getName()
    {
        return sprintf('Slam/%s', parent::getName());
    }
}
