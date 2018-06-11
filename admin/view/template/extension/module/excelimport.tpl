<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mymodul" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mymodul" class="form-horizontal">
          <div class="form-group">
          
            <div id="div_export" class ="div_blocks">
            	<H3 style="color: black; font-style: italic;text-align: center; text-decoration: underline; "  >Export</H3>
            	<div id="btn_export" class = "btn_div">Press me</div><p> here we get progress</p>
            </div>
            <div id = "div_import" class = "div_blocks">
            	<H3 style="color: black; font-style: italic;text-align: center; text-decoration: underline; " >Import</H3>
            	<div id="btn_import"class = "btn_div">Press me</div>
            	<p> here we get progress</p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">

$(window).load(function() {
	  
	  var vars = window.location.search.split('&');
	  for (var i = 0; i < vars.length; i++) {
   	var parts = vars[i].split('=');
  		if (parts[0] == 'token') token = parts[1];}  
 });
	var type;
	var request;
	//alert("Wrong username");
	// $("#div_export p").html("text comes from JS and this from PHP:");
	$("#btn_export").click(function(){
		var person = {
	            name: "klim",
	            address:"astreet",
	            phone:"aphone"
	        }
	    $.ajax({
	    	type: 'post',
	    	
		    url: "index.php?token=" + token + "&route=extension/module/excelimport/ajaxexport", 
		    dataType: 'text',
		    contentType: 'application/json; charset=utf-8',
		    data: person,
		    success: function(result){
		    	;
		    	$("#div_export p").html("text comes from JS and this from PHP:"+result);
	   		},
	   		error: function(jqXHR, textStatus, errorThrown){
	            alert('error' + textStatus +  errorThrown);
	        }         
   		});
	   // $("#div_export p").html("text comes from JS");
	
	});
	$("#btn_import").click(function(){
		var person = {
	            name: "klim",
	            address:"astreet",
	            phone:"aphone"
	        }
	    $.ajax({
	    	type: 'post',
	    	
		    url: "index.php?token=" + token + "&route=extension/module/excelimport/ajaximport", 
		    dataType: 'text',
		    contentType: 'application/json; charset=utf-8',
		    data: person,
		    success: function(result){
		    	;
		    	$("#div_import p").html("text comes from JS and this from PHP:"+result);
	   		},
	   		error: function(jqXHR, textStatus, errorThrown){
	            alert('error' + textStatus +  errorThrown);
	        }         
   		});
	   // $("#div_export p").html("text comes from JS");
	
	});
</script>
<style>
<!--
.div_blocks{
float:none;
border:2px solid black;
margin:10px;
background-color: grey;
}
.btn_div{
float:left;
clear:both;
width: 100px;
background-color: yellow;
border:1px solid black;
margin:5px;
}
.div_blocks p{
float:both;
color:white;

}
.btn_div:HOVER {
	cursor: pointer;
	background-color: white;
}
-->
</style>
<?php echo $footer; ?>
