#!/usr/bin/env php
<?php

$fileHeaderComment = <<<COMMENT
This file is part of eelly package.

(c) eelly.com

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

define('ROOT_PATH', __DIR__);
chdir(ROOT_PATH);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$rules = [
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'ordered_imports' => true,
    'phpdoc_order' => true,
    'array_syntax' => ['syntax' => 'short'],
    'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'bottom'],
    'ordered_class_elements' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_var_without_name' => false,
    'no_multiline_whitespace_before_semicolons' => true,
];

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder)
    ->setCacheFile(sys_get_temp_dir().'/.php_cs.cache');
