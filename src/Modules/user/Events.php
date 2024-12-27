<?php

class UserEvents {

	public $events = [
		'Frontend_Controller.constructor' => 'checkUserStatus',
	];

	// Cek apakah user sudah aktivasi akun
	// Pengecekan ini dilakukan kalau mode "Allow Login Before Activation" diset aktif
	public function checkUserStatus($params)
	{
		if(setting_item('user.allow_login_before_activation') == 1){
			$user = isLoggedIn();
			if($user && $user['status'] == 'inactive'){
				ci()->session->set_flashdata('toastr', json_encode(['type'=>'warning', 'message'=>'Segera aktivasi akun dengan mengklik tautan aktivasi yang telah kami kirim ke alamat email.']));
			}
		}
	}

}