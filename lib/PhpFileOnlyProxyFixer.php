<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;

final class PhpFileOnlyProxyFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    private FixerInterface $fixer;

    public function __construct(FixerInterface $fixer)
    {
        $this->fixer = $fixer;
    }

    public function configure(?array $configuration = null): void
    {
        if (! $this->fixer instanceof ConfigurableFixerInterface) {
            return;
        }

        $this->fixer->configure($configuration);
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        if (! $this->fixer instanceof ConfigurableFixerInterface) {
            return new class() implements FixerConfigurationResolverInterface {
                public function getOptions(): array
                {
                    return [];
                }

                public function resolve(array $configuration): array
                {
                    return [];
                }
            };
        }

        return $this->fixer->getConfigurationDefinition();
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config): void
    {
        if (! $this->fixer instanceof WhitespacesAwareFixerInterface) {
            return;
        }

        $this->fixer->setWhitespacesConfig($config);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $originalDefinition = $this->fixer->getDefinition();

        return new FixerDefinition(
            \sprintf('PHP-FILE-ONLY: %s', $originalDefinition->getSummary()),
            $originalDefinition->getCodeSamples(),
            $originalDefinition->getDescription(),
            $originalDefinition->getRiskyDescription()
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $this->fixer->isCandidate($tokens);
    }

    public function isRisky(): bool
    {
        return $this->fixer->isRisky();
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $this->fixer->fix($file, $tokens);
    }

    public function getName(): string
    {
        return \sprintf('Slam/php_only_%s', \str_replace('/', '_', \mb_strtolower($this->fixer->getName())));
    }

    public function getPriority(): int
    {
        return $this->fixer->getPriority();
    }

    public function supports(SplFileInfo $file): bool
    {
        return 'php' === \pathinfo($file->getFilename(), \PATHINFO_EXTENSION);
    }
}
