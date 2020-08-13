<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Log File Viewer</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/main.css">
	<style type="text/css">
	
	</style>
	<script src="<?php echo base_url(); ?>assets/js/jquery-1.11.0.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
	<script>
		var base_url = '<?php echo base_url(); ?>';
	</script>

	
</head>
<body>

<div id="container">	
	
	<h2>Log File Viewer</h2>
	
	<div class="panel panel-default">
		<div class="panel-body">
			<form id="form">
				<div class="form-group input-group form-file-group">
				   <input type="hidden" id="pageno" />
				   <input type="text" name="file" value="" id="file" class="form-control" placeholder="/path/to/file" required />
				   <span class="input-group-btn">
				        <button class="btn btn-primary" type="submit">View</button>
				   </span>
				</div>		
			</form>
			<div id="data-container"></div>
			<div id="data-loader"><img src="<?php echo base_url(); ?>assets/images/cube.gif"</div>
		</div>

	</div>
	
</div>

</body>
</html>