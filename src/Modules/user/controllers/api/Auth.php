<?php

use App\core\REST_Controller;

class Auth extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('user/ci_auth');
    }

    public function login()
    {
        $this->setMethod('post');

        $data = json_decode(ci()->input->raw_input_stream, true);
        if(! $data){
            $data['email'] = $this->input->post('email', true);
            $data['password'] = $this->input->post('password');
        }

        $login = $this->ci_auth->login($data['email'], $data['password']);

        if (in_array($login['status'], ['failed','inactive'])) {
            $login['message'] = strip_tags($login['message']);
            $this->response($login, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $this->response(["status" => "success", "token" => $this->ci_auth->jwt]);
    }

    public function logout()
    {
        $this->ci_auth->logout();
        $this->response('Loogut success.');
    }

    public function get_jwt()
    {
        $jwt = $this->ci_auth->getJWT();
        if($jwt) $this->response(["token" => $this->ci_auth->getJWT()]);

        $this->response("Session not exists. Please login first.", REST_Controller::HTTP_UNAUTHORIZED);
    }

    public function register()
    {
        $this->setMethod('post');

        $data = json_decode(ci()->input->raw_input_stream, true);
        if(! $data) $data = $this->input->post(null, true);

        $data['username'] = $data['username'] ?? preg_replace('/[\W]/', '', $data['email']);

        $user = [
            'name' => $data['name'],
            'username' => htmlspecialchars($data['username']) . random_string('alnum', 5),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password'  => $data['password'],
            'confirm_password' => $data['confirm_password'],
            'role_id' => 3
        ];
        $userFields = array_keys($user);

        // Backward compatibility, will DEPRECATED
        if ($data['nomorhp'] ?? '') {
            $user['phone'] = $data['nomorhp'];
            unset($data['nomorhp']);
        }

        // Make sure phone begin with country code
        if (substr($user['phone'], 0, 1) == '0')
            $user['phone'] = '62' . substr_replace($user['phone'], '', 0, 1);

        $profile = array_filter($data, function($var) use ($userFields) {
            return !in_array($var, $userFields);
        }, ARRAY_FILTER_USE_KEY);

        $register = $this->ci_auth->register($user, $profile);

        if ($register['status'] == 'failed') {
            $register['message'] = strip_tags($register['message']);
            $this->response($register, REST_Controller::HTTP_UNAUTHORIZED);
        }
        
        if(setting_item('user.confirmation_type') == 'otp')
            $this->sendOTP($register);
        // TODO: Send email for option send link activation    

        $this->response($register);
    }

    private function sendOTP($data)
    {
        if(setting_item('user.otp_mode') == 'email')
        {
            ci()->load->helper('email');
            sendEmail(
                [$data['email'], $data['name']], 
                "Konfirmasi Registrasi", 
                [], null, 
                ci()->load->render('pages/auth/register/email_otp.html', $data, true));
        }

        elseif(in_array(setting_item('user.otp_mode'), ['WASender','Woowa','ZenzivaWA']))
        {
            $sender = new App\modules\notifier\libraries\Sender(setting_item('user.otp_mode'));
            $message = $this->getRandomOTPMessage();
            $message = str_replace('{otp}',$data['otp'], $message);
            $res = $sender->sendText($data['phone'], $message);
        }
    }

    private function getRandomOTPMessage()
    {
        $firstMessage = "Anda telah mendaftar di aplikasi ".setting_item("site.site_title")."\n";
        $message_confirm_register_otp = [
            "Masukkan kode berikut untuk mengkonfirmasi pendaftaran: {otp}",
            "Silakan aktifkan akun Anda dengan memasukkan kode berikut di halaman konfirmasi: {otp}",
            "Satu langkah lagi, masukkan kode berikut untuk mengaktifkan akun: {otp}",
            "Aktifkan akun dengan memasukkan kode berikut ini: {otp}",
            "Terima kasih sudah mendaftar, aktifkan akun dengan memasukkan kode ini: {otp}",
            "Pendaftaran akun berhasil. Silakan aktifkan akun dengan memasukkan kode berikut: {otp}",
        ];

        return $firstMessage.$message_confirm_register_otp[array_rand($message_confirm_register_otp)];
    }

    public function reset_password()
    {
        $this->setMethod('post');

        $email = $this->input->post('email', true);

        $recovery = $this->ci_auth->recovery($email);

        if ($recovery['status'] == 'failed') {
            $recovery['message'] = strip_tags($recovery['message']);
            $this->response($recovery, REST_Controller::HTTP_UNAUTHORIZED);
        }
        
        $this->response($recovery);
    }

    public function resend_otp($user_id)
    {
        $data = ci()->ci_auth->updateOTP($user_id);
        if($data['status'] == 'failed') $this->response($data);

        $this->sendOTP($data['user']);
        $this->response([
            "status" => "success", 
            "message" => "OTP resent.", 
            "name" => $data['user']['name'],
            "email" => $data['user']['email'],
            "phone" => $data['user']['phone'],
            "token" => $data['user']['token'],
            "otp" => $data['user']['otp']
        ]);
    }

    public function confirm_otp()
    {
        $this->setMethod('post');

        $token = ci()->input->post('token', true);
        $otp = ci()->input->post('otp', true);

        // Get token based on otp for backward compatibility
        if(!$otp) {
            $otp = $token;
            $user = $this->db->from('mein_users')->where('otp',$otp)->get()->row_array();
            $token = $user['token'] ?? '';
        }

        $activated = ci()->ci_auth->activateByOTP($token, $otp);
        $this->response($activated);
    }

    public function reset_user_test()
    {
        if($_ENV['CI_ENV'] == 'production')
            $this->response('Not found', self::HTTP_NOT_FOUND);

        $email = $this->input->post('email', true);
        $user = $this->ci_auth->getUser('email', $email);
        
        if(!$user)
            $this->response('User not found', self::HTTP_NOT_FOUND);

        $res = $this->ci_auth->hardDeleteUser($user['id']);
        if($res)
            $this->response('User deleted.');
        else
            $this->response('Fail to delete, probably user not exist.');
    }
}
