<?php

class UserSeeder extends App\libraries\Seeder {

	private $user_table = 'mein_users';
	private $profile_table = 'mein_user_profile';
	
	public function run()
	{
		ci()->load->library('user/phpass');

		$this->db->truncate($this->user_table);
		$this->db->truncate($this->profile_table);

		// Create admin user
		$user = [
        	'id'		=> 1,
            'name'      => 'Mimin',
            'email'     => 'admin@admin.com',
            'username'  => 'admin',
            'password'  => ci()->phpass->HashPassword('12345'),
            'status'    => 'active',
            'role_id'   => 1,
            'created_at'=> date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->user_table, $user);
        $profile = [
        	'user_id'	=> 1,
            'phone'     => '08987654321',
            'address'   => 'Jl. Cijeungjing Padalarang Bandung Barat',
            'created_at'=> date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->profile_table, $profile);

        // Create normal user
		$user = [
        	'id'		=> 2,
            'name'      => 'Test User',
            'email'     => 'test@test.com',
            'username'  => 'test',
            'password'  => ci()->phpass->HashPassword('12345'),
            'status'    => 'active',
            'role_id'   => 1,
            'created_at'=> date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->user_table, $user);
        $profile = [
        	'user_id'	=> 2,
            'phone'     => '08981234567',
            'address'   => 'Jl. Cijeungjing Padalarang Bandung Barat',
            'created_at'=> date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->profile_table, $profile);

        // Role seeder
        $this->db->query(
        "INSERT INTO `mein_roles` (`id`, `role_name`, `status`, `created_at`) VALUES
        (1,	'Super',	'active',	'2013-05-13 10:32:53'),
        (2,	'Writer',	'active',	'2013-05-13 10:32:53'),
        (3,	'Member',	'active',	'2013-05-13 10:32:53'),
        (4,	'Admin',	'active',	'2020-12-28 11:56:37');");
	}
}