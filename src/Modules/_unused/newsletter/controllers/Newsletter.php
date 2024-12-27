<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
 * Newsletter
 * 
 * Newsletter module for Mein CMS.
 * 
 * @author Oriza
 */
class Newsletter extends Frontend_Controller 
{
    public $path;
    
	public function __construct()
	{
        parent::__construct();
        
        $this->load->helper('download');
        $this->load->model('Newsletter_model');
        
        $this->path = './uploads/sources/admin/';
    }
    
    /**
     * Verify
     * 
     * @return mixed
     */
    public function verify($token)
    {
        $token = $this->Newsletter_model->getToken($token);
        
        if ($token['status'] == 'failed')
            $this->Newsletter_model->outputJSON($token);
        
        redirect('finish');
    }

    /**
     * Send Email
     * 
     * @return mixed
     */
    public function send_email()
    {
        // Inject dependency
        $this->load->library('Recaptcha');

        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $recaptcha_response = $this->input->post('g-recaptcha-response');
        
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Request is not valid.</div>');
            redirect('#form-newsletter');
        }

        // Recaptcha Checking ..
        if (!isset($recaptcha_response)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Recaptcha is empty/not valid</div>');
            redirect('#form-newsletter');
        }

        $response = $this->recaptcha->verifyResponse($recaptcha_response);

        if (empty($response['success']))
        {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Recaptcha is empty/not valid</div>');
            redirect('#form-newsletter');
        }

        // Go!
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
            redirect($_SERVER['HTTP_REFERER'] . '#form-newsletter');
        }

        $send_email = $this->Newsletter_model->sendEmail([
            'name' => $name,
            'email' => $email
        ]);
        
        if (!$send_email)
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to send Email</div>');
        else
            $this->session->set_flashdata('message', '<div class="alert alert-success">Berhasil mengirim email konfirmasi. Silahkan cek email kamu di '. $email .' untuk langkah terakhir berlangganan :)</div>');
        
        redirect(getenv('HTTP_REFERER') . '#form-newsletter');
    }
}