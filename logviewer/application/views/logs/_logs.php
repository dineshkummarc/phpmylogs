<?php if($result!=NULL && count($result) > 0) { ?>
  <div class="clear20"></div>
  <div class="total-records">
    Total Logs: <?php echo $total_count; ?>
  </div>
  <div class="table-responsive">

    <table class="table table-bordered">
      <thead>
        <tr>
          <td class="col-xs-1">Line No.</td>
          <td>Content</td>
        </tr>
        
      </thead>
      <tbody>
      <?php foreach($result as $key => $value) { ?>
        <tr>
          <td class="text-right"><?php echo $value["line_no"]; ?></td>
          <td><?php echo $value["content"]; ?></td>
        </tr>
      <?php } ?>
        
        
      </tbody>
    </table>
  </div>  
  
  <?php echo $this->ajax_pagination->create_links(); ?>        
  
<?php } else { ?>
  <div class="alert alert-danger form-file-group">No data available</div>
<?php } ?>
