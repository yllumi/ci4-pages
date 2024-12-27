<?php

namespace Yllumi\Ci4Pages\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'mein_options';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    public function getAll()
    {
        $result = $this->findAll();
        if (count($result) > 0) {
            $data = [];
            foreach ($result as $value) {
                $data[$value['option_group'] . '.' . $value['option_name']] = $value['option_value'];
            }

            return $data;
        }

        return false;
    }
}
