<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use Exception;
use InvalidArgumentException;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Linter\TokenizerLinter;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

abstract class AbstractFixerTestCase extends TestCase
{
    private TokenizerLinter $linter;
    protected FixerInterface $fixer;

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

    final protected function getTestFile(string $filename = __FILE__): SplFileInfo
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
     */
    final protected function doTest(string $expected, ?string $input = null, ?SplFileInfo $file = null): void
    {
        if ($expected === $input) {
            throw new InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $file            = $file ?: $this->getTestFile();
        $fileIsSupported = $this->fixer->supports($file);

        if (null !== $input) {
            self::assertNull($this->lintSource($input));

            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            if ($fileIsSupported) {
                self::assertTrue($this->fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                self::assertFalse($tokens->isChanged(), 'Fixer must not touch Tokens on candidate check.');
                $this->fixer->fix($file, $tokens);
            }

            self::assertSame(
                $expected,
                $tokens->generateCode(),
                'Code build on input code must match expected code.'
            );
            self::assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');

            $tokens->clearEmptyTokens();

            self::assertSame(
                \count($tokens),
                \count(\array_unique(\array_map(static fn (Token $token) => \spl_object_hash($token), $tokens->toArray()))),
                'Token items inside Tokens collection must be unique.'
            );

            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($expected);
            self::assertTokens($expectedTokens, $tokens);
        }

        self::assertNull($this->lintSource($expected));

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        if ($fileIsSupported) {
            $this->fixer->fix($file, $tokens);
        }

        self::assertSame(
            $expected,
            $tokens->generateCode(),
            'Code build on expected code must not change.'
        );
        self::assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
    }

    private function lintSource(string $source): ?string
    {
        try {
            $this->linter->lintSource($source)->check();
        } catch (Exception $e) {
            return $e->getMessage() . "\n\nSource:\n{$source}";
        }

        return null;
    }

    private static function assertTokens(Tokens $expectedTokens, Tokens $inputTokens): void
    {
        foreach ($expectedTokens as $index => $expectedToken) {
            if (! isset($inputTokens[$index])) {
                self::fail(\sprintf("The token at index %d must be:\n%s, but is not set in the input collection.", $index, $expectedToken->toJson()));
            }

            $inputToken = $inputTokens[$index];

            self::assertTrue(
                $expectedToken->equals($inputToken),
                \sprintf("The token at index %d must be:\n%s,\ngot:\n%s.", $index, $expectedToken->toJson(), $inputToken->toJson())
            );

            $expectedTokenKind = $expectedToken->isArray() ? $expectedToken->getId() : $expectedToken->getContent();
            self::assertTrue(
                $inputTokens->isTokenKindFound($expectedTokenKind),
                \sprintf(
                    'The token kind %s (%s) must be found in tokens collection.',
                    $expectedTokenKind,
                    \is_string($expectedTokenKind) ? $expectedTokenKind : Token::getNameForId($expectedTokenKind)
                )
            );
        }

        self::assertSame($expectedTokens->count(), $inputTokens->count(), 'Both collections must have the same length.');
    }
}
