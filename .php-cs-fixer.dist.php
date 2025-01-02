<?php

require __DIR__ . '/vendor/autoload.php';

use CodeIgniter\CodingStandard\CodeIgniter4;
use Nexus\CsConfig\Factory;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__ . '/src',
    ]);

$options = [
    'finder' => $finder,
];

return Factory::create(new CodeIgniter4(), [], $options)
    ->forLibrary('yllumi/ci4-pages', 'Toni Haryanto', 'toha.samba@gmail.com', 2024);
