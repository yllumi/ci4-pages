<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PageCreateCommand extends BaseCommand
{
    protected $group       = 'Page'; // Group command
    protected $name        = 'page:create'; // Nama command
    protected $description = 'Create a new page router';

    public function run(array $params)
    {
        if (empty($params)) {
            CLI::error('Please specify the page name.');

            return;
        }

        $pageName       = $params[0];
        $basePath       = APPPATH . "Pages/{$pageName}";
        $controllerPath = "{$basePath}/PageController.php";
        $viewPath       = "{$basePath}/index.php";

        // Path to templates
        $templatePath = dirname(__DIR__) . '/templates';

        // Create the folder if it doesn't exist
        if (! is_dir($basePath)) {
            mkdir($basePath, 0755, true);
            CLI::write("Folder created: {$basePath}", 'green');
        } else {
            CLI::write("Folder already exists: {$basePath}", 'yellow');
        }

        // Create the PageController.php file
        $this->createFileFromTemplate(
            "{$templatePath}/PageController.php",
            $controllerPath,
            [
                '{{pageName}}'      => $pageName,
                '{{pageNamespace}}' => str_replace('/', '\\', $pageName),
            ]
        );

        // Create the index.php file
        $this->createFileFromTemplate(
            "{$templatePath}/index.php",
            $viewPath,
            [
                '{{pageName}}' => $pageName,
                '{{pageSlug}}' => str_replace('/', '_', $pageName),
            ]
        );
    }

    /**
     * Create a file from a template, replacing placeholders.
     */
    private function createFileFromTemplate(string $templateFile, string $targetFile, array $replacements)
    {
        if (! file_exists($templateFile . '.tpl')) {
            CLI::error("Template not found: {$templateFile}.tpl");

            return;
        }

        if (file_exists($targetFile)) {
            CLI::write("File already exists: {$targetFile}", 'yellow');

            return;
        }

        // Read template content
        $content = file_get_contents($templateFile . '.tpl');

        // Replace placeholders
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        // Write to target file
        file_put_contents($targetFile, $content);
        CLI::write("File created: {$targetFile}", 'green');
    }
}
