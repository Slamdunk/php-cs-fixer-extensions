<?php

declare(strict_types=1);

namespace SlamCsFixer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SlamCsFixer\FinalInternalClassFixer;

#[CoversClass(FinalInternalClassFixer::class)]
final class FinalInternalClassFixerTest extends AbstractFixerTestCase
{
    #[DataProvider('provideCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public static function provideCases(): array
    {
        return [
            [
                '<?php final class MyClass {}',
                '<?php class MyClass {}',
            ],
            [
                '<?php final class MyClass extends MyAbstract {}',
                '<?php class MyClass extends MyAbstract {}',
            ],
            [
                '<?php final class MyClass implements MyInterface {}',
                '<?php class MyClass implements MyInterface {}',
            ],
            [
                "<?php\n/**\n * @codeCoverageIgnore\n */\nfinal class MyEntity {}",
                "<?php\n/**\n * @codeCoverageIgnore\n */\nclass MyEntity {}",
            ],
            [
                "<?php\n/**\n * @ORM\\Entity\n */\nclass MyEntity {}",
            ],
            [
                "<?php\n/**\n * @final\n */\nclass MyEntity {}",
            ],
            [
                "<?php\n#[ORM\\Entity]\nclass MyEntity {}",
            ],
            [
                "<?php\n#[ORM\\Entity]\n#[CustomAttribute]\nclass MyEntity {}",
            ],
            [
                "<?php\n#[ORM\\Entity(repositoryClass: \\MyClass::class)]\n#[CustomAttribute]\nclass MyEntity {}",
            ],
            [
                "<?php\n#[\\Doctrine\\ORM\\Mapping\\Entity(repositoryClass: \\MyClass::class)]\nclass MyEntity {}",
            ],
            [
                '<?php abstract class MyAbstract {}',
            ],
            [
                '<?php trait MyTrait {}',
            ],
            [
                '<?php $anonymClass = new class {};',
            ],
        ];
    }
}
