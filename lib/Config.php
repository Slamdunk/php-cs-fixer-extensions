<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Config as PhpCsFixerConfig;
use PhpCsFixer\Fixer as MainFixer;
use Symfony\Component\Console\Terminal;

final class Config extends PhpCsFixerConfig
{
    public const RULES = [
        '@PhpCsFixer'                                       => true,
        '@PhpCsFixer:risky'                                 => true,
        '@DoctrineAnnotation'                               => true,
        '@PHP71Migration'                                   => true,
        '@PHP71Migration:risky'                             => true,
        '@PHPUnit75Migration:risky'                         => true,
        'Slam/final_abstract_public'                        => true,
        'Slam/final_internal_class'                         => true,
        'Slam/function_reference_space'                     => true,
        'Slam/php_only_braces'                              => true,
        'Slam/php_only_slam_inline_comment_spacer'          => true,
        'Slam/utf8'                                         => true,
        'align_multiline_comment'                           => ['comment_type' => 'all_multiline'],
        'backtick_to_shell_exec'                            => true,
        'binary_operator_spaces'                            => ['default' => 'align_single_space'],
        'braces'                                            => false,   // See Slam/php_only_braces
        'class_keyword_remove'                              => false,
        'combine_consecutive_issets'                        => false,
        'combine_consecutive_unsets'                        => false,
        'comment_to_phpdoc'                                 => false,
        'concat_space'                                      => ['spacing' => 'one'],
        'date_time_immutable'                               => false,
        'error_suppression'                                 => false,
        'final_class'                                       => false,
        'final_internal_class'                              => false,
        'final_public_method_for_abstract_class'            => false,
        'final_static_access'                               => true,
        'general_phpdoc_annotation_remove'                  => false,
        'global_namespace_import'                           => true,
        'header_comment'                                    => false,
        'heredoc_indentation'                               => false,
        'linebreak_after_opening_tag'                       => true,
        'list_syntax'                                       => true,
        'mb_str_functions'                                  => false,
        'method_argument_space'                             => ['keep_multiple_spaces_after_comma' => true],
        'native_constant_invocation'                        => true,
        'native_function_invocation'                        => true,
        'no_blank_lines_before_namespace'                   => false,
        'no_multiline_whitespace_around_double_arrow'       => false,
        'no_php4_constructor'                               => true,
        'no_superfluous_phpdoc_tags'                        => ['allow_mixed' => true],
        'not_operator_with_space'                           => false,
        'not_operator_with_successor_space'                 => true,
        'nullable_type_declaration_for_default_null_value'  => true,
        'ordered_class_elements'                            => ['order' => ['use_trait', 'constant_public', 'constant_protected', 'constant_private', 'property', 'construct', 'destruct', 'magic', 'phpunit', 'method']],
        'ordered_interfaces'                                => true,
        'php_unit_internal_class'                           => false,
        'php_unit_size_class'                               => false,
        'php_unit_strict'                                   => false,
        'php_unit_test_class_requires_covers'               => false,
        'phpdoc_add_missing_param_annotation'               => false,
        'phpdoc_line_span'                                  => true,
        'phpdoc_to_param_type'                              => false,
        'phpdoc_to_return_type'                             => false,
        'pow_to_exponentiation'                             => false,
        'psr0'                                              => true,
        'random_api_migration'                              => true,
        'self_static_accessor'                              => true,
        'simple_to_complex_string_variable'                 => false,
        'simplified_null_return'                            => false,
        'single_line_throw'                                 => false,
        'space_after_semicolon'                             => true,
        'static_lambda'                                     => false,
        'unary_operator_spaces'                             => false,
    ];

    /**
     * @param array<string, mixed> $overriddenRules
     */
    public function __construct(array $overriddenRules = [])
    {
        parent::__construct(__NAMESPACE__);
        \putenv('PHP_CS_FIXER_FUTURE_MODE=1');

        // @codeCoverageIgnoreStart
        if ('cygwin' === \getenv('TERM')) {
            \putenv(\sprintf('COLUMNS=%s', (new Terminal())->getWidth() - 1));
        }
        // @codeCoverageIgnoreEnd

        $this->setRiskyAllowed(true);
        $this->registerCustomFixers([
            new FinalAbstractPublicFixer(),
            new FinalInternalClassFixer(),
            new FunctionReferenceSpaceFixer(),
            new PhpFileOnlyProxyFixer(new InlineCommentSpacerFixer()),
            new PhpFileOnlyProxyFixer(new MainFixer\Basic\BracesFixer()),
            new Utf8Fixer(),
        ]);

        $rules = self::RULES;
        if (! empty($overriddenRules)) {
            $rules = \array_merge($rules, $overriddenRules);
        }

        $this->setRules($rules);
    }
}
