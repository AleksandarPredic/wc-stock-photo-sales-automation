<?php
/**
 * A tool to automatically fix PHP Coding Standards issues
 *
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 * @link https://cs.symfony.com
 * TODO: Implement psr-12 when released as stable https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/4502
 */
$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('node_modules')
    ->exclude('build')
    ->in(__DIR__)
;
return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
          'default' => 'align',
          'operators' => []
        ],
        'align_multiline_comment' => [
            'comment_type' => 'all_multiline',
        ],
        'constant_case' => ['case' => 'upper'],
        'no_useless_return' => true,
        'not_operator_with_space' => false,
        'is_null' => false,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'yoda_style' => null,
        'full_opening_tag' => true,
        'phpdoc_order' => true,
        'no_unused_imports' => true,
        'blank_line_before_statement' => true,
        'braces' => true,
        'class_definition' => true,
        'elseif' => true,
        'function_declaration' => true,
        'indentation_type' => true,
        'method_argument_space' => [
          'on_multiline' => 'ensure_fully_multiline'
        ],
        'line_ending' => true,
        'lowercase_constants' => true,
        'method_argument_space' => true,
        'no_closing_tag' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_short_bool_cast' => true,
        'no_short_echo_tag' => true,
        'no_useless_else' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_scalar' => true,
        'single_blank_line_at_eof' => true,
        'single_blank_line_before_namespace' => true,
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'single_line_comment_style' => true,
        'single_trait_insert_per_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
    ])
    ->setFormat('txt')
    ->setFinder($finder)
;
