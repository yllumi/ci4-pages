<?php

namespace Heroic\Libraries;

class Setting 
{
    public $data;

    public function __construct()
    {
        $SettingModel = model('Heroic\Models\SettingModel');
        $this->data = $SettingModel->getAll();
    }

    public function get($setting_name)
    {
        return $this->data[$setting_name] ?? '';
    }
}