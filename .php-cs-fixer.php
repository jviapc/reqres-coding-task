<?php

$finder = PhpCsFixer\Finder::create()
    ->name('*.php')
    ->in([
        __DIR__,
    ])
    ->exclude([
        'vendor',
        'docker',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setCacheFile('/tmp/.php-cs-fixer.cache')
    ->setRules([
        '@PhpCsFixer' => true,
        '@PSR12' => true,
        'array_indentation' => true,
        'backtick_to_shell_exec' => false,
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => [
                '=>' => 'single_space',
                '=' => 'single_space',
            ],
        ],
        'increment_style' => false,
        'blank_line_before_statement' => [
            'statements' => [
                'continue',
                'declare',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'phpdoc',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
                'yield_from',
            ],
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'trait_import' => 'none',
            ],
        ],
        'class_definition' => [
            'inline_constructor_arguments' => false,
            'multi_line_extends_each_single_line' => true,
            'single_item_single_line' => true,
            'single_line' => true,
            'space_before_parenthesis' => true,
        ],
        'combine_consecutive_unsets' => false,
        'concat_space' => ['spacing' => 'one'],
        'control_structure_braces' => true,
        'control_structure_continuation_position' => [
            'position' => 'same_line',
        ],
        'curly_braces_position' => [
            'allow_single_line_anonymous_functions' => true,
            'allow_single_line_empty_anonymous_classes' => true,
            'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'anonymous_functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'control_structures_opening_brace' => 'same_line',
            'functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'declare_parentheses' => true,
        'empty_loop_body' => false,
        'escape_implicit_backslashes' => false,
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
        ],
        'list_syntax' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'modernize_types_casting' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'no_alias_functions' => true,
        'no_empty_comment' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'attribute',
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ],
        'no_multiple_statements_per_line' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unreachable_default_argument_value' => true,
        'not_operator_with_successor_space' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_imports' => [
            'imports_order' => [
                'class', 'function', 'const',
            ],
        ],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_no_alias_tag' => false,
        'phpdoc_separation' => false,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_types_order' => false,
        'psr_autoloading' => false,
        'self_accessor' => false,
        'simplified_null_return' => false,
        'single_import_per_statement' => [
            'group_to_single_imports' => false,
        ],
        'single_space_around_construct' => true,
        'space_after_semicolon' => [
            'remove_in_empty_for_expressions' => false,
        ],
        'statement_indentation' => true,
        'visibility_required' => [
            'elements' => ['method', 'property', 'const'],
        ],
        'whitespace_after_comma_in_array' => [
            'ensure_single_space' => true,
        ],
        'yoda_style' => false,

        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
    ])
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
