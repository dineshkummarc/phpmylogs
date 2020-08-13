<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('Log_Parser');
	}

	private function IsFileExist($file)
	{
		if(!is_dir($file) && file_exists($file))
		{
			return true;	
		}
		return false;		
	}

	private function IsTotalRowCount($file)
	{
		$count = 0;
		if($this->IsFileExist($file)) {
			$count = $this->log_parser->get_contents_count($file);
		}		
		return $count > 0 ? true : false;
	}

	private function IfContentsExist($file)
	{
		if($this->IsTotalRowCount($file)) {
			$data = $this->log_parser->get_file_contents($file, 1, 10);
			if($data!=null && count($data) > 0 && $data[1]['content'])
				return true;
		}
		return false;
	}

	public function index()
	{
		$file = $this->input->get('file');
		$this->unit->run($this->IsFileExist($file), true, "Check if file exists"); 
		$this->unit->run($this->IsTotalRowCount($file), true, "Check if file has records");
		$this->unit->run($this->IfContentsExist($file), true, "Check if file returns content");
		$this->load->view('test/index.php');	
	}

}