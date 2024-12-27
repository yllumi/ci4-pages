<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages\Config;

use CodeIgniter\Config\BaseConfig;

class Site extends BaseConfig
{
    public string $siteTitle          = 'Example Site';
    public string $siteDesc           = 'Just another web app';
    public string $siteLogo           = '';
    public string $siteLogoMonochrome = '';
    public string $siteLogoSmall      = '';
    public string $loginCover         = '';
    public string $phone              = '';
    public string $address            = '';
    public string $contact            = '';
    public string $latlong            = '';
    public string $currency           = 'IDR';
    public bool $enableRegistration   = false;
    public bool $maintenanceMode      = false;
}
