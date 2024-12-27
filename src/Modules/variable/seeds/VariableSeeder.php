<?php

class VariableSeeder extends App\libraries\Seeder
{
	public $table = 'mein_users';

    public function run()
    {
        $this->db->truncate($this->table);

        $data = [
            [
                `name`      => 'Mimin',
                `email`     => 'slickkitten@mailinator.com',
                `username`  => 'admin',
                `password`  => '$P$BoFGu4UhAf//eE9r/C.H0IT9rGsVZA1',
                `status`    => 'active',
                `role_id`   => 1,
            ],
            [
                `name`      => 'The Member',
                `email`     => 'themember@mailinator.com',
                `username`  => 'admin',
                `password`  => '$P$BoFGu4UhAf//eE9r/C.H0IT9rGsVZA1',
                `status`    => 'active',
                `role_id`   => 2,
            ],
        ];

        $this->db->insert_batch($this->table, $data);
    }
}