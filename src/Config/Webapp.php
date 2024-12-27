<?php

namespace Heroic\Config;

use CodeIgniter\Config\BaseConfig;

class Webapp extends BaseConfig
{
    public string $authLogo;
    public string $navbarLogo;
    public string $colorTheme = '#1E74FD';
    public string $navbarColor = '#FFFFFF';
    public bool $transparentNavbar = false;
    public string $navbarTextColor = '#333333';
    public string $linkColor = '#1E74FD';
    public bool $enableBottomMenu = true;
    public string $bottomMenuColor = '#1E74FD';
    public bool $highlightCenterBottomMenu = false;
    public string $appBackground = '#f5f5f5';
    public bool $darkmode = false;
    public string $GTagID;
    public bool $enableSW;
    public string $manifest;
    public string $assetlinks;
    public string $customScripts;
}
