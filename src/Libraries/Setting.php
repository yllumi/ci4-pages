<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages\Libraries;

class Setting
{
    public $data;

    public function __construct()
    {
        $SettingModel = model('Yllumi\Ci4Pages\Models\SettingModel');
        $this->data   = $SettingModel->getAll();
    }

    public function get($setting_name)
    {
        return $this->data[$setting_name] ?? '';
    }
}
