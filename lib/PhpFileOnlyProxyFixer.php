<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\AbstractFixer as PhpCsFixerAbstractFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpFileOnlyProxyFixer extends PhpCsFixerAbstractFixer
{
    private $fixer;

    public function __construct(FixerInterface $fixer)
    {
        parent::__construct();

        $this->fixer = $fixer;
    }

    public function isCandidate(Tokens $tokens)
    {
        return $this->fixer->isCandidate($tokens);
    }

    public function isRisky()
    {
        return $this->fixer->isRisky();
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        return $this->fixer->applyFix($file, $tokens);
    }

    public function getName()
    {
        return sprintf('Slam/php_only_%s', $this->fixer->getName());
    }

    public function getPriority()
    {
        return $this->fixer->getPriority();
    }

    public function supports(\SplFileInfo $file)
    {
        return pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php';
    }

    public function getDefinition()
    {
        return new FixerDefinition(sprintf('[.php] %s', $this->fixer->getDescription()), array());
    }
}
