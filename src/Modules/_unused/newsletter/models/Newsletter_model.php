<?php

/**
 * Newsletter
 *
 * @author Oriza
 */

class Newsletter_model extends CI_Model
{
	protected $main_table = 'newsletter';
	
	public function __construct()
	{
		parent::__construct();
	}

    public function getMembers($result = 'data', $status = 'all', $limit = 10, $limit_order = 1)
	{
		if ($result == 'total')
			$this->db->select($this->main_table . '.id');
		else
			$this->db->select('*,' . $this->main_table . '.id as user_id');
		
		$this->db->from($this->main_table);
		
		if (!empty($status) && $status != 'all')
		{
			$this->db->where($this->main_table.'.status', $status);
		}

		$this->db->where($this->main_table.'.status !=', 'deleted');
		
		if ($result == 'total')
			return $this->db->get()->num_rows();
        
		$this->db->order_by($this->main_table.'.id', 'desc');
		$this->db->limit($limit, $limit_order);
		
		return $this->db->get()->result();
    }

    public function getToken($token)
	{
        // Get member by token.
        $this->db->select('*');
        $this->db->from($this->main_table);
        $this->db->where('token', $token);
        
        $token = $this->db->get()->row();

        if (empty($token))
            return ['status' => 'failed', 'message' => 'Token is not exist / expired'];

        // Reset token
        $this->db->where('token', $token->token);
        $this->db->update($this->main_table, ['token' => '', 'status' => 'valid']);
        
        return ['status' => 'success', 'message' => 'Token exist'];
    }

    /**
     * Json
     *
     * @return mixed
     */
    public function outputJSON($param)
    {
        echo json_encode($param);
        exit;
    }

    /**
     * Search
     *
     * @return array
     */
    public function searchMember($result = 'data', $status = 'all', $keyword = null, $limit = 10, $limit_order = 1)
	{
		if ($result == 'total')
			$this->db->select($this->main_table . '.id');
		else
			$this->db->select('*,' . $this->main_table . '.id as user_id');
		
		$this->db->from($this->main_table);
		
		$this->db->like($this->main_table . '.email', $keyword);

		if (!empty($status) && $status != 'all')
		{
			$this->db->where($this->main_table.'.status', $status);
		}

		$this->db->where($this->main_table.'.status !=', 'deleted');
		
		if ($result == 'total')
			return $this->db->get()->num_rows();

		$this->db->order_by($this->main_table.'.id', 'desc');
		$this->db->limit($limit, $limit_order);
		
		return $this->db->get()->result();
    }
    
    /**
     * Send email
     * 
     * @return bool
     */
    public function sendEmail($param)
    {
        $token = random_string('alnum', 10);
        
        // Save user as member ..
        $this->db->insert($this->main_table, [
            'name' => $param['name'],
            'email' => $param['email'],
            'phone' => '',
            'status' => 'not valid',
            'token' => $token,
            'created_at' => date('y-m-d h:i:s')
        ]);
        
        $message  = 'Hai ' . $param['name'] . '!<br/><br/>';
        $message .= 'Bagaimana kabarnya? Terimakasih telah berlangganan email Codepolitan. Silahkan klik link dibawah ini untuk menyelesaikan langkah<br/>';
        $message .= '<a href="'. site_url('newsletter/verify/' . $token) .'">'. site_url('newsletter/verify/' . $token) .'</a><br/><br/>';
        $message .= 'Salam,<br/>Codepolitan';
        
        $this->load->library('email', [
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_port' => 587,
            'smtp_user' => 'azure_ebd8980d48588ac38bbbf04fe10b9361@azure.com',
            'smtp_pass' => 'Nyanazure890*()',
            'mailtype'  => 'html', 
            'charset'   => 'iso-8859-1'
        ]);
        
        $this->email->set_newline("\r\n");
        $this->email->from('info@codepolitan.com', 'Codepolitan Info');
        $this->email->to($param['email']); 
        $this->email->subject('Codepolitan Info - Subscription Confirmation');
        $this->email->message($message);  
        
        return $this->email->send();
    }
}