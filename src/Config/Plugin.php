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

class Plugin extends BaseConfig
{
    public string $enabledModules;
    public string $disabledEntries;
}
