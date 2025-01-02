<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PagesRouter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Ambil URI dari request
        $uri     = strtolower($request->getPath());
        $uriPage = $uri = trim($uri, '/');

        // Set default page for root uri
        if (empty($uri)) {
            $uriPage = config('App')->defaultPage ?? 'home';
            $uri     = '/';
        }

        $uriSegments = explode('/', $uriPage);

        // Cek apakah segment pertama adalah /api
        $isApi          = false;
        $isAjax         = false;
        $controllerName = 'PageController';
        if ($uriSegments[0] === 'ajax') {
            array_shift($uriSegments);
            $isAjax = true;
        } elseif ($uriSegments[0] === 'api') {
            array_shift($uriSegments);
            $controllerName = 'APIController';
            $isApi          = true;
        }

        // Path ke folder Pages
        $basePath = APPPATH . 'Pages';

        // Evaluasi apakah folder sesuai dengan URI
        $found = false;

        while (count($uriSegments) > 0) {
            $folderPath = $basePath . '/' . str_replace('/', DIRECTORY_SEPARATOR, implode('/', $uriSegments));
            if (is_dir($folderPath)) {
                $found   = true;
                $uriPage = implode('/', $uriSegments);
                break;
            }
            array_pop($uriSegments);
        }

        // Jika tidak ada yang cocok, kembalikan 404
        if (! $found) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Cek apakah ada folder dengan struktur tersebut
        if (is_dir($folderPath)) {
            // Pastikan ada file controller

            if (file_exists($folderPath . '/' . $controllerName . '.php')) {
                // Ubah namespace dan jalankan controller
                $controllerNamespace = '\\App\\Pages\\' . str_replace('/', '\\', $uriPage) . '\\' . $controllerName;

                // Add route resource for the controller
                $routeCollection = service('routes');

                // HACK, inject new property to route collection
                $routeCollection->currentURI = $uriPage;

                // dd($uriSegments, $uri, $isApi, $isAjax, $uriPage, $controllerNamespace);
                // Set route for default page
                if ($uri === '/') {
                    $routeCollection->get('/', $controllerNamespace . '::index');
                }

                // Route to resource controller
                elseif ($isApi) {
                    $routeCollection->resource('api/' . $uriPage, ['controller' => $controllerNamespace]);

                    // Route for uri with prefix ajax/
                } elseif ($isAjax) {
                    $routeCollection->get('ajax/' . $uriPage, $controllerNamespace . '::supply');
                    // dd($uriSegments, $uri, $isApi, $isAjax, $uriPage, $controllerNamespace);

                    // Route to base controller
                } else {
                    $getMethod = $request->getGet('get') ? 'get_'.$request->getGet('get') : 'index';
                    $routeCollection->get($uriPage . '(:any)', $controllerNamespace . '::'.$getMethod.'$1');

                    $postMethod = $request->getPost('post') ? 'post_'.$request->getPost('post') : 'process';
                    $routeCollection->post($uriPage . '(:any)', $controllerNamespace . '::'.$postMethod.'$1');
                    // dd($uriSegments, $uri, $isApi, $isAjax, $uriPage);
                }

                return $routeCollection;
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah response
    }
}
