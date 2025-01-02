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

class Webapp extends BaseConfig
{
    public string $authLogo;
    public string $navbarLogo;
    public string $colorTheme              = '#1E74FD';
    public string $navbarColor             = '#FFFFFF';
    public bool $transparentNavbar         = false;
    public string $navbarTextColor         = '#333333';
    public string $linkColor               = '#1E74FD';
    public bool $enableBottomMenu          = true;
    public string $bottomMenuColor         = '#1E74FD';
    public bool $highlightCenterBottomMenu = false;
    public string $appBackground           = '#f5f5f5';
    public bool $darkmode                  = false;
    public string $GTagID;
    public bool $enableSW;
    public string $manifest;
    public string $assetlinks;
    public string $customScripts;
}
