<?php namespace App\modules\user\libraries;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class FirebaseAuth 
{
	private $serviceAccountPath = SITEPATH.'configs/firebase-admin.json';
	private $auth;

	public function __construct(){
		$factory = (new Factory)->withServiceAccount($this->serviceAccountPath);
		$this->auth = $factory->createAuth();
	}

	public function login($post = [])
	{
		try {
			$signInResult = $this->auth->signInWithEmailAndPassword($post['email'], $post['password']);
		} catch (FailedToSignIn $e) {
			$data['status'] = false;
		    $data['message'] = $e->getMessage();
		    return $data;
		}

		$idTokenString = $signInResult->idToken();
	    $verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
		if(! $verifiedIdToken->getClaim('email_verified')){
			$data['status'] = false;
		    $data['message'] = 'EMAIL_INACTIVE';
			return $data;
		}

		// Set session
		ci()->session->set_userdata([
			'logged_in' => true,
			'user_id' => $verifiedIdToken->getClaim('sub'),
			'email' => $verifiedIdToken->getClaim('email'),
			'fullname' => $verifiedIdToken->getClaim('name'),
			'token' => $idTokenString
		]);

		// Add token to cookie
		set_cookie('usertoken', $idTokenString, 3600); 

		$data['status'] = true;
		return $data;
	}

	public function register(array $data, string $continueUri)
	{
		// set to flashdata
		ci()->session->set_flashdata('old', $data);

		// Register user to local database
		$userData = [
		    'name' => $data['name'],
		    'username' => str_replace(' ', '_', strtolower($data['name'])).'_'.random_string('alnum', 5),
		    'email' => $data['email'],
		    'password' => $data['password'],
		    'confirm_password' => $data['confirm_password']
		];
		$profileData = [
		    'phone' => $data['phone'],
		];
		$result = ci()->ci_auth->register($userData, $profileData, false);
		if($result['status'] == 'failed'){
			$data['status'] = false;
		    $data['message'] = $result['message'];
		    return $data;
		}

		// Register User to Firebase
		$userProperties = [
		    'email' => $data['email'],
		    'emailVerified' => false,
		    'phoneNumber' => '+62'.$data['phone'],
		    'password' => $data['password'],
		    'displayName' => $data['name'],
		    'disabled' => false,
		];

		try {
			$createdUser = $this->auth->createUser($userProperties);
		} catch (AuthException $e) {
			$data['status'] = false;
		    $data['message'] = $e->getMessage();
		} catch (FirebaseException $e) {
			$data['status'] = false;
		    $data['message'] = $e->getMessage();
		}
	    
	    // Delete inserted data to database if createUser firebase fail
	    if(($data['status'] ?? true) == false){
	    	ci()->ci_auth->hardDeleteUser($result['id']);
			return $data;
	    }

		// Update firebase uid to source_id in mein_users
		ci()->ci_auth->updateUser(['email' => $createdUser->email], ['source_id' => $createdUser->uid], [], true);

		// Send email activation
		try{
			$this->auth->sendEmailVerificationLink($data['email'], [
				'continueUrl' => site_url($continueUri.'?token='.$result['token'])
			]);
		} catch (FirebaseException $e) {
			$data['status'] = false;
		    $data['message'] = $e->getMessage();
		}

		$data['status'] = true;
	    $data['message'] = 'Registrasi berhasil. Silakan cek link yang kami kirim ke email Anda untuk aktivasi akun.';
		return $data;
	}

	public function logout()
	{
		ci()->ci_auth->logout();
		delete_cookie('usertoken');
	}

	public function activateUser($token)
	{
		$result = ci()->ci_auth->activate($token);
		return $result['status'] == 'success';
	}

	public function checkToken()
	{
		$data['status'] = true;
		$idTokenString = ci()->session->token ?? '';
		if(!$idTokenString) {
			$data['status'] = false;
			return $data;
		}

		try {
			$verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
		} catch (FirebaseException $e) {
			$data['status'] = false;
			$data['message'] = $e->getMessage();
		} catch (InvalidToken $e) {
			$data['status'] = false;
		    $data['message'] = $e->getMessage();
		}

		$data['verifiedData'] = $verifiedIdToken;
		return $data;
	}

	public function sendPasswordReset($email, $continueUri)
	{
		$data['status'] = true;

		// Send email with reset password link
		try{
			$this->auth->sendPasswordResetLink($email, [
				'continueUrl' => site_url($continueUri)
			]);
		} catch (FirebaseException $e){
			$data['status'] = false;
			$data['message'] = $e->getMessage();
		}

		return $data;
	}

	public function changeEmail()
	{
		
	}

}