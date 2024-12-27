<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_file_urls'))
{
    function get_file_urls($file)
    {
        $ci = get_instance();
        $ci->config->load('media/config');
        $fileManagerDriver = config_item('fileManagerDriver');
        $fileManagerConfig = config_item('fileManagerConfig')[$fileManagerDriver];
        $upload_url = $fileManagerConfig['cdn_base_url'];

        if(!empty($file))
        {
            $data['original'] = $upload_url.'original/'.$file;
            foreach ($fileManagerConfig['thumbnail_versions'] as $thumbsize) {
                $data['thumb_'.$thumbsize] = $upload_url.'thumbnail/'.$thumbsize.'/'.$file;
            }
        } else {
            $data['original'] = "";
            foreach ($fileManagerConfig['thumbnail_versions'] as $thumbsize) {
                $data['thumb_'.$thumbsize] = "";
            }
        }

        return $data;
    }
}

if (!function_exists('get_file_url'))
{
    function get_file_url($file, $size = 'original')
    {
        $ci = get_instance();
        $ci->config->load('files/config');
        $fileManagerDriver = config_item('fileManagerDriver');
        $fileManagerConfig = config_item('fileManagerConfig')[$fileManagerDriver];
        $upload_url = $fileManagerConfig['cdn_base_url'];

        if(empty($file))
            return "";
        else {
            if(strpos($file, 'http') === 0)
                return $file;
            else if($size == 'original')
                return $upload_url.'original/'.$file;
            else
                return $upload_url.'thumbnail/'.$size.'/'.$file;
        }
    }
}
