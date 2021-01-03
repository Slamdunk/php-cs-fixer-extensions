<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use Exception;
use InvalidArgumentException;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Linter\TokenizerLinter;
use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

abstract class AbstractFixerTestCase extends TestCase
{
    use AssertTokensTrait;

    private TokenizerLinter $linter;
    protected DefinedFixerInterface $fixer;

    protected function setUp(): void
    {
        $this->linter = new TokenizerLinter();
        $this->fixer  = $this->createFixer();
    }

    final protected function createFixer()
    {
        $fixerClass = static::class;
        $fixerClass = \str_replace('\\Tests\\', '\\', $fixerClass);
        $fixerClass = \preg_replace('/Test$/', '', $fixerClass);

        return new $fixerClass();
    }

    /**
     * @param string $filename
     *
     * @return SplFileInfo
     */
    final protected function getTestFile($filename = __FILE__)
    {
        static $files = [];

        if (! isset($files[$filename])) {
            $files[$filename] = new SplFileInfo($filename);
        }

        return $files[$filename];
    }

    /**
     * Tests if a fixer fixes a given string to match the expected result.
     *
     * It is used both if you want to test if something is fixed or if it is not touched by the fixer.
     * It also makes sure that the expected output does not change when run through the fixer. That means that you
     * do not need two test cases like [$expected] and [$expected, $input] (where $expected is the same in both cases)
     * as the latter covers both of them.
     * This method throws an exception if $expected and $input are equal to prevent test cases that accidentally do
     * not test anything.
     *
     * @param string           $expected The expected fixer output
     * @param null|string      $input    The fixer input, or null if it should intentionally be equal to the output
     * @param null|SplFileInfo $file     The file to fix, or null if unneeded
     */
    final protected function doTest($expected, $input = null, ?SplFileInfo $file = null): void
    {
        if ($expected === $input) {
            throw new InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $file            = $file ?: $this->getTestFile();
        $fileIsSupported = $this->fixer->supports($file);

        if (null !== $input) {
            static::assertNull($this->lintSource($input));

            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            if ($fileIsSupported) {
                static::assertTrue($this->fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                static::assertFalse($tokens->isChanged(), 'Fixer must not touch Tokens on candidate check.');
                $fixResult = $this->fixer->fix($file, $tokens);
                static::assertNull($fixResult, '->fix method must return null.');
            }

            static::assertSame(
                $expected,
                $tokens->generateCode(),
                'Code build on input code must match expected code.'
            );
            static::assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');

            $tokens->clearEmptyTokens();

            static::assertSame(
                \count($tokens),
                \count(\array_unique(\array_map(static function (Token $token) {
                    return \spl_object_hash($token);
                }, $tokens->toArray()))),
                'Token items inside Tokens collection must be unique.'
            );

            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($expected);
            static::assertTokens($expectedTokens, $tokens);
        }

        static::assertNull($this->lintSource($expected));

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        if ($fileIsSupported) {
            $fixResult = $this->fixer->fix($file, $tokens);
            static::assertNull($fixResult, '->fix method must return null.');
        }

        static::assertSame(
            $expected,
            $tokens->generateCode(),
            'Code build on expected code must not change.'
        );
        static::assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
    }

    /**
     * @param string $source
     *
     * @return null|string
     */
    private function lintSource($source)
    {
        try {
            $this->linter->lintSource($source)->check();
        } catch (Exception $e) {
            return $e->getMessage() . "\n\nSource:\n{$source}";
        }

        return null;
    }
}
