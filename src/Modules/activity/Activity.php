<?php

namespace App\modules\activity;

class Activity {

    static function log(string $module, string $action, int $user_id = null, $post_data = null)
    {
        if(!$post_data)
            $post_data = ci()->input->post(null, true);

        $data = [
            'module' => $module,
            'action' => $action,
            'user_id' => $user_id ?? ci()->session->user_id ?? 0,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] . ' ' . $_SERVER['REMOTE_ADDR'],
            'post_data' => is_array($post_data) ? json_encode($post_data) : $post_data
        ];
        
        ci()->db->insert('activity_logs', $data);
    }

}