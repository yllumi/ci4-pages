<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Config\Services as AppServices;
use Config\View;

if (! function_exists('pageView')) {
    /**
     * @param list<mixed>          $data
     * @param array<string, mixed> $options
     */
    function pageView(string $name, array $data = [], array $options = []): string
    {
        $config       = config(View::class);
        $saveData     = $config->saveData;
        $pageViewPath = APPPATH . 'Pages/';

        // Create new view instance with custom view path
        $renderer = new CodeIgniter\View\View($config, $pageViewPath, AppServices::get('locator'), CI_DEBUG, AppServices::get('logger'));

        if (array_key_exists('saveData', $options)) {
            $saveData = (bool) $options['saveData'];
            unset($options['saveData']);
        }

        return $renderer->setData($data, 'raw')->render($name, $options, $saveData);
    }
}

if (! function_exists('asset_url')) {
    /**
     * Generate asset URL with version based on file modification time (filemtime)
     *
     * @param string $filePath Relative path to the asset file
     *
     * @return string Full URL to the asset with version
     */
    function asset_url(string $filePath): string
    {
        // Full file path
        $fullFilePath = FCPATH . $filePath;

        // Check if file exists
        $version = file_exists($fullFilePath)
            // Add file modification time as version
            ? filemtime($fullFilePath)
            // Fallback version (current timestamp if file doesn't exist)
            : time();

        // Generate full URL with version
        return base_url($filePath) . '?v=' . $version;
    }
}
