<?php namespace App\modules\entry\models;

use App\libraries\Entity;

class EntryEntity extends Entity {

	private $entry;
	private $entryConf;
	private $entryData;

	public function __construct($entry, $data)
	{
		parent::__construct();

		$this->entry = $entry;
		$this->entryConf = config_item('entries')[$this->entry];
		$this->adjustData($data);

	}

	private function adjustData($data)
	{
		$show_on_api = $this->entryConf['show_on_api'] ?? [];

		foreach ($this->entryConf['fields'] as $field => $config) {
			if($show_on_api && !in_array($field, $show_on_api)) continue;
			$this->entryData[$field] = generate_output_api($config, $data);
		}

		$this->entryData = array_merge($data, $this->entryData);
	}

	public function asArray()
	{
		// return $this->entryConf;
		return $this->entryData;
	}

}