<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Log Controller
 *
 */
class Logmine extends CI_Controller {

    private $logViewer;

    public function __construct()
	{
        parent::__construct(); 
        $this->load->helper('url');
		$this->load->library('CI_Telescope');
        $this->logViewer = new CI_Telescope();
    }

	public function index()
	{
        echo $this->logViewer->show();
    }

    // --------------------------------------------------------------------

    public function get_last_logs()
    {
        echo $this->logViewer->get_last_logs();
    }
}
