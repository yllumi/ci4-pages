<?php

class PostModel extends MY_Model
{
	// Define table name
	public $table = 'mein_posts';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];
}
