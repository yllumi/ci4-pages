<?php

namespace Yllumi\Ci4Pages\Config;

use CodeIgniter\Config\BaseConfig;

class Emailer extends BaseConfig
{
    public bool $useMailcatcher = false;
    public bool $sendByQueue = false;
    public bool $debugEmailer = false;
    public string $SMTPHost = 'localhost';
    public int $SMTPPort = 1025;
    public string $SMTPUsername;
    public string $SMTPPassword;
    public string $emailFrom = 'admin@heroicbit.com';
    public string $senderName = 'Heroicbit Team';
}
