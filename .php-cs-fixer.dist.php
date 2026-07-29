<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->append([
        __DIR__ . '/.php-cs-fixer.dist.php',
    ])
    ->exclude([
        'vendor',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'          => true,
        'array_syntax'    => ['syntax' => 'short'],
        'list_syntax'     => ['syntax' => 'short'],
        'braces_position' => [
          'functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'binary_operator_spaces' => [
            'default'   => 'single_space',
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'align_single_space_minimal',
            ],
        ],
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'cast_spaces'                  => ['space' => 'single'],
        'concat_space'                 => ['spacing' => 'one'],
        'declare_strict_types'         => true,
        'final_class'                  => false,
        'fully_qualified_strict_types' => true,
        'increment_style'              => ['style' => 'post'],
        'lowercase_cast'               => true,
        'lowercase_static_reference'   => true,
        'method_argument_space'        => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'no_extra_blank_lines'                   => [
            'tokens' => [
                'extra',
                'throw',
                'use',
            ],
        ],
        'no_short_bool_cast'          => true,
        'no_unused_imports'           => true,
        'no_useless_else'             => true,
        'no_useless_return'           => true,
        'no_whitespace_in_blank_line' => true,
        'ordered_imports'             => [
            'sort_algorithm' => 'alpha',
            'imports_order'  => ['class', 'function', 'const'],
        ],
        'php_unit_method_casing'         => ['case' => 'camel_case'],
        'phpdoc_scalar'                  => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name'        => true,
        'return_type_declaration'        => ['space_before' => 'one'],
        'single_quote'                   => true,
        'space_after_semicolon'          => true,
        'standardize_not_equals'         => true,
        'trailing_comma_in_multiline'    => [
            'elements' => ['arrays', 'arguments', 'parameters'],
        ],
        'trim_array_spaces'     => true,
        'unary_operator_spaces' => true,
        'modifier_keywords'     => [
            'elements' => ['property', 'method', 'const'],
        ],
        'yoda_style' => [
            'equal'            => false,
            'identical'        => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
