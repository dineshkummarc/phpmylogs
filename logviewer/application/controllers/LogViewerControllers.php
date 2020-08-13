<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LogViewerControllers extends CI_Controller {

private $logViewer;

public function __construct() {

       parent::__construct();
        // load the library
		$this->load->library('CILogViewers');
		$this->logViewer = new CILogViewers();
}

public function index() {
    echo $this->logViewer->showLogs();
    return;
	
}
}