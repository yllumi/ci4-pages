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

class Module extends BaseConfig
{
    // Core CMS modules
    public $coreModules = [
        'activity'    => [APPPATH . 'modules/activity/', '../modules/activity/', true],
        'banner'      => [APPPATH . 'modules/banner/', '../modules/banner/', true],
        'dashboard'   => [APPPATH . 'modules/dashboard/', '../modules/dashboard/'],
        'development' => [APPPATH . 'modules/development/', '../modules/development/'],
        'entry'       => [APPPATH . 'modules/entry/', '../modules/entry/'],
        'media'       => [APPPATH . 'modules/media/', '../modules/media/'],
        'navigation'  => [APPPATH . 'modules/navigation/', '../modules/navigation/'],
        'post'        => [APPPATH . 'modules/post/', '../modules/post/', true],
        'setting'     => [APPPATH . 'modules/setting/', '../modules/setting/'],
        'user'        => [APPPATH . 'modules/user/', '../modules/user/', true],
        'search'      => [APPPATH . 'modules/search/', '../modules/search/', true],
    ];
    public $entry_paths = [
        'redirect'     => APPPATH . 'entries/redirect/',
        'downloadable' => APPPATH . 'entries/downloadable/',
        'slider'       => APPPATH . 'entries/slider/',
        'video'        => APPPATH . 'entries/video/',
        'province'     => APPPATH . 'entries/province/',
        'regency'      => APPPATH . 'entries/regency/',
        'district'     => APPPATH . 'entries/district/',
        'village'      => APPPATH . 'entries/village/',
    ];
}
