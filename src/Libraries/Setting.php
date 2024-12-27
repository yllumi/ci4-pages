<?php

namespace Yllumi\Ci4Pages\Libraries;

class Setting 
{
    public $data;

    public function __construct()
    {
        $SettingModel = model('Yllumi\Ci4Pages\Models\SettingModel');
        $this->data = $SettingModel->getAll();
    }

    public function get($setting_name)
    {
        return $this->data[$setting_name] ?? '';
    }
}