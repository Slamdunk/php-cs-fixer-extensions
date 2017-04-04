<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpFileOnlyProxyFixer implements DefinedFixerInterface
{
    private $fixer;

    public function __construct(DefinedFixerInterface $fixer)
    {
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

    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        return $this->fixer->fix($file, $tokens);
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
        $originalDefinition = $this->fixer->getDefinition();

        return new FixerDefinition(
            sprintf('[.php] %s', $originalDefinition->getSummary()),
            $originalDefinition->getCodeSamples(),
            $originalDefinition->getDescription(),
            $originalDefinition->getRiskyDescription()
        );
    }
}
