<?php

use App\core\Backend_Controller;
use Symfony\Component\Yaml\Yaml;

class Migration extends Backend_Controller {

	public $module_location;

	private $enabledModules = [];
	private $enabledModuleConfig = SITEPATH.'configs/enabled_modules.yml';

	function __construct()
	{
		parent::__construct();
		$this->load->library('parser');
		$this->load->library('migration');

		$this->module_location = realpath(__DIR__.'/../..');

		if(!file_exists(SITEPATH.'configs/')){
			mkdir(SITEPATH.'configs', 0777, true);
			file_put_contents($this->enabledModuleConfig, Yaml::dump([]));
		}

		$this->enabledModules = Yaml::parseFile($this->enabledModuleConfig);

		$this->shared['submodule'] = 'migration';
	}

	private function checkAccessKey()
	{
		// use special key for migration in production server
		if(ENVIRONMENT == 'production')
			if(!isset($_GET['key']) || $_GET['key'] != '1234567890qwertyuiop') show_error("use key to migrate in production mode");
	}

	public function index($module = 'CI_core')
	{
		$data['has_migration'] = [];
		foreach ($this->migration->list_all_modules_with_migrations() as $value){
			$data['has_migration'][$value[1]] = $value;
		}

		$data['current'] = $this->migration->display_current_migrations();
		$data['migrations'] = $this->migration->display_all_migrations();
		$data['module'] = $module;
		$data['all_modules'] = modules_list();

		// Seeder data
		if($module != 'CI_core')
		{
			$data['seed']['className'] = ucfirst($module).'Seeder';
			$data['seed']['file'] = $data['seed']['className'].'.php';
			$seedPath = $data['all_modules'][$module]['path'].'seeds/'.$data['seed']['file'];
			$data['seed']['exists'] = file_exists($seedPath);
			if($data['seed']['exists']){
				require($seedPath);
				$seedClass = new $data['seed']['className'];
				$data['seed']['methods'] = $seedClass->seeds ?? '';
			}
		}

		$this->view('migration/index', $data);
	}

	public function generateMigration($name, $module = 'CI_core')
	{
		$this->checkAccessKey();

		$modules = [];
		// set key first for easy choosing
		foreach (modules_list() as $module_path) {
			$modules[$module_path[1]] = $module_path;
		}

		// set migration and config folder path
		if($module == 'CI_core'){
			$config_path = APPPATH.'config/';
			$migration_path = APPPATH.'migrations/';
		} else {
			$config_path = realpath($modules[$module][0].$modules[$module][1]).'/config/';
			$migration_path = realpath($modules[$module][0].$modules[$module][1]).'/migrations/';
		}

		// get current version
		$nextVersion = ($this->migration->display_current_migrations()[$module] ?? 0) + 1;
		if($nextVersion < 10)
			$nextVersion = '00'.$nextVersion;
		elseif($nextVersion < 100)
			$nextVersion = '0'.$nextVersion;
		
		// check config folder existence
		if (!file_exists($config_path))
			mkdir($config_path, 0755, true);
		
		// Write Migration config file template
		$filename = 'migration.php';
		$config_path = $config_path.$filename;
		if(! file_exists($config_path)){
			$config_file = file_get_contents($this->module_location.'/template/migration_config');

			if(file_put_contents($config_path, $config_file) === false){	
				$this->session->set_flashdata('message', '<div class="alert alert-warning">Migration config failed to create. Please check folder permission</div>');
				redirect('admin/development/migration/index/');
			}
		}
			
		// check migration folder existence
		if (!file_exists($migration_path))
			mkdir($migration_path, 0755, true);

		// Set Migration file template file and path
		$name = strtolower(str_replace([' ','-'], '_', $name));
		$classname = 'Migration_'.ucfirst($name);
		$filename = $nextVersion.'_'.$name.'.php';
		$path = $migration_path.$filename;
		if(file_exists($path))
			die('Migration already exists.');

		$template = file_get_contents($this->module_location.'/template/migration');
		$parsed = $this->parser->parse_string($template, ['name'=>$classname], true);

		if(file_put_contents($path, $parsed) === false)
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Migration $filename failed to create. Please check module/application folder permission</div>');
		else
			$this->session->set_flashdata('message', '<div class="alert alert-success">Migration $filename created.</div>');

		redirect('admin/development/migration/index/'.$module);
	}

	public function generateSeeder($module = 'CI_core')
	{
		$this->checkAccessKey();

		$modules = [];
		// set key first for easy choosing
		foreach (modules_list() as $module_path) {
			$modules[$module_path[1]] = $module_path;
		}

		// set migration and config folder path
		if($module == 'CI_core'){
			$seed_path = APPPATH.'seeds/';
		} else {
			$seed_path = $modules[$module]['path'].'seeds/';
		}

		// check migration folder existence
		if (!file_exists($seed_path))
			mkdir($seed_path, 0755, true);

		// Set Migration file template file and path
		$classname = ucfirst($module).'Seeder';
		$filename = $classname.'.php';
		$path = $seed_path.$filename;
		if(file_exists($path)) die('Seed already exists.');

		$template = file_get_contents($this->module_location.'/template/seed');
		$parsed = $this->parser->parse_string($template, ['name'=>$classname], true);

		if(file_put_contents($path, $parsed) === false)
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Migration '.$filename.' failed to create. Please check module/application folder permission</div>');
		else
			$this->session->set_flashdata('message', '<div class="alert alert-success">Seed '.$filename.' created.</div>');

		redirect(getenv('HTTP_REFERER'));
	}

	function runSeeder($module, $method = 'run')
	{
		if(config_item('modules')[$module] ?? ''){
			$classname = ucfirst($module).'Seeder';
			$path = config_item('modules')[$module]['path'].'seeds/'.$classname.'.php';
			if(! file_exists($path))
				show_error('Seeder file not found');

			require_once($path);
			$seeder = new $classname;
			$seeder->{$method}();

			$this->session->set_flashdata('message', '<div class="alert alert-success">Seeder run successfully.</div>');
			redirect('admin/development/migration/index/'.$module);
		}

		show_error('Module not found');
	}

	function migrate($module = 'CI_core', $version = 0)
	{
		$this->checkAccessKey();

		$this->load->library('migration');

		if ($this->migration->init_module($module)) {
			$this->migration->version($version);
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Migrating '.$module.' to version '.$version.' succeed.</div>');

		if($module == 'CI_core' && $version === 0)
			redirect('development/install');
		else
			redirect('admin/development/migration/index/'.$module);
	}

	public function migrateAll()
	{
		$this->checkAccessKey();

		$this->load->library('migration');

		$this->migration->migrate_all_modules('latest', true);
		$this->session->set_flashdata('message','<div class="alert alert-success">All module has migrated to their latest version.</div>');
		
		redirect(getenv('HTTP_REFERER'));
	}

	public function enable($module)
	{
		$this->checkAccessKey();

		$this->enabledModules[] = $module;

		file_put_contents($this->enabledModuleConfig, Yaml::dump($this->enabledModules));
		redirect(getenv('HTTP_REFERER'));
	}

	public function disable($module)
	{
		$this->checkAccessKey();

		$index = array_search($module, $this->enabledModules);
		unset($this->enabledModules[$index]);

		file_put_contents($this->enabledModuleConfig, Yaml::dump($this->enabledModules));
		redirect(getenv('HTTP_REFERER'));
	}

	public function clear_cache($cacheName = null)
	{
		if (cache()->delete($cacheName . '*'))
			$this->session->set_flashdata('message', '<div class="alert alert-success">Cache cleared.</div>');

		redirect(getenv('HTTP_REFERER'));
	}
}
