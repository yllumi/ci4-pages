<?php

$config['fileManagerDriver'] = $_ENV['FILEMANAGER_DRIVER'] ?? 'Local'; // or S3

$config['fileManagerConfig']['Local'] = [
	'thumbnail_versions' => ['250x150','340x195','700x350'],
	'rename' 			 => false,
	'delete' 			 => true,
	'upload_path' 		 => './uploads/',
	'cdn_base_url' 		 => base_url('uploads/'),
	'allowed_types'		 => 'jpg|png|jpeg',
];

$config['fileManagerConfig']['S3'] = [
	'thumbnail_versions' => ['250x150','340x195','700x350'],
	'upload_path' 		 => './uploads/',
	'rename' 			 => false,
	'delete' 			 => true,
	'allowed_types'		 => 'jpg|png|jpeg',
	'cdn_base_url' 		 => 'https://cdn-cdpl.sgp1.digitaloceanspaces.com/',
	'key' 				 => 'BVRYBQUHP2F2CMR7J6MX',
	'secret' 			 => 'L0ULEqaQy2QIdnccOW6e6lf5qy6Qg5Ya01A5fbqEVu8',
	'space_name' 		 => 'cdn-cdpl',
	'region' 			 => 'sgp1',
];