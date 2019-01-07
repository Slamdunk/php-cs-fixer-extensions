<?php

declare(strict_types=1);

namespace SlamCsFixer;

use PhpCsFixer\Config as PhpCsFixerConfig;
use PhpCsFixer\Fixer as MainFixer;
use Symfony\Component\Console\Terminal;

final class Config extends PhpCsFixerConfig
{
    const APP_V1    = 'APP_V1';
    const APP_V2    = 'APP_V2';
    const LIB       = 'LIB';

    public function __construct(string $type = self::APP_V2, array $overriddenRules = [])
    {
        parent::__construct(\sprintf('%s:%s', __NAMESPACE__, $type));
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

        $rules = [
            '@DoctrineAnnotation'                           => true,
            '@PHP71Migration'                               => true,
            '@PHP71Migration:risky'                         => true,
            '@PHPUnit60Migration:risky'                     => true,
            '@Symfony'                                      => true,
            '@Symfony:risky'                                => true,
            'Slam/final_abstract_public'                    => self::APP_V1 !== $type,
            'Slam/final_internal_class'                     => self::APP_V1 !== $type,
            'Slam/function_reference_space'                 => true,
            'Slam/php_only_braces'                          => true,
            'Slam/php_only_slam_inline_comment_spacer'      => true,
            'Slam/utf8'                                     => self::APP_V1 !== $type,
            'align_multiline_comment'                       => ['comment_type' => 'all_multiline'],
            'array_indentation'                             => true,
            'array_syntax'                                  => ['syntax' => self::LIB === $type ? 'short' : 'long'],
            'backtick_to_shell_exec'                        => true,
            'binary_operator_spaces'                        => ['default' => 'align_single_space'],
            'blank_line_before_statement'                   => true,
            'braces'                                        => false,
            'class_definition'                              => ['singleItemSingleLine' => true],
            'class_keyword_remove'                          => false,
            'combine_consecutive_issets'                    => false,
            'combine_consecutive_unsets'                    => false,
            'comment_to_phpdoc'                             => false,
            'compact_nullable_typehint'                     => true,
            'concat_space'                                  => ['spacing' => 'one'],
            'date_time_immutable'                           => false,
            'declare_strict_types'                          => self::APP_V1 !== $type,
            'doctrine_annotation_array_assignment'          => true,
            'doctrine_annotation_spaces'                    => true,
            'encoding'                                      => self::APP_V1 !== $type,
            'error_suppression'                             => false,
            'escape_implicit_backslashes'                   => true,
            'explicit_indirect_variable'                    => true,
            'explicit_string_variable'                      => true,
            'final_internal_class'                          => false,
            'fully_qualified_strict_types'                  => true,
            'function_to_constant'                          => ['functions' => ['get_class', 'get_called_class', 'php_sapi_name', 'phpversion', 'pi']],
            'general_phpdoc_annotation_remove'              => false,
            'header_comment'                                => false,
            'heredoc_indentation'                           => false,
            'heredoc_to_nowdoc'                             => true,
            'linebreak_after_opening_tag'                   => true,
            'list_syntax'                                   => true,
            'logical_operators'                             => self::LIB === $type,
            'mb_str_functions'                              => self::APP_V2 === $type,
            'method_argument_space'                         => ['keep_multiple_spaces_after_comma' => true],
            'method_chaining_indentation'                   => true,
            'multiline_comment_opening_closing'             => true,
            'multiline_whitespace_before_semicolons'        => ['strategy' => 'new_line_for_chained_calls'],
            'native_constant_invocation'                    => self::LIB === $type,
            'native_function_invocation'                    => self::LIB === $type,
            'no_alternative_syntax'                         => true,
            'no_binary_string'                              => true,
            'no_blank_lines_before_namespace'               => false,
            'no_extra_blank_lines'                          => ['tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'use_trait', 'curly_brace_block', 'parenthesis_brace_block', 'square_brace_block']],
            'no_multiline_whitespace_around_double_arrow'   => false,
            'no_null_property_initialization'               => true,
            'no_php4_constructor'                           => true,
            'no_short_echo_tag'                             => true,
            'no_superfluous_elseif'                         => true,
            'no_superfluous_phpdoc_tags'                    => false,
            'no_unneeded_control_parentheses'               => true,
            'no_unreachable_default_argument_value'         => true,
            'no_unset_cast'                                 => true,
            'no_unset_on_property'                          => true,
            'no_useless_else'                               => true,
            'no_useless_return'                             => true,
            'not_operator_with_space'                       => false,
            'not_operator_with_successor_space'             => true,
            'ordered_class_elements'                        => ['order' => ['use_trait', 'constant_public', 'constant_protected', 'constant_private', 'property', 'construct', 'destruct', 'magic', 'phpunit', 'method']],
            'ordered_imports'                               => true,
            'php_unit_internal_class'                       => false,
            'php_unit_method_casing'                        => true,
            'php_unit_ordered_covers'                       => true,
            'php_unit_set_up_tear_down_visibility'          => true,
            'php_unit_strict'                               => false,
            'php_unit_test_annotation'                      => true,
            'php_unit_test_case_static_method_calls'        => true,
            'php_unit_test_class_requires_covers'           => false,
            'phpdoc_add_missing_param_annotation'           => false,
            'phpdoc_order'                                  => true,
            'phpdoc_to_return_type'                         => false,
            'phpdoc_trim_consecutive_blank_line_separation' => true,
            'phpdoc_types_order'                            => true,
            'phpdoc_var_annotation_correct_order'           => true,
            'pow_to_exponentiation'                         => false,
            'psr0'                                          => true,
            'random_api_migration'                          => true,
            'return_assignment'                             => true,
            'semicolon_after_instruction'                   => true,
            'simplified_null_return'                        => true,
            'single_line_comment_style'                     => true,
            'single_quote'                                  => ['strings_containing_single_quote_chars' => true],
            'space_after_semicolon'                         => true,
            'static_lambda'                                 => false,
            'strict_comparison'                             => self::APP_V1 !== $type,
            'strict_param'                                  => self::APP_V1 !== $type,
            'string_line_ending'                            => true,
            'unary_operator_spaces'                         => false,
            'void_return'                                   => false,
            'yoda_style'                                    => true,
        ];
        if (! empty($overriddenRules)) {
            $rules = \array_merge($rules, $overriddenRules);
        }

        $this->setRules($rules);
    }
}
