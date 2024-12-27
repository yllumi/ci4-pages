<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Heroic\Config;

use CodeIgniter\Config\BaseConfig;

class Emailer extends BaseConfig
{
    public bool $useMailcatcher = false;
    public bool $sendByQueue    = false;
    public bool $debugEmailer   = false;
    public string $SMTPHost     = 'localhost';
    public int $SMTPPort        = 1025;
    public string $SMTPUsername;
    public string $SMTPPassword;
    public string $emailFrom  = 'admin@heroicbit.com';
    public string $senderName = 'Heroicbit Team';
}
