<?php namespace App\modules\entry\libraries;

class Form {

    private $Entry;
    private $value;

    public function __construct($entry)
    {
        $this->Entry = new Entry($entry);
        if(!isset($this->Entry->entryConf['form']))
            throw new \Exception("Entry form configuration not set.", 403);
    }

    public function build($value = [])
    {
        $this->value = $value;
        $formConfig = $this->Entry->entryConf['form'];

        $output = '<div class="'.$formConfig['containerClass'].'">' . "\n";
        foreach ($formConfig['components'] as $component) {
            $method = 'generate'.ucfirst($component['type']);
            $output .= $this->$method($component);
        }
        $output .= '</div>';

        return $output;
    }

    private function generateField($component)
    {
        $output = '<div class="' . ($component['containerClass'] ?? '') . ' input_'. $component['name'].'" id="' . ($component['containerID'] ?? '') . '">';
        $output .= '<label for="'. $component['name'] .'" class="'. ($component['labelClass'] ?? '') .'">'. $this->Entry->entryConf['fields'][$component['name']]['label'];
        if(strpos($this->Entry->entryConf['fields'][$component['name']]['rules'] ?? '', 'required') !== FALSE)
            $output .= ' <strong class="text-danger">*</strong>';
        $output .= '</label>';
        if($this->Entry->entryConf['fields'][$component['name']]['description'] ?? null)
            $output .= '<small class="description">'. $this->Entry->entryConf['fields'][$component['name']]['description'] .'</small>';
        $output .= generate_input($this->Entry->entryConf['fields'][$component['name']], $this->value[$component['name']] ?? null);
        $output .= '</div>' . "\n";
        return $output;
    }

    private function generateSeparator($component)
    {
        $output = '<div class="separator ' . ($component['containerClass'] ?? '') . '" id="' . ($component['containerID'] ?? '') . '"></div>' . "\n";
        return $output;
    }

    private function generateText($component)
    {
        $output = '<div class="'.($component['containerClass'] ?? '').'" id="'.($component['containerID'] ?? '').'">';
        $output .= $component['content'];
        $output .= '</div>' . "\n";
        return $output;
    }

}