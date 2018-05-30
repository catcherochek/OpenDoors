<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
  
      <div class="pull-right">
        <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary save-changes"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success_message) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success_message; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <div>
			<p>Мой текст</p>
			
		</div>
        
          
        
      </div>
    </div>
  </div>
  </form>
</div>

<div id="progress-dialog" class="modal" data-backdrop="static" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content padding20">
      <div id="progressbar">
        <div class="progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
          </div>
        </div>
      </div>
      <div id="progressinfo"></div>
      <button class="btn btn-default finishActionButton" style="display: none;">Abort</button>
    </div>
  </div>
</div>

<script type="text/javascript">



</script>

<?php echo $footer; ?>