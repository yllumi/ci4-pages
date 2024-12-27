<?php

use Config\View;
use Config\Services as AppServices;

if (!function_exists('pageView')) {
    function pageView(string $name, array $data = [], array $options = []): string
    {
        $config   = config(View::class);
        $saveData = $config->saveData;
        $pageViewPath = APPPATH . 'Pages/';
        
        // Create new view instance with custom view path
        $renderer = new \CodeIgniter\View\View($config, $pageViewPath, AppServices::get('locator'), CI_DEBUG, AppServices::get('logger'));
        
        if (array_key_exists('saveData', $options)) {
            $saveData = (bool) $options['saveData'];
            unset($options['saveData']);
        }

        return $renderer->setData($data, 'raw')->render($name, $options, $saveData);
    }
}
