<?php

$finder = PhpCsFixer\Finder::create()
    //->exclude('somedir')
    //->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php'
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer' => true,
        'strict_param' => false,
        'array_syntax' => ['syntax' => 'short'],
        'braces' => [
            'allow_single_line_closure' => true,
            'position_after_functions_and_oop_constructs' => 'same'],
    ])
    ->setFinder($finder)
;
