<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_telegram_bot_table extends CI_Migration 
{
    public $user_table = 'bot_telegram_users';
    public $chat_table = 'bot_telegram_chats';
	public $message_table = 'bot_telegram_messages';

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field("id");
        $this->dbforge->add_field("user_id INT UNSIGNED NOT NULL"); // codepolitan id
        $this->dbforge->add_field("botname varchar(100) NOT NULL DEFAULT 'anonymous'");
         $this->dbforge->add_field("first_name varchar(100) NOT NULL DEFAULT 'anonymous'");
        $this->dbforge->add_field("last_name varchar(100) NOT NULL DEFAULT 'anonymous'");
        $this->dbforge->add_field("username varchar(100) NOT NULL DEFAULT 'anonymous'");
        $this->dbforge->add_field("photo_url varchar(255)");
        $this->dbforge->add_field("auth_date int unsigned");

        $this->dbforge->create_table($this->user_table, TRUE, array('ENGINE' => 'InnoDB'));

        
        $this->dbforge->add_field("id");
        $this->dbforge->add_field("tg_user_id int unsigned");
        $this->dbforge->add_field("title varchar(50)");
        $this->dbforge->add_field("type varchar(20) NOT NULL DEFAULT 'private'");
        $this->dbforge->add_field("botname varchar(100) NOT NULL DEFAULT 'anonymous'");        
        $this->dbforge->create_table($this->chat_table, TRUE, array('ENGINE' => 'InnoDB'));

        $this->dbforge->add_field("id");
        $this->dbforge->add_field("chat_id int NOT NULL");
        $this->dbforge->add_field("message_id int UNSIGNED NOT NULL");
        $this->dbforge->add_field("date int UNSIGNED NOT NULL");
        $this->dbforge->add_field("forward_from text");
        $this->dbforge->add_field("reply_to_message text");
        
        $this->dbforge->add_field("message_type VARCHAR(10) NOT NULL DEFAULT 'text'"); // text, audio, document, animation, game, photo, sticker, video, voice, video_note, contact, location, venue, 

        $this->dbforge->add_field("message_content text");
        $this->dbforge->add_field("caption VARCHAR(255)");

        $this->dbforge->create_table($this->message_table, TRUE, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->dbforge->drop_table($this->message_table, TRUE);
        $this->dbforge->drop_table($this->chat_table, TRUE);
        $this->dbforge->drop_table($this->user_table, TRUE);
    }

}