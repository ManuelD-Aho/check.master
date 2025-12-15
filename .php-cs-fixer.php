<?php

declare(strict_types=1);

/*
 * PHP-CS-Fixer Configuration
 * PSR-12 Coding Style
 */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/database')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try'],
        ],
        'concat_space' => ['spacing' => 'one'],
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'class_attributes_separation' => [
            'elements' => ['method' => 'one', 'property' => 'one'],
        ],
        'no_extra_blank_lines' => [
            'tokens' => ['extra', 'throw', 'use'],
        ],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_trim' => true,
        'cast_spaces' => ['space' => 'single'],
        'function_typehint_space' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'visibility_required' => [
            'elements' => ['property', 'method', 'const'],
        ],
        'void_return' => true,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/storage/cache/.php-cs-fixer.cache');
