<?= form_dropdown("filter[{$config['field']}]", [''=>'Semua'] + $config['options'], $this->input->get("filter[{$config['field']}]", true), 'class="form-control form-control-sm" placeholder="filter by {$config[\'field\']}"'); ?>

