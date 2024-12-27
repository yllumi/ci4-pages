<?php

use App\core\REST_Controller;

/**
 * Profile
 * 
 * Profile API handler
 * 
 * @author Gemblue
 */

use \Firebase\JWT\JWT;

class Profile extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('user/ci_auth');
    }

    /**
     * Sync Google with internal account
     */
    public function sync()
	{
        $input = file_get_contents("php://input");
        $post = json_decode($input, true);
        
        // Cari email dari google apakah sudah terdaftar? jika sudah balikan data user tersebut
        $user = $this->ci_auth->getUser('email', $post['email']);
        
        if ($user) 
        {
            $jwt = $this->generateJWT([
                'logged_in' => true,
                'user_id' => $user['user_id'],
                'email' =>  $user['email'],
                'username' =>  $user['username'],
                'fullname' =>  $user['name'],
                'role_name' =>  $user['role_name'],
                'role_id' =>  $user['role_id'],
                'timestamp' => time()
            ]);

            $this->response([
                'response_code' => REST_Controller::HTTP_OK,
                'response_message' => 'success', 
                'data' => ['token' => $jwt]
            ]);
        } 
        else 
        {
            // Jika gak ada, bikinkan akun otomatis! lalu loginkan JWT nya.
            $username = str_replace([".","@"],"", $post['email']);
            $password = $this->randomPassword();

            $register = $this->ci_auth->register([
                'name' => htmlspecialchars($post['name']),
                'username' => htmlspecialchars($username),
                'email' => htmlspecialchars($post['email']),
                'password' => $password,
                'confirm_password' => $password,
                'role_id' => 3
            ], [
                'phone' => null,
            ]);
            
            if ($register['status'] == 'success') {
                
                $jwt = $this->generateJWT([
                    'logged_in' => true,
                    'user_id' => $register['id'],
                    'email' => $register['email'],
                    'username' => $username,
                    'fullname' => $register['name'],
                    'role_name' => 'Member',
                    'role_id' => 3,
                    'timestamp' => time()
                ]);

                $this->response([
                    'response_code' => REST_Controller::HTTP_OK, 
                    'response_message' => 'success', 
                    'data' => ['token' => $jwt]
                ]);
            
            } else {

                $this->response(['status' => 'failed', 'message' => 'Terdapat kesalahan teknis, hubungi admin.'], HTTP_BAD_REQUEST);
            
            }
        }
    }

    public function index()
    {
        $user =  $this->checkToken();
        $profile = $this->ci_auth->getUser('id', $user->user_id);

        $this->response($profile);
    }
    
    public function detail($username = null)
    {
        $user = $this->ci_auth->getUser('username', $username);
        $point = $this->Point_model->getTotal($user['id']);
        $rank = $this->Rank_model->getRank($point);
        $courses = $this->Course_model->getCompletedCoursesID($user['id']);
        $certificates = $this->Log_model->getLogsByUser($user['id']);
        
        // Just get yang dibutuhin aja
        $result = [
            'name' => $user['name'],
            'username' => $user['username'],
            'avatar' => $this->ci_auth->getProfilePicture(null, $user['email'], 300),
            'short_description' => $user['short_description'],
            'portfolio' => $user['portfolio'],
            'user_url' => $user['user_url'],
            'interest' => $user['interest'],
            'point' => $point,
            'rank' => $rank,
            'courses' => $courses,
            'certificates' => $certificates,
        ];

        $this->response($result);
    }

    public function update()
    {
        $this->setMethod('post');

        // Check JWT.
        $this->user =  $this->checkToken();
        
        // Find current data.
        $user = $this->ci_auth->getUser('id', $this->user->user_id);

        $post = json_decode(ci()->input->raw_input_stream, true);
        if(! $post) $post = $this->input->post(null, true);

        $data['user'] = [
            'user_id' => $this->user->user_id,
            'avatar' => $post['profile_picture'] ?? '',
            'name' => $post['name'] ?? '',
            'username' => $user['username'],
            'email' => $post['email'] ?? $user['email'],
            'password' => $post['password'] ?? '',
            'confirm_password' => $post['confirm_password'] ?? '',
            'status' => $user['status']
        ];
        $userFields = array_keys($data['user']);

        $data['profile'] = array_filter($post, function($var) use ($userFields) {
            return !in_array($var, $userFields);
        }, ARRAY_FILTER_USE_KEY);

        try {
            $update = $this->ci_auth->updateUser(['id' => $this->user->user_id], $data['user'], $data['profile']);
        } catch (Exception $e){
            $this->response(['status' => 'failed', 'message' => $e->getMessage()], HTTP_BAD_REQUEST);
        }
        
        if ($update['status'] == 'failed') {
            $this->response(['status' => 'failed', 'message' => $update['message']], HTTP_BAD_REQUEST);
        } 
        
        $this->response(['status' => 'success', 'message' => 'Successfully updated']);
    }

    public function update_password()
    {
        $this->setMethod('post');

        // Check JWT.
        $this->user = $this->checkToken();
        
        // Find current data.
        $user = $this->ci_auth->getUser('id', $this->user->user_id);

        $post = json_decode(ci()->input->raw_input_stream, true);
        if(! $post) $post = $this->input->post(null, true);

        // Check if old password is same as inputed
        if(ci()->phpass->CheckPassword($post['old_password'], $user['password']) === false)
            $this->response(['status' => 'failed', 'message' => 'Password lama tidak cocok.'], HTTP_NOT_ACCEPTABLE);

        if(strcmp($post['password'], $post['confirm_password']) !== 0)
            $this->response(['status' => 'failed', 'message' => 'Konfirmasi password tidak sama.'], HTTP_NOT_ACCEPTABLE, HTTP_BAD_REQUEST);

        $data['password'] = ci()->phpass->HashPassword($post['password']);

        $update = $this->ci_auth->updateUser(['id' => $this->user->user_id], $data, null, true);
        
        if ($update['status'] == 'failed') {
            $this->response(['status' => ', HTTP_BAD_REQUESTfailed', 'message' => $update['message']], HTTP_FORBIDDEN);
        } 
        
        $this->response(['status' => 'success', 'message' => 'Successfully updated']);
    }

    private function generateJWT($payload) {
        
        /** Generate JWT */
        ci()->event->trigger('Ci_auth.setJWTSession', $payload);
        ci()->session->set_userdata($payload);
        $jwt = JWT::encode($payload, config_item('jwt_key'), 'HS256');
        $this->ci_auth->updateUser(['id' => $payload['user_id']], ['session_id' => $jwt], null, true);
        ci()->session->set_userdata('session_token_id', $jwt);
        
        if ($jwt) {
            return $jwt;
        }

        return null;
    }

    private function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        
        return implode($pass); //turn the array into a string
    } 
}