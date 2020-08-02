<?php
defined('BASEPATH') OR exit('No direct script access allowed');

private $logViewer;

public function __construct() {
		
    $this->logViewer = new CILogViewer();
}

public function index() {
    echo $this->logViewer->showLogs();
    return;
}