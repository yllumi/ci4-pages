<?php

namespace Yllumi\Ci4Pages\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\Request;
use CodeIgniter\Router\RouteCollectionInterface;
use Config\Services as AppServices;
use Yllumi\Ci4Pages\PageRouter;

class Services extends BaseService
{
    public static function router(?RouteCollectionInterface $routes = null, ?Request $request = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('router', $routes, $request);
        }

        $routes ??= AppServices::get('routes');
        $request ??= AppServices::get('request');

        return new PageRouter($routes, $request);
    }
}