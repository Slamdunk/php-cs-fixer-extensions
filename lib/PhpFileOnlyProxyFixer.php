<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

final class PhpFileOnlyProxyFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    private $fixer;

    public function __construct(FixerInterface $fixer)
    {
        $this->fixer = $fixer;
    }

    public function configure(array $configuration = null)
    {
        if (! $this->fixer instanceof ConfigurationDefinitionFixerInterface) {
            throw new \LogicException(sprintf('Cannot configure using Abstract parent, child not implementing `%s`.', ConfigurationDefinitionFixerInterface::class));
        }

        $this->fixer->configure($configuration);
    }

    public function getConfigurationDefinition()
    {
        if (! $this->fixer instanceof ConfigurationDefinitionFixerInterface) {
            throw new \LogicException(sprintf('Cannot get configuration definition using Abstract parent, child not implementing `%s`.', ConfigurationDefinitionFixerInterface::class));
        }

        return $this->fixer->getConfigurationDefinition();
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config)
    {
        if (! $this->fixer instanceof WhitespacesAwareFixerInterface) {
            throw new \LogicException(sprintf('Cannot run method for class not implementing `%s`.', WhitespacesAwareFixerInterface::class));
        }

        $this->fixer->setWhitespacesConfig($config);
    }

    public function getDefinition()
    {
        $originalDefinition = $this->fixer->getDefinition();

        return new FixerDefinition(
            sprintf('PHP-FILE-ONLY: %s', $originalDefinition->getSummary()),
            $originalDefinition->getCodeSamples(),
            $originalDefinition->getDescription(),
            $originalDefinition->getRiskyDescription()
        );
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
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }
}
