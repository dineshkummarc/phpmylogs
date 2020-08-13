<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LogViewerController extends CI_Controller {

private $logViewer;

public function __construct() {

       parent::__construct();
        // load the library
		$this->load->library('CILogViewer');
		
    $this->logViewer = new CILogViewer();
}

public function index() {
    echo $this->logViewer->showLogs();
    return;
}
}