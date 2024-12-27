<?php

namespace Heroic\Config;

use CodeIgniter\Config\BaseConfig;

class Plugin extends BaseConfig
{
    public string $enabledModules;
    public string $disabledEntries;
}
