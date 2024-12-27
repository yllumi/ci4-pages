<?php
$idname = str_replace(['[',']'], ['__',''], $config['field']);

if($config['relation']['table'] ?? '')
  $table = $config['relation']['table'];
else {
  $entry = config_item('entries')[$config['relation']['entry']] ?? '';
  if(!$entry) throw new Exception('Entry not found: '.$config['relation']['entry']);
  $table = $entry['table'];
}

$attributes = 'id="'.$idname.'" class="form-control" data-caption="'.$config['label'].'"';
$default_option = [];
if($value){
  if($record = $this->db->select(implode(',',$config['relation']['searchby']))
                        ->where($config['relation']['foreign_key'] ?? 'id', ($entryConf['id_type']??'increment') == 'uuid' ? uuid2bin($value) : $value)
                        ->get($table)
                        ->row_array())
  {
    foreach ($config['relation']['searchby'] as $caption)
      $default_option[$value][] = $record[$caption];

    $default_option[$value] = implode(" - ", $default_option[$value]);
  }
}
echo form_dropdown($config['field'], $default_option, $value, $attributes);

if ($config['relation'] ?? '')
  echo ("<script>
  $(function(){
    $('#".$idname."').select2({
      placeholder: '".($config['placeholder'] ?? '')."',
      ajax: {
        url: '".site_url('admin/entry/config/getSelect2Dropdown?fkey='.($config['relation']['foreign_key']??'id'))."',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          var query = {
            table: '".$table."',
            caption_field: ".json_encode($config['relation']['caption']).",
            search_field: ".json_encode($config['relation']['searchby']).",
            keyword: params.term
          }
          return query;
        },
        cache: false
      }
    });
  });</script>");
