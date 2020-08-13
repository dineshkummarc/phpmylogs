<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_Parser {

	
	public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**     
     * Function to retrieve contents from a file
     * @access  public
     * @param 	String 	$path Path of the file
     * @param 	String 	$offset Page offset
     * @param 	String 	$limit Limit per page
     * @return 	Array   Array with columns line no, content
     */
	public function get_file_contents($path, $offset, $limit)
	{	
		$cols = array();
		$total_count = $this->get_contents_count($path);

		if($total_count > 0) {

			$file = $this->read_file($path);
			if(count($file) > 0) {
				for ($i = $offset; $i < $limit + $offset; $i++) {
					if($i < $total_count)
					{
						$cols[$i]['line_no'] = $i+1;
						$cols[$i]['content'] = $file[$i];
					}
				}
			}
		}
		
		return $cols;		
	}

	/**     
     * Function to retrieve row count of a file
     * @access  public
     * @param 	String 	$path Path of the file     
     * @return 	Int 	Count of rows
     */
	public function get_contents_count($path)
	{	
		return count($this->read_file($path));
	}

	/**     
     * Function to read given file path
     * @access  private
     * @param 	String 	$path Path of the file     
     * @return 	Array 	Array of file contents
     */
	private function read_file($path)
	{
		$file = array();
		$handle = @fopen($path, "r"); 
		if ($handle) { 

		   while (!feof($handle)) { 		   	
		   	   $line = fgets($handle, 4096); 
		   	   if(strlen(trim($line)) > 0) {
		   	   		$file[] = $line; 
		   	   }	
		   } 
		   fclose($handle); 
		}

		return $file;
	}
}
