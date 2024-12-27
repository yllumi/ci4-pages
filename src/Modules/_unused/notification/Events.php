<?php

class NotificationEvents {

	public $events = [
		'Post_model.insert' => 'pushPostAuthorNotif'
	];

	public function pushPostAuthorNotif($params)
	{
		// Push to room, notification.
        ci()->load->model('notification/Room_model');
        ci()->Room_model->push('post-' . $params['id'], $params['author']);
	}

}