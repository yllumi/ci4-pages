<?php

use App\core\Frontend_Controller;

class User extends Frontend_Controller {

	public function __construct()
	{
        parent::__construct();
        
        $this->load->library('ci_auth');

        $this->entry_profile = get_entry_config('user_profile',false,false);
    }
    
    /**
     * Redirect to login page.
     */
    public function index()
    {
        // Just redirect to login page.
        redirect('login');
    }

    // Login as another user, with admin authentication 
    public function login_as($key = false)
    {
        if($key != '1234567890qwertyuiop')
            show_404();

        // Do not show form login if user already login
        if(isLoggedIn())
        {
            // if user has logged in, redirect to callback
            if($this->session->callback)
                redirect($this->session->callback);
            else
                redirect('user/dashboard');
        }

        // Catch POST login form
        if($this->input->post())
        {
            // Init
            $callback = $this->session->callback;
            $this->session->unset_userdata('callback');

            $username = $this->input->post('username', true);
            $password = $this->input->post('password');
            $loginas = $this->input->post('loginas', true);
            
            // Start logic
            $login = $this->ci_auth->login_as($username, $password, $loginas);

            if (in_array($login['status'], ['failed','inactive']))
            {
                $this->session->set_flashdata('message', $login['message']);

                if (!empty($callback))
                    redirect('login_as?callback=' . $callback);

                redirect('login_as');
            }

            if (!empty($callback)) redirect($callback);
            
            redirect('user/dashboard');
        }

        $this->shared['action_url'] = site_url('user/login_as/'.$key);
        $this->shared['page_title'] = "Login As..";

        $this->load->render('login_as');
    }

    /**
     * Show register form.
     */
    public function register()
	{
        // Inject dependency
        $this->load->library('Recaptcha');

        // Do not show form register if user already login
        if(isLoggedIn())
            redirect('user/dashboard');

        $this->shared['action_url'] = site_url('user/register_action');
        $this->shared['page_title'] = "Register";
        $this->shared['captcha'] = $this->recaptcha->getWidget();
        $this->shared['script_captcha'] = $this->recaptcha->getScriptTag();

        $this->load->render('register');
    }
    
    /**
     * Register action handler.
     */
    public function register_action()
    {
        // Inject dependency
        $this->load->library('Recaptcha');

        $post = $this->input->post(null, true);

        foreach($post as $p => $value)
            $this->session->set_flashdata($p, htmlspecialchars($value));

        $response = $this->recaptcha->verifyResponse($post['g-recaptcha-response'] ?? '');

        if ($_ENV['CI_ENV'] == 'production' && empty($response['success']))
        {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Recaptcha is empty /not valid, please fill reCaptcha correcly.</div>');
            redirect('user/register');
        }

        // Set username from email if not provided
        $username = $this->input->post('username') 
                    ?? preg_replace( '/[\W]/', '', $post['email']);
        
        $register = $this->ci_auth->register([
            'name' => htmlspecialchars($post['name']),
            'username' => htmlspecialchars($username).random_string('alnum', 5),
            'email' => htmlspecialchars($post['email']),
            'password' => $this->input->post('password'),
            'confirm_password' => $this->input->post('confirm_password'),
            'role_id' => 3
        ],
        [
            'phone' => $this->input->post('phone'),
        ]
        );
        
        if ($register['status'] == 'success')
        {
            $this->session->set_flashdata('message', '<div class="alert alert-success">Satu langkah lagi, cek email kamu untuk aktifasi.</div>');
            redirect('login');
        }

        $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $register['message'] .'</div>');
        redirect('user/register');
    }

    public function setReferrer()
    {
        $seg1 = $this->uri->segment(1);
        
        // Deny direct access from default 
        // module route (user/referral/index)
        if($seg1 == 'user')
            show_404();

        if($seg1 == 'ref')
            $referrer = $this->uri->segment(2);
        else
            $referrer = substr($seg1, 1);

        // Set referrer to cookie
        $this->ci_auth->setCookieReferrer($referrer);

        redirect('/');
    }

    /**
     * Show dashboard page.
     */
	public function dashboard()
	{
        // Prevent access for non-login user
        if(! isLoggedIn())
            redirect('login');
        
        $this->shared['page_title'] = isLoggedIn('fullname') . '&#39;s Dashboard';
        
        $this->load->render('dashboard');
	}

    /**
     * Show change avatar.
     */
    public function edit_avatar()
	{
        $this->shared['page_title'] = "Edit Avatar";
        
        $this->load->render('edit_avatar');
    }

    /**
     * Show edit profile form.
     */
	public function edit_profile()
	{
        // Prevent access for non-login user
        if(! isLoggedIn())
            redirect('login');

        $data['profile_entry'] = $this->entry_profile;

        // Show form.
        $this->shared['page_title'] = isLoggedIn('name') . '\'s Edit Profile';
        $this->shared['result'] = $this->ci_auth->getUser('id', isLoggedIn('user_id'));
        
        $this->load->render('edit_profile', $data);
    }
    
    /**
     * Edit profile action handler
     */
    public function edit_profile_action()
	{
        // Prevent access for non-login user
        if(! isLoggedIn())
            redirect('login');

        if (!$this->input->post()) {
            redirect('user/edit_profile');
        }

        $post = $this->input->post(null, true);

        $profileField = array_keys($this->entry_profile['fields']);
        $profileValue = [];

        foreach($post as $p => $value){
            $this->session->set_flashdata($p, $value);
            if(in_array($p, $profileField)){
                $profileValue[$p] = $value;
            }
        }
        
        $user_id = isLoggedIn('user_id');
        $userValue = [
            'user_id' => $user_id,
            'name' => htmlspecialchars($post['name']),
            'username' => htmlspecialchars($post['username']),
            'email' => isLoggedIn('email'),
            'password' => $this->input->post('password'),
            'confirm_password' => $this->input->post('confirm_password'),
            'role_id' => isLoggedIn('role_id'),
            'short_description' => $post['short_description'],
            'avatar' => $post['avatar'],
            'status' => 'active'
        ];

        $updateUser = $this->ci_auth->updateUser(['id' => $user_id], $userValue, $profileValue);
        
        if ($updateUser['status'] == 'failed')
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $updateUser['message'] .'</div>');
        else
            $this->session->set_flashdata('message', '<div class="alert alert-success">'. $updateUser['message'] .'</div>');    
        
        redirect('user/edit_profile');
    }

    /** 
     * Profile page for public access :) 
     */
	public function profile($username = null, $page = 1)
	{
        $user = $this->ci_auth->getUser('username', $username ?? isLoggedIn('username'));

        if (!$user)
            show_404();
        
        $this->shared['page_title'] = ucwords($user['name']);
        $this->shared['profile'] = $user;
        
        $this->load->render('profile');
    }

    /**
     * Show password form.
     */
    public function change_password($success = null)
	{
        if($success)
            return $this->load->render('change_password_success');

        $token = $this->input->get('token', true);

        if (!$token || !$this->ci_auth->isExist('token', $token))
            show_404();

        $this->shared['action_url'] = site_url('user/change_password_action');
        $this->shared['token'] = $token;
        $this->shared['page_title'] = "Change Password";
        
        $this->load->render('change_password');
    }

    /**
     * Handling password change.
     */
    public function change_password_action()
	{
        $post = $this->input->post(null, true);
        
        $change = $this->ci_auth->changePassword($post['token'], $this->input->post('new_password'), $this->input->post('confirm_new_password'));

        if ($change['status'] == 'success')
        {
            $this->session->set_flashdata('message', '<div class="alert alert-success">'. $change['message'] .'</div>');
            redirect('user/change_password/success');
        }
        
        $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $change['message'] .'</div>');
        redirect('user/change_password?token=' . $post['token']);
    }

    /**
     * Show recovery page.
     */
    public function recovery()
	{
        $this->shared['action_url'] = site_url('user/recovery_action');
        $this->shared['page_title'] = "Recovery Password";
        
        $this->load->render('recovery');
    }
    
    /**
     * Handle recovery action
     */
    public function recovery_action()
	{
        $this->load->library('form_validation');

        $post = $this->input->post(null, true);
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
            redirect('user/recovery');
        }
        
        $recovery = $this->ci_auth->recovery(htmlspecialchars($post['email']));
        
        if ($recovery['status'] == 'success')
        {
            $this->session->set_flashdata('message', '<div class="alert alert-success">'. $recovery['message'] .'</div>');
            redirect('user/recovery');
        }

        $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $recovery['message'] .'</div>');

        redirect('user/recovery');
    }

    /**
     * Activate user by token.
     */
    public function confirm_activation()
    {
        $this->shared['page_title'] = 'Konfirmasi Aktivasi';

        $token = $this->input->get('token', true);

        $user = $this->ci_auth->validateActivationToken($token);
        if(!$user) show_404();

        $this->load->render('confirm_activation', compact('user','token'));
    }

	public function activate()
	{
        $token = $this->input->get('token', true);

        $activate = $this->ci_auth->activate($token);

        if ($activate['status'] == 'success')
        {
            $this->session->set_flashdata('message', '<div class="alert alert-success">'. $activate['message'] .'</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $activate['message'] .'</div>');
        }

        redirect('user/activated');
    }

    /**
     * Show this page if account activated 
     */
    public function activated()
    {
        $this->load->render('activated');
    }
    
    /**
     * Logout link.
     */
    public function logout()
    {
        $this->ci_auth->logout();
        redirect('/');
    }

    /**
     * Custom Dashboard Link
     * 
     * Ini akan mengarah ke theme yang aktif ..
     */
    public function page($page = null)
	{
        // Prevent access for non-login user
        if(! isLoggedIn())
            redirect('login');
        
        $this->shared['page_title'] = isLoggedIn('name') . '&#39;s ' . ucfirst($page);
        
        // Mengecek ada tidaknya file theme tersebut
        if (!file_exists($this->shared['theme_path'] . '/user/' . $page . '.html'))
            show_404();
        
        $this->load->render('' . $page);
    }

}
