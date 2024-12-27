<?php
$idname = str_replace(['[',']'], ['__',''], $config['field']);
$attributes = 'id="'.$idname.'" class="form-control" multiple data-caption="'.$config['label'].'"';

if ($config['relation'] ?? '')
{
    $relEntry = $config['relation']['entry'];
    $modelName = $config['relation']['model'] ?? ucfirst($relEntry).'Model';
    $caption = $config['relation']['caption'];
    $options = $this->$modelName->as_dropdown($caption)->getAll();

    if(($config['relation']['pivot_table'] ?? '') && isset($result[$config['relation']['local_key']])){
    	$values = $this->db
    				  ->select($config['relation']['pivot_foreign_key'])
    				  ->from($config['relation']['pivot_table'])
    				  ->where($config['relation']['pivot_local_key'], $result[$config['relation']['local_key']])
    				  ->get()->result_array();
		  $value = array_column($values,$config['relation']['pivot_foreign_key']);
    }
}
elseif(isset($config['options']))
{
    // If defined, limit the options as defined
    $options = $config['options']; 
}
else 
{
  // Use free text tagging
  if(is_string($value)){
    $options = array_combine(explode(',', $value), explode(',', $value));
    $value = explode(',', $value);
  } else {
    $options = array_combine(array_values($value), array_values($value));
  }
}

echo form_dropdown($config['field'].'[]', $options ?? [], $value, $attributes);

if(isset($config['options']))
  echo ("<script>
    $(function(){
      $('#".$idname. "').select2();
    });
  </script>");
else
  echo ("<script>
    $(function(){
      $('#".$idname. "').select2({
        tags: true,
        createTag: function (tag) {
          return {id: tag.term, text: tag.term, tag: true};
        }
      });
    });
  </script>");