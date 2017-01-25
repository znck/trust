<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src', 'tests', 'config', 'migrations']);

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        // 'strict_param' => true,
        'array_syntax' => array('syntax' => 'short'),
    ))
    ->setFinder($finder);
