<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class AlignMultilineCommentFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    private $tokenKinds;

    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $this->tokenKinds = array(T_DOC_COMMENT);
        if ('phpdocs_only' !== $this->configuration['comment_type']) {
            $this->tokenKinds[] = T_COMMENT;
        }
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'Multiline doc comment: enforce asterisk start on each line [PSR-5] and align them.',
            array(
                new CodeSample(
'<?php
    /**
            * This is a DOC Comment
with a line not prefixed with asterisk

   */'
                ),
                new CodeSample(
'<?php
    /*
            * This is a doc-like multiline comment
*/',
                    array('comment_type' => 'phpdocs_like')
                ),
                new CodeSample(
'<?php
    /*
            * This is a doc-like multiline comment
with a line not prefixed with asterisk

   */',
                    array('comment_type' => 'all_multiline')
                ),
            )
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound($this->tokenKinds);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind($this->tokenKinds)) {
                continue;
            }

            $whitespace = '';
            $previousIndex = $index - 1;
            if ($tokens[$previousIndex]->isWhitespace()) {
                $whitespace = $tokens[$previousIndex]->getContent();
                --$previousIndex;
            }
            if ($tokens[$previousIndex]->isGivenKind(T_OPEN_TAG)) {
                $whitespace = preg_replace('/\S/', '', $tokens[$previousIndex]->getContent()) . $whitespace;
            }

            if (! preg_match('/\R([ \t]*)$/', $whitespace, $matches)) {
                continue;
            }

            $indentation = $matches[1];
            $lines = preg_split('/\R/', $token->getContent());
            foreach ($lines as $lineNumber => $line) {
                if (0 === $lineNumber) {
                    continue;
                }

                $line = ltrim($line);
                if ($token->isGivenKind(T_COMMENT) && (! isset($line[0]) || '*' !== $line[0])) {
                    if ('all_multiline' !== $this->configuration['comment_type']) {
                        continue 2;
                    }
                    continue;
                }

                if (! isset($line[0])) {
                    $line = '*' . $line;
                } elseif ('*' !== $line[0]) {
                    $line = '* ' . $line;
                }

                $lines[$lineNumber] = $indentation . ' ' . $line;
            }

            $tokens[$index]->setContent(implode($lineEnding, $lines));
        }
    }

    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver(array(
            (new FixerOptionBuilder('comment_type', 'Whether to align doc-like multiline comments if all lines start with an asterisk [`phpdocs_like`] or all multile comments  with mixed content on lines that start with an asteristk [`all_multiline`]'))
                ->setAllowedValues(array('phpdocs_only', 'phpdocs_like', 'all_multiline'))
                ->setDefault('phpdocs_only')
                ->getOption(),
        ));
    }
}
