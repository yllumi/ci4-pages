<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BasePageController extends BaseController
{
    use ResponseTrait;

    // Global data
    public array $data;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // load helper
        helper('Yllumi\Ci4Pages\Helpers\pageview');
    }

    public function index()
    {
        throw PageNotFoundException::forPageNotFound('Method not implemented: index');
    }

    public function supply()
    {
        throw PageNotFoundException::forPageNotFound('Method not implemented: supply');
    }

    public function detail($id)
    {
        throw PageNotFoundException::forPageNotFound('Method not implemented: detail');
    }

    public function process()
    {
        throw PageNotFoundException::forPageNotFound('Method not implemented: process');
    }
}
