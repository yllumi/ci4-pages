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

class Theme extends BaseConfig
{
    public string $frontendTheme = 'mobilekit';
    public string $adminTheme    = 'admin';
    public string $frontendThemePath;
    public string $adminThemePath;
    public string $frontendThemeURL;
    public string $adminThemeURL;
    public string $homePage = 'member';
    public string $adminBackgroundColor;
    public string $adminBackgroundCSS;
    public string $adminLogoWidth;
    public string $minifyHTML;
    public string $showProfiler;

    public function __construct()
    {
        $this->frontendThemePath = 'template/' . $this->frontendTheme . '/';
        $this->adminThemePath    = 'template/' . $this->adminTheme . '/';

        $this->frontendThemeURL = base_url($this->frontendTheme . '/');
        $this->adminThemeURL    = base_url($this->adminTheme . '/');
    }
}
