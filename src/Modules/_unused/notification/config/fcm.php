<?php

$config['fcm'] = [
	'enable'	=> $_ENV['FCM_ENABLE'] ?? false,
	'endpoint' 	=> 'https://fcm.googleapis.com/fcm/send',
	'authToken'	=> $_ENV['FCM_AUTH_TOKEN'] ?? '',
];