<?php

use App\core\REST_Controller;
/**
 * Micro user auth.
 * 
 * This is auth for Mein CMS need. Super simple for Codeigniter Environment. 
 * 
 * @author Oriza
 */

use \Firebase\JWT\JWT;

class Ci_auth 
{
    public $user = 'mein_users';
    public $profile = 'mein_user_profile';
    public $role = 'mein_roles';
    public $privileges = 'mein_privileges';
    public $role_privileges = 'mein_role_privileges';
    public $login_mode = ['email','phone','username'];
    public $jwt;

    private $loggedInUser = [];
    private $currentUserPrivileges = [];
    
    public function __construct()
    {
        ci()->load->library('user/phpass');
        ci()->load->model('user/User_model');
        ci()->load->model('user/Role_model');

        // Get logged in user, false if not logged in
        $this->loggedInUser = $this->isValidLogin();

        if(isset($this->loggedInUser['role_id']))
            $this->currentUserPrivileges = $this->getRolePrivileges($this->loggedInUser['role_id'], true);
    }

    /**
     * Get detail user by field.
     *
     * @param string $field
     * @param string $value
     * @return array
     */
    public function getUser($field, $value, $status = null)
    {
        ci()->db->select($this->role . '.*, ' . $this->profile.'.*, ' . $this->user . '.*, ' . $this->user .'.id as id,' . $this->user . '.email as email,'  . $this->user . '.status, ' . $this->user . '.created_at');
        ci()->db->from($this->user);
        ci()->db->join($this->profile, $this->profile . '.user_id = ' . $this->user . '.id', 'left');
        ci()->db->join($this->role, $this->role . '.id = ' . $this->user . '.role_id', 'left');
        ci()->db->where($this->user .'.'. $field, $value);

        if($status)
            ci()->db->where($this->user .'.status', $status);
        
        ci()->db->order_by($this->user .'.id', 'desc');
        $result = ci()->db->get()->row_array();
        
        if (!empty($result))
            return $result; 
        
        return [];
    }

    public function getUsers($result = 'data', $status = 'all', $limit = 10, $limit_order = 1)
	{
		if ($result == 'total')
			ci()->db->select($this->user . '.id');
		else
			ci()->db->select('*,' . $this->user . '.id as user_id,' . $this->user . '.status as status,' . $this->user . '.created_at as created_at');
		
		ci()->db->from($this->user);
		ci()->db->join($this->role, $this->role . '.id = '. $this->user . '.role_id');
		
		if (!empty($status) && $status != 'all')
		{
			ci()->db->where($this->user.'.status', $status);
		}

		ci()->db->where($this->user.'.status !=', 'deleted');
		
		if ($result == 'total')
			return ci()->db->get()->num_rows();

		ci()->db->order_by($this->user.'.id', 'desc');
		ci()->db->limit($limit, $limit_order);
		
		return ci()->db->get()->result();
    }

    /**
     * Get user field value.
     *
     * @return array
     */
    public function getUserField($field_name, $by_field, $field_value)
	{
		ci()->db->select($field_name . ' as field');
		ci()->db->from($this->user);
		ci()->db->where($by_field, $field_value);

		$result = ci()->db->get()->row();

		if (!empty($result))
			return $result->field;
		
		return null;
    }
    
    /**
     * Search user.
     *
     * @return array
     */
    public function searchUser($result = 'list', $status = 'all', $keyword = null, $limit = 10, $limit_order = 1)
	{
		if ($result == 'total')
			ci()->db->select($this->user . '.id');
		else
			ci()->db->select('*,' . $this->user . '.id as user_id,' . $this->user . '.status as status');
		
		ci()->db->from($this->user);
		ci()->db->join($this->role, $this->role . '.id = '. $this->user . '.role_id');
		
		ci()->db->like($this->user . '.name', $keyword);

		if (!empty($status) && $status != 'all')
		{
			ci()->db->where($this->user.'.status', $status);
		}

		ci()->db->where($this->user.'.status !=', 'deleted');
		
		if ($result == 'total')
			return ci()->db->get()->num_rows();

		ci()->db->order_by($this->user.'.id', 'desc');
		ci()->db->limit($limit, $limit_order);
		
		return ci()->db->get()->result();
    }

    /**
     * Get detail role by field.
     *
     * @param string $field
     * @param string $value
     * @return array
     */
    public function getRole($field, $value)
    {
        ci()->db->select('*');
        ci()->db->from($this->role);
        ci()->db->where($this->role .'.'. $field, $value);
        
        $result = ci()->db->get()->row_array();

        if (!empty($result))
            return $result; 
        
        return [];
    }

    /**
     * Get privileges by role.
     *
     * @param int $id
     * @return array
     */
    public function getRolePrivileges($role_id, $grouped = false)
	{
        return ci()->Role_model->getRolePrivileges($role_id, $grouped);
    }

    /**
     * Get all roles.
     *
     * @return array
     */
    public function getRoles()
	{
        ci()->db->select('*');
        ci()->db->from($this->role);
        ci()->db->where($this->role . '.status', 'active');
        ci()->db->where($this->role . '.status !=', 'deleted');

        // Don't deliver Super to non Super
        if(isLoggedIn('role_name') != 'Super')
            ci()->db->where($this->role . '.role_name !=', 'Super');
        
        return ci()->db->get()->result();
    }

    /**
     * Search roles.
     *
     * @return array
     */
    public function searchRole($result = 'list', $keyword = 'Super')
	{
        if ($result == 'total')
            ci()->db->select('id');
        else
            ci()->db->select('*');             

        ci()->db->from($this->role);
        ci()->db->where($this->role . '.status', 'active');
        ci()->db->where($this->role . '.status !=', 'deleted');
        ci()->db->like($this->role . '.role_name', $keyword);
        
        if ($result == 'total')
            return ci()->db->get()->num_rows();

        return ci()->db->get()->result();
    }


    /**
     * Get role privilege detail
     *
     * @return array
     */
    public function getPrivilege($field, $value)
    {
        ci()->db->select('*');
        ci()->db->from($this->privileges);
        ci()->db->where($this->privileges .'.'. $field, $value);
        
        $result = ci()->db->get()->row_array();

        if (!empty($result))
            return $result; 
        
        return [];
    }

    // Get privileges by module name
    // if $grouped, data will be grouped per module
    // @return associative array of privileges per modules
    public function getModulePrivileges($module = 'all')
    {
        $modules = config_item('modules');

        if($module != 'all'){
            if($modules[$module]['enable'] == true)
            {
                $privileges = $modules[$module]['privileges'] ?? [];
                if(isset($modules[$module]['setting']) && !empty($modules[$module]['setting']))
                    $privileges[] = 'settings';
                return $privileges;
            } else {
                return [];
            }
        }

        $data= [];
        foreach ($modules as $module => $detail) {
            if($modules[$module]['enable'] == true)
            {
                $data[$module] = $detail['privileges'] ?? [];
                if(isset($detail['setting']) && !empty($detail['setting']))
                    $data[$module][] = 'settings';
            }
        }

        return $data;
    }

    // Get privileges by module name
    // if $grouped, data will be grouped per module
    // @return associative array of privileges per modules
    public function getEntryPrivileges($entry = 'all')
    {
        $entries = config_item('entries');
        
        if($entry != 'all'){
            if($entries[$entry]['enable'] == true)
            {
                $privileges = $entries[$entry]['privileges'] ?? [];
                if(isset($entries[$entry]['setting']) 
                    && !empty($entries[$entry]['setting']))
                    $privileges[] = 'settings';
                return $privileges;
            } else {
                return [];
            }
        }

        $data= [];
        foreach ($entries as $entry => $detail) {
            if ($entries[$entry]['enable'] == true)
            {
                $data[$entry] = $detail['privileges'] ?? [];
                if(isset($detail['setting']) && !empty($detail['setting']))
                   $data[$entry][] = 'settings';
            }
        }

        return $data;
    }

    /**
     * Get all privileges
     *
     * @return array
     */
    public function getPrivileges()
	{
        ci()->db->select('*');
        ci()->db->from($this->privileges);
        
        return ci()->db->get()->result();
    }

    /**
     * Search privileges
     *
     * @return array
     */
    public function searchPrivilege($keyword)
	{
        ci()->db->select('*');
        ci()->db->from($this->privileges);
        ci()->db->like($this->privileges . '.permission', $keyword);
        
        return ci()->db->get()->result();
    }
    
    /**
     * Get current user active credential (logged in)
     *
     * @return array
     */
    public function getCredential()
    {
        $credential['fullname'] = isLoggedIn('fullname');
        $credential['username'] = isLoggedIn('username');
        $credential['email'] = isLoggedIn('email');
        $credential['profile_picture'] = isLoggedIn('profile_picture');

        if (!empty($credential))
            return $credential;
    }

    /**
     * Get current user profile picture using gravatar.
     *
     * @return string
     */
    public function getProfilePicture($filename = null)
    {
        if(! $filename)
            $filename = base_url('views/theme/assets/images/default-avatar.png');
        else
            $filename = base_url('uploads/'.$_ENV['SITENAME'].'/entry_files/'.$filename);
        
        return $filename;
	}

    /**
     * Let's activate user from token
     *
     * @param string $token
     * @return array
     */
    public function activate($token)
    {
        ci()->db->select('id, email');
        ci()->db->from($this->user);
        ci()->db->where($this->user . '.token', $token);
        $result = ci()->db->get()->row_array();
        
        if (!empty($result))
        {
            ci()->db->where('id', $result['id']);
            ci()->db->update($this->user, ['status' => 'active', 'token' => '', 'otp' => '']);
            
            // $this->forceLogin($result['id']); Let user login himself
            
            return ['status' => 'success', 'message' => 'Akun Anda telah diaktifkan.', 'user_id' => $result['id'], 'email' => $result['email']];
        }

        return ['status' => 'failed', 'message' => 'Token aktivasi tidak valid.', 'status_code' => REST_Controller::HTTP_UNAUTHORIZED];
    }

    public function validateActivationToken($token)
    {
        return $this->getUser('token', $token);
    }

    public function validateOTP($token, $otp)
    {
        $user = ci()->User_model
                    ->where('token', $token)
                    ->where('otp', $otp)
                    ->get();

        return $user;
    }

    public function activateByOTP($token, $otp)
    {
        $user = $this->validateOTP($token, $otp);
        if($user) return $this->activate($token);

        return ['status' => 'failed', 'message' => 'OTP salah atau token tidak valid.', 'status_code' => REST_Controller::HTTP_UNAUTHORIZED];
    }

    // Prepare otp and token for resetting password
    public function resetPassword($email)
    {
        $otp = $this->generateOTP();
        $token = $this->generateActivationToken($email);


        ci()->load->library('form_validation');
        ci()->form_validation->set_data(['email' => $email]);
        ci()->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if (ci()->form_validation->run() == FALSE)
        {
            return ['status' => 'failed', 'message' => validation_errors()];
        }

        $res = $this->updateUser(['email' => $email], ['token' => $token, 'otp' => $otp], null, true);

        if($res['status'] == 'success')
            return ['status'=>'success', 'otp' => $otp, 'token' => $token];

        return ['status' => 'failed', 'message' => 'Alamat email tidak ditemukan.'];
    }


    /**
     * Change password.
     *
     * @param string $token
     * @param string $password
     * @param string $confirm_password
     * @return array
     */
    public function changePassword($token, $password, $confirm_password)
    {
        if ($password != $confirm_password)
            return ['status' => 'failed', 'message' => 'New Password is not match with confirm password field'];

        if (strlen($password) < 8)
            return ['status' => 'failed', 'message' => 'Password should be at least 8 characters'];

        if (empty($password) || empty($confirm_password))
            return ['status' => 'failed', 'message' => 'New Password and confirm password must be filled!'];

        // Is token exist
        if (!$this->isExist('token', $token))
            return ['status' => 'failed', 'message' => 'Token is not exist / expired'];

        // Go!
        $this->updateUser(['token' => $token], ['password' => ci()->phpass->HashPassword($password), 'token' => '', 'status' => 'active'], null, true);
        
        return ['status' => 'success', 'message' => 'Successfully change password! Login please.'];   
    }

    /**
     * Recovery business model.
     *
     * @param string $email
     * @return array
     */
    public function recovery($email, $sendEmail = true)
    {
        // Dependency
        ci()->load->helper('string');

        // Is email exist?
        $user = $this->getUser('email', $email);
        if (!$user)
            return ['status' => 'failed', 'message' => 'Email tidak terdaftar di dalam sistem.'];
        
        // Reset token.
        $token = $this->generateActivationToken($email);
        
        // Update token
        ci()->db->where('email', $email);
        ci()->db->update($this->user, ['token' => $token]);
        
        // Kirim email recovery password lewat email.
        // Send email activation
        if($sendEmail)
        {
            $message  = 'Hai '.$user['name'].'!<br/><br/>';
            $message .= 'Sepertinya kamu meminta perubahan password. Satu langkah lagi, silahkan mengklik tautan dibawah ini!<br/>';
            $message .= '<a href="'. site_url('user/change_password?token=' . $token) .'">'. site_url('user/change_password?token=' . $token) .'</a>';
            $message .= '<br/><br/>Salam,<br/>'.ci()->shared['site_title'];

            ci()->load->helper('email');
            sendEmail($email, 'Permintaan Reset Password', [], null, $message);
            return ['status' => 'success', 'message' => 'Tautan untuk mengganti password sudah dikirim ke email.'];
        
        } elseif(isset($_ENV['WOOWA_LICENSE'])) {
            $waResult = $this->sendWAResetPassword($user['phone'], $token);

            if (strpos(strtolower($waResult), 'number not found') !== false)
                return ['status' => 'failed', 'message' => 'Nomor WhatsApp tidak valid.'];
            elseif(strpos(strtolower($waResult), 'success') === false)
                return ['status' => 'failed', 'message' => 'Error sending WhatsApp reset password.'];
            
            return ['status' => 'success', 'message' => 'Tautan untuk mengganti password sudah dikirim ke WhatsApp.'];
        }
        
    }

    /**
     * Login
     *
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function login($username, $password)
    {
        // Detect di DB lokal
        ci()->db->select('*,' . $this->user . '.id as user_id,' . $this->user . '.email as email', $this->role . '*');
        ci()->db->join($this->role, $this->role . '.id = '. $this->user . '.role_id');
        ci()->db->join($this->profile, $this->profile . '.user_id = ' . $this->user . '.id', 'left');
        ci()->db->from($this->user);

        ci()->db->where($this->user . '.status', 'active');
        ci()->db->where($this->user . '.email', $username);

        // Username mode included?
        unset($this->login_mode[array_search('email', $this->login_mode)]);
        foreach ($this->login_mode as $mode) {
            ci()->db->or_where($this->user . '.status', 'active');
            ci()->db->where($this->user . '.'. $mode, $username);
        }
        
        $result = ci()->db->get()->row_array();

        if (empty($result))
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Akun Anda tidak terdaftar atau tidak aktif.</div>'];

        // Check password using CheckPassword instead of compare in sql
        if(ci()->phpass->CheckPassword($password, $result['password']) === false)
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Password tidak cocok. Mungkin kamu perlu <a href="'.site_url('user/recovery').'">reset password?</a></div>'];

        if ($result['status'] == 'inactive')
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Sepertinya akun kamu belum diaktifkan, coba cek email untuk aktifasi.</div>'];

        // Lalu generate session bersama cookie JWTnya
        $this->setJWTSession($result);

        // Update last login.
        $this->updateUser(['id' => $result['user_id']], ['last_login' => date('Y-m-d H:i:s')], null, true);

        $result = ci()->event->trigger('Ci_auth.login', $result);
        
        return ['status' => 'success', 'message' => 'Welcome back!', 'credential' => $result]; 
    }

    public function login_as($username, $password, $login_as)
    {
        // Detect di DB lokal
        ci()->db->select('*,' . $this->user . '.id as user_id,' . $this->user . '.email as email');
        ci()->db->join($this->role, $this->role . '.id = '. $this->user . '.role_id');
        ci()->db->join($this->profile, $this->profile . '.user_id = ' . $this->user . '.id', 'left');
        ci()->db->from($this->user);
        
        // Username mode included?
        if (in_array('username', $this->login_mode)) 
        {
            ci()->db->where($this->user . '.username', $username);
            ci()->db->where($this->user . '.status', 'active');
        }

        ci()->db->or_where($this->user . '.email', $username);
        ci()->db->where($this->user . '.status', 'active');
        
        $result = ci()->db->get()->row_array();

        if (empty($result))
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Email tidak terdaftar, atau akun kamu tidak aktif.</div>'];

        // check password using CheckPassword instead of compare in sql
        if(ci()->phpass->CheckPassword($password, $result['password']) === false)
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Password tidak cocok. Mungkin kamu perlu <a href="'.site_url('user/recovery').'">reset password?</a></div>'];

        if ($result['status'] == 'inactive')
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Sepertinya akun kamu belum diaktifkan, coba cek email untuk aktifasi.</div>'];

        // Get another userdata
        if($result['role_name'] != 'Super')
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">Hanya admin yang dapat login sebagai orang lain.</div>'];

        $user = $this->getUser('email', $login_as, 'active');
        if(!$user)
            return ['status' => 'failed', 'message' => '<div class="alert alert-danger">User yang ingin dilihat tidak aktif.</div>'];
        
        // Lalu generate session beserta JWTnya
        $this->setJWTSession($user);

        // Update last login.
        $this->updateUser(['id' => $user['id']], ['last_login' => date('Y-m-d H:i:s')], null, true);
        
        return ['status' => 'success', 'message' => 'Welcome back!', 'credential' => $user]; 
    }

    /**
     * Register
     *
     * Name, username, email, password, confirm password.
     * 
     * @param array $param
     * @return array
     */
    public function register($param, $profile = [], $sendEmail = true)
    {
        // Dependency
        ci()->load->library('form_validation');
        ci()->load->helper('string');

        // Filter dulu.
        ci()->form_validation->set_data(array_merge($param, $profile));
        ci()->form_validation->set_rules('name', 'Name', 'required|alpha_numeric_spaces');
        ci()->form_validation->set_rules('username', 'Username', 'required|alpha_dash');
        ci()->form_validation->set_rules('email', 'Email', 'required|valid_email');
        ci()->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        ci()->form_validation->set_rules('phone', 'Phone', 'numeric');
        ci()->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        
        if (ci()->form_validation->run() == FALSE)
        {
            return ['status' => 'failed', 'message' => validation_errors()];
        }

        if ($this->isEmailExist($param['email']))
            return ['status' => 'failed', 'message' => 'Email sudah digunakan. Silakan login atau reset password bila lupa.'];
        
        if ($this->isExist('username', $param['username']))
            return ['status' => 'failed', 'message' => 'Username is used by other user'];

        if (isset($param['phone']) && $this->isPhoneExist($param['phone']))
            return ['status' => 'failed', 'message' => 'No. telepon sudah digunakan. Silakan login atau reset password bila lupa.'];

        // Generate token and OTP
        $otp = $this->generateOTP();
        $tokenActivation = $this->generateActivationToken($param['email']);

        ci()->db->trans_start();
        ci()->db->insert($this->user, [
            'name' => $param['name'],
            'status' => 'inactive',
            'username' => $param['username'],
            'email' => $param['email'],
            'phone' => $param['phone'] ?? '' ?? '',
            'password' => ci()->phpass->HashPassword($param['password']),
            'token' => $tokenActivation,
            'otp' => $otp,
            'referrer_code' => $param['referrer_code'] ?? '',
            'role_id' => $param['role_id'] ?? 3,
            'created_at' => date('Y-m-d H:i:s')
        ]);
            
        // Get id.
        $id = ci()->db->insert_id();
        
        // Update profile
        $this->updateProfile($id, $profile);
        ci()->db->trans_complete();
         
        return ['status' => 'success', 'message' => 'Pendaftaran berhasil, silahkan cek email untuk '.(setting_item('user.confirmation_type')=='otp' ? 'kode OTP.' : 'tautan aktivasi.'), 'id' => $id, 'name' => $param['name'], 'email' => $param['email'], 'phone' => $param['phone'] ?? '', 'token' => $tokenActivation, 'otp' => $otp];
    }

    public function generateActivationToken($email)
    {
        $data['token'] = random_string('alnum', 6);
        $data['email'] = $email;
        $encoded = sha1(json_encode($data));
        return $encoded;
    }

    public function generateOTP()
    {
        do {
            $otp = random_string('numeric', 6);
        } while($this->isExist('token',$otp));

        return $otp;
    }

    public function updateOTP($user_id)
    {
        $user = $this->getUser('id',$user_id,'inactive');
        if(!$user)
            return ['status'=>'failed', 'message'=>'User baru non-aktif untuk dikirimi OTP tidak ditemukan', 'status_code' => REST_Controller::HTTP_NOT_FOUND];

        $user['otp'] = $this->generateOTP();

        ci()->db->where('id',$user_id)->update($this->user, ['otp'=>$user['otp']]);
        if(! ci()->db->affected_rows())
            return ['status' => 'failed', 'message' => 'Error updating user\'s OTP.', 'status_code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR];

        return ['status' => 'success', 'message' => 'OTP updated', 'user' => $user];
    }

    public function sendWAConfirmation($phone, $token, $use_otp = false)
    {
        ci()->load->library('user/Woowa');
        
        if($use_otp)
            $template_message = config_item('message_confirm_register_otp');
        else{
            $template_message = config_item('message_confirm_register');
            $token = site_url('conf/' . $token);
        }

        $randomIndex = mt_rand(0,count($template_message)-1);

        $data['message'] = [
            $template_message[$randomIndex],
            [
                'title' => ci()->shared['settings']['site.site_title'],
                'token' => $token
            ]
        ];

        return ci()->woowa->setAsync(1, 180)->sendMessage($phone, $data);
    }

    public function sendWAResetPassword($phone, $token)
    {
        ci()->load->library('user/Woowa');
        $template_message = config_item('message_reset_password');
        $randomIndex = mt_rand(0,count($template_message)-1);

        $data['message'] = [
            $template_message[$randomIndex],
            ['url' => $token]
        ];
        return ci()->woowa->setAsync(2, 180)->sendMessage($phone, $data);
    }

    /**
     * Sync
     *
     * Sync registered user.
     * 
     * @param string $source_id
     * @param string $name
     * @param string $username
     * @param string $email
     * @param string $role_id
     * @return array
     */
    public function sync($source_id, $name, $username, $email, $role_id = 3)
    {
        $now = date('Y-m-d H:i:s');

        if ($this->isExist('email', $email))
        {
            $user = ci()->ci_auth->getUser('email', $email);
            
            // Force Login
            $this->forceLogin($user['id']);

            return true;
        }

        // Register him!
        ci()->db->insert($this->user, [
            'name' => $name,
            'status' => 'active',
            'username' => $username,
            'email' => $email,
            'password' => '',
            'token' => '',
            'role_id' => $role_id,
            'source_id' => $source_id,
            'created_at' => $now
        ]);

        $id = ci()->db->insert_id();

        // Update profile
        $this->updateProfile($id, [
            'phone' => '-',
            'address' => '-'
        ]);
        
        // Force Login
        $this->forceLogin($id);
        
        return true;
    }

    /**
     * Logout, destroy session
     *
     * @return bool
     */
    public function logout()
    {
        // Clear session_id
        if($this->loggedInUser)
            ci()->User_model->where('id', $this->loggedInUser['id'])->update(['session_id' => '']);      

        // Clear Session
        ci()->session->unset_userdata(['logged_in','user_id','email','session_token_id']);
        return true;
    }

    /**
     * Force Login
     *
     * Untuk meloginkan dan generate session orang langsung tanpa login. Modal id usernya saja.
     *
     * @param int $user_id
     * @return mixed
     */
    public function forceLogin($id)
    {
        // Detect di DB lokal
        ci()->db->select('*,' . $this->user . '.id as user_id,'. $this->user . '.status as status');
        ci()->db->join($this->role, $this->role . '.id = '. $this->user . '.role_id');
        ci()->db->join($this->profile, $this->profile . '.user_id = ' . $this->user . '.id', 'left');
        ci()->db->from($this->user);
        ci()->db->where($this->user . '.id', $id);
        
        $result = ci()->db->get()->row_array();

        if (empty($result))
            return false;
        
        // Update last login and set to temporary account if inactive
        $update['last_login'] = date('Y-m-d H:i:s');
        if($result['status'] == 'inactive')
            $update['status'] = 'temporary';
        $this->updateUser(['id' => $result['user_id']], $update, [], true);

        // Lalu generate session
        $this->setJWTSession($result);

        $result = ci()->event->trigger('Ci_auth.login', $result);

        return true;
    }

    /**
     * Checking current role active or not.
     *
     * @param array $roles
     * @return bool
     */
    public function isRoleOn($roles)
    {
        $current = isLoggedIn('role_name');
        
        if (in_array($current, $roles))
            return true; 
        
        return false;
    }

    public function isLoggedIn($key = null)
    {
        if($key && !empty($this->loggedInUser)){
            if(!isset($this->loggedInUser[$key]))
                throw new Exception("Undefined user data key '$key'.");

            return $this->loggedInUser[$key];
        } 

        return $this->loggedInUser;
    }

    /**
     * Is current user has valid logged in?
     *
     * @return mixed
     */
    public function isValidLogin()
    {
        if (ci()->session->userdata('logged_in'))
        {
            // Cek token.
            $user = $this->getUser('id', ci()->session->userdata('user_id'));
            
            // Kalau data user tidak ditemukan (karena dihapus mungkin), atau tidak aktif, maka logout
            if (empty($user) || in_array($user['status'], ['inactive','deleted'])){
                $this->logout();
                return [];
            }
            
            if(setting_item('user.use_single_login') === '1' 
                && strcmp($user['session_id'] ?? '', ci()->session->userdata('session_token_id') ?? '') !== 0) {
                $this->logout();
                ci()->session->set_flashdata('message', '<div class="alert alert-warning">Kamu telah login di perangkat lain. Sesi pada perangkat ini otomatis berakhir.</div>');
                redirect('auth/login');
            }

            return $user;
        }

        return [];
    }

    /**
     * Checking is username/email/etc exist ..
     *
     * @param string $field
     * @param string $value
     * @return bool
     */
    public function isExist($field, $value, $table = null)
    {
        if(empty(trim($value)))
            return false;

        if(!$table)
            $table = $this->user;

        ci()->db->select('id');
        ci()->db->from($table);
        ci()->db->where($table . '.' . $field, $value);
        
        $result = ci()->db->get()->num_rows();

        if ($result > 0)
            return true; 

        return false;
    }

    /**
     * Checking is username/email/etc exist ..
     *
     * @param string $field
     * @param string $value
     * @return bool
     */
    public function isPhoneExist($number)
    {
        if(empty(trim($number)))
            return false;

        // Prepare variasi nomor dengan prefix 0 dan prefix 62
        $numbers = $this->_prepPhoneNumberOptions($number);

        ci()->db->select($this->user.'.id');
        ci()->db->from($this->user);
        ci()->db->where($this->user.'.status', 'active');
        ci()->db->where_in($this->user.'.phone', $numbers);
        
        $result = ci()->db->get()->num_rows();

        if ($result > 0)
            return true; 

        return false;
    }

    /**
     * Checking is username/email/etc exist ..
     *
     * @param string $field
     * @param string $value
     * @return bool
     */
    public function isEmailExist($email)
    {
        if(empty(trim($email)))
            return false;

        ci()->db->from($this->user);
        ci()->db->where($this->user.'.email', $email);        
        ci()->db->where($this->user.'.status', 'active');        
        $result = ci()->db->get()->num_rows();

        if ($result > 0)
            return true; 

        return false;
    }

    private function _prepPhoneNumberOptions($number)
    {
        $number = ltrim($number, '62');
        $number = ltrim($number, '0');
        
        return [
            '62'.$number,
            '0'.$number,
        ];
    }

    /**
     * Checking is current user allowed to action.
     *
     * @return boolean
     */
    public function isPermitted($permission, $module = null, $whitelist = [])
    {
        if(in_array(ci()->session->user_id, $whitelist))
            return true;
        
        // Allow all privilege to superuser
        if(isLoggedIn('role_id') == 1)
            return true;

        // Check if permission bring the module name with dot separator
        if(strpos($permission, '.') !== false)
            list($module, $permission) = explode('.', $permission);

        // is user has access to the module
        if(!isset($this->currentUserPrivileges[$module]) || ! in_array($permission, $this->currentUserPrivileges[$module]))
            return false;

        return true;
    }

    /**
     * Change user status
     *
     * Inactive, active, deleted
     * 
     * @return bool
     */
    public function changeUserStatus($id, $status)
	{
		return ci()->db->update($this->user, ['status' => $status], ['id' => $id]);
	}

    /**
     * Change role status
     *
     * Deleted, active
     * 
     * @return bool
     */
    public function changeRoleStatus($id, $status)
	{
		return ci()->db->update($this->role, ['status' => $status], ['id' => $id]);
	}

    /**
     * Update Role
     * 
     * @return array
     */
    public function updateRole($condition, $param)
    {
        ci()->db->where($condition);
        ci()->db->update($this->role, $param);
        
        return ['status' => 'success', 'message' => 'Successfully updated.'];
    }

    /**
     * Remove Role Privileges
     * 
     * @return bool
     */
    public function removeRolePrivilege($id)
    {
        ci()->db->delete($this->role_privileges, ['id' => $id]);
        
        return true;
    }
    
    /**
     * Update User
     * 
     * @return array
     */
    public function updateUser($condition = [], $param = [], $profile = [], $force = false)
    {
        if ($force)
        {
            ci()->db->where($condition);
            ci()->db->update($this->user, $param);
            $res = ci()->db->affected_rows();
            
            if($res)
                return ['status' => 'success', 'message' => 'Successfully updated.'];
            else
                return ['status' => 'failed', 'message' => 'Updating failed.'];
        }

        // Dependency
        ci()->load->library('form_validation');
        
        // Filter dulu.
        ci()->form_validation->set_data($param);
        ci()->form_validation->set_rules('name', 'Name', 'required')
                             ->set_rules('email', 'Email', 'required|valid_email');
        
        if (!empty($param['password']))
        {
            ci()->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            ci()->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        }

        if (ci()->form_validation->run() == FALSE)
        {
            return ['status' => 'failed', 'message' => validation_errors()];
        }

        if ($this->isExist('username', $param['username']))
		{
			$current_username = $this->getUserField('username', 'id', $param['user_id']);

			if ($param['username'] != $current_username)
			{
				return ['status' => 'failed', 'message' => 'Username is used by others'];
			}
        }
        
        if ($this->isExist('email', $param['email']))
		{
			$current_email = $this->getUserField('email', 'id', $param['user_id']);

			if ($param['email'] != $current_email)
			{
				return ['status' => 'failed', 'message' => 'Email is used by others'];
			}
        }
        
        // Update password is setted.
        if (!empty($param['password']))
        {
            ci()->db->where($condition);
            ci()->db->update($this->user, ['password' => ci()->phpass->HashPassword($param['password'])]);
        }

        // Prepare usedata
        $userdata = [
            'name' => $param['name'],
            'username' => $param['username'],
            'email' => $param['email'],
            'phone' => $param['phone'] ?? '',
            'status' => $param['status'],
            'avatar' => $param['avatar'] ?? '',
            'url' => $param['url'] ?? '',
            'short_description' => $param['short_description'] ?? '',
        ];

        if($param['role_id'] ?? '')
            $userdata['role_id'] = $param['role_id'];

        // Update master.
        ci()->db->where($condition);
        ci()->db->update($this->user, $userdata);
        
        // Update profile.
        if (!empty($profile))
            $this->updateProfile($param['user_id'], $profile);
        
        return ['status' => 'success', 'message' => 'Successfully updated.'];
    }

    /**
     * Update Profile
     * 
     * @return array
     */
    public function updateProfile($user_id, $sets)
    {
        $ProfileModel = setup_entry_model('user_profile');

        $record = $ProfileModel->where('user_id', $user_id)->get();
        if (!empty($record))
        {
            if($ProfileModel->where('user_id',$user_id)->update($sets))
                return true;

            return false;
        }

        $sets['user_id'] = $user_id;
        if($ProfileModel->insert($sets))
            return true;

        return false;
    }

    public function hardDeleteUser($id)
    {
        ci()->db->where('id', $id)->delete($this->user);
        ci()->db->where('user_id', $id)->delete($this->profile);
        return true;
    }

    /**
     * Insert
     *
     * Insert role to DB.
     * 
     * @return array
     */
    public function insertRole($param)
	{
        $param['created_at'] = date('Y-m-d H:i:s');
        
        ci()->db->insert($this->role, $param);
           
        return ['status' => 'success', 'message' => 'Successfully inserted.', 'role_id' => ci()->db->insert_id()];
    }

    /**
     * Insert
     *
     * Insert privileges to DB.
     * 
     * @return array
     */
    public function insertPrivileges($param)
	{
        // Check first.
        ci()->db->select('id');
        ci()->db->from($this->privileges);
        ci()->db->where($this->privileges . '.permission', $param['permission']);
        $result = ci()->db->get()->num_rows();
        
        if (empty($result))
        {
            $param['created_at'] = date('Y-m-d H:i:s');
            
            ci()->db->insert($this->privileges, $param);

            return ['status' => 'success', 'message' => 'Successfully added.'];
        }

        return ['status' => 'failed', 'message' => 'That permission is exist.'];
    }

    /**
     * Insert
     *
     * Insert user to DB.
     * 
     * @return bool
     */
    public function insertUser($param, $profile, $force = false)
	{
        if ($force)
        {
            ci()->db->insert($this->user, $param);
            $this->updateProfile(ci()->db->insert_id(), $profile);
            
            return true;
        }

        // Dependency
        ci()->load->library('form_validation');

        // Do validation.
        ci()->form_validation->set_data($param);
		ci()->form_validation->set_rules('name', 'Name', 'required')
                                  ->set_rules('username', 'Username', 'required|is_unique[mein_users.username]')
                                  ->set_rules('email', 'Email', 'required|is_unique[mein_users.email]')
                                  ->set_rules('role_id', 'Role', 'required')
                                  ->set_rules('password', 'Password', 'required')
                                  ->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        
        if (ci()->form_validation->run() == false)
		{
			return ['status' => 'failed', 'message' => validation_errors()];
        }
        
		ci()->db->insert($this->user, [
            'name' => $param['name'],
            'username' => $param['username'],
            'email' => $param['email'],
            'phone' => $param['phone'] ?? '',
            'password' => ci()->phpass->HashPassword($param['password']),
            'status' => $param['status'] ?? 'inactive',
            'role_id' => $param['role_id'],
            'avatar' => $param['avatar'],
            'url' => $param['url'],
            'short_description' => $param['short_description'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $user_id = ci()->db->insert_id();
        
        // Update profile
        $this->updateProfile($user_id, $profile);
        
		return ['status' => 'success', 'message' => 'Successfully added', 'user_id' => $user_id];
	}

    /**
     * Set Token
     *
     * To set login identity
     * 
     * @return bool
     */
    public function setJWTSession($user = [])
    {
        if($user) {
            $userSession = [
                'logged_in' => true,
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'timestamp' => time()
            ];
        } else if (ci()->session->logged_in ?? '') {
            $userSession = [
                'logged_in' => true,
                'user_id' => ci()->session->user_id,
                'email' => ci()->session->email,
                'timestamp' => time()
            ];
        } else {
            return false;
        }
        
        // You can add another session index with this event
        ci()->event->trigger('Ci_auth.setJWTSession', $userSession);

        // Set session
        ci()->session->set_userdata($userSession);

        // Generate JWT
        $this->jwt = JWT::encode($userSession, config_item('jwt_key'), 'HS256');

        $this->updateUser(['id' => $userSession['user_id']], ['session_id' => $this->jwt], null, true);

        // Place JWT to session
        ci()->session->set_userdata('session_token_id', $this->jwt);
        
        return true;
    }

}
