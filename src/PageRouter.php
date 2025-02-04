<?php

declare(strict_types=1);

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages;

use Closure;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Exceptions\BadRequestException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Router;
use RuntimeException;

/**
 * Page based router.
 *
 * This class extends the CodeIgniter's Router class
 * to handle page based routes.
 */
class PageRouter extends Router
{
    /**
     * Finds the controller corresponding to the URI.
     *
     * @param string|null $uri URI path relative to baseURL
     *
     * @return (Closure(mixed...): (ResponseInterface|string|void))|string Controller classname or Closure
     *
     * @throws BadRequestException
     * @throws PageNotFoundException
     */
    public function handle(?string $uri = null)
    {
        try {
            $handle = parent::handle($uri);
        } catch (PageNotFoundException $e) {
            if ($uri === null) {
                throw $e;
            }

            // HACK: Check for page based routes
            if ($this->pageBasedRoute($uri)) {
                return $this->controllerName();
            }

            throw $e;
        }

        return $handle;
    }

    /**
     * Checks Auto Routes
     *
     * Attempts to match a URI path against Controllers and directories
     * found in APPPATH/Pages, to find a matching page route.
     *
     * @return bool
     */
    public function pageBasedRoute(string $uri)
    {
        $pageFound = false;
        $httpVerb  = strtolower($this->collection->getHTTPVerb());
        $uri       = trim($uri, '/');

        // Set default page for root uri
        if ($uri === '') {
            $uri = config('App')->defaultPage ?? 'home';

            if (! is_string($uri)) {
                throw new RuntimeException('App Config defaultPage must be a string.');
            }
        }

        // Set default variables
        $pagesPath      = config('App')->pagesPath ?? APPPATH . 'Pages';
        $controllerName = 'PageController';
        $this->method   = $httpVerb . ucfirst($this->collection->getDefaultMethod());
        $this->params   = [];

        $uriSegments = explode('/', $uri);

        while ($uriSegments !== []) {
            $folderPath = $pagesPath . '/' . str_replace('/', DIRECTORY_SEPARATOR, implode('/', $uriSegments));
            if (is_dir($folderPath) && file_exists($folderPath . '/' . $controllerName . '.php')) {
                $uri                 = implode('/', $uriSegments);
                $controllerNamespace = '\\App\\Pages\\' . str_replace('/', '\\', $uri) . '\\' . $controllerName;
                $this->controller    = $controllerNamespace;
                $this->params        = array_reverse($this->params);

                // Check if method exists in class
                if (isset($this->params[0]) && method_exists($controllerNamespace, $httpVerb . ucfirst($this->params[0]))) {
                    $this->method = $httpVerb . ucfirst($this->params[0]);
                    array_shift($this->params);
                }

                $pageFound = true;
                break;
            }

            $this->params[] = array_pop($uriSegments);
        }

        return $pageFound;
    }
}
