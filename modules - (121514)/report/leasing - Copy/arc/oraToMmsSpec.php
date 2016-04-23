<?php
switch($_POST['action']){
	case "create":
		$checkFrom = $_POST['checkFrom'];
		$checkTo = $_POST['checkTo'];
		$invalidRange = $_POST['checkTo'] - $_POST['checkFrom'];
				
		if(empty($checkFrom)){
			echo "$().toastmessage('showErrorToast','Please input check number!');";
			return false;
		}
		if($checkTo != "" && $invalidRange < 0){
			echo "$().toastmessage('showErrorToast','Invalid check number range!<br>Check number from must be greater than check number to');";
			return false;
		}
		
		exit();
	break;
}
?>

<html>
	<head>
    	<!-- jQuery, Bootstrap -->
    	<link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap-responsive.css"/>
        <link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap-responsive.min.css"/>
        <!-- jQuery, Bootstrap -->
        
        <script src="../../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../../includes/bootbox/bootbox.js"></script>
		
		<script src="../../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
        
        <script type="text/javascript">
		function CreateTextfile(){
			$.ajax({
				url: 'oraToMms.php',
				type: 'POST',
				data: 'action=create&checkFrom='+$("#checkFrom").val()+'&checkTo='+$("#checkTo").val(),
				success: function(data){
					eval(data);
				}	
			});
		}
		
		function numericFilter(txb) {
		   txb.value = txb.value.replace(/[^0-9]/ig, "");
		}
		
		var next = 1;
		function addFormField(){
		var addto = "#field" + next;
		next = next + 1;
		var newIn = '<br /><br /><input autocomplete="off" class="span3" id="field' + next + '" name="field' + next + '" type="text" data-provide="typeahead" data-items="8">';
		var newInput = $(newIn);
		$(addto).after(newInput);
		$("#field" + next).attr('data-source',$(addto).attr('data-source'));
		$("#count").val(next);
		}
		</script>
        
        <style type="text/css">
		<!--
		input,
		textarea,
		select {
			padding: 3px;
			font: 900 1em Verdana, Sans-serif;
			font-size:11px;
			color: #333;
			background:#eee;
			border: 1px solid #ccc;
			margin:0 0 0px 0;
			width:700px%;
		}
		input:focus,
		textarea:focus,
		select:focus {
			background: #fff;
			border: 1px solid #999;
		}
		
		input,
		textarea {
		  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
		  -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
		  -ms-transition: border linear 0.2s, box-shadow linear 0.2s;
		  -o-transition: border linear 0.2s, box-shadow linear 0.2s;
		  transition: border linear 0.2s, box-shadow linear 0.2s;
		}
		input:focus,
		textarea:focus {
		  border-color: rgba(82, 168, 236, 0.8);
		  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
		  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
		  outline: 0;
		  outline: thin dotted \9;
		  /* IE6-9 */
		}
		.dvContainer{
			margin-left:10px;
		}
		#input-userName{
			margin-left:50px;
		}
		.control-group{
			margin-left:10px;
		}
		-->	
		</style>   
    </head>
    	<body>
            <input type="hidden" name="count" value="1" style="height:30px;"/>
            <div class="control-group" id="fields">
            	<b>Check Number :</b>
            <div class="controls" id="profs">
            <div class="input-append">
                <input autocomplete="off" class="span3" id="field1" name="prof1" type="text" placeholder="Check Number"
                data-provide="typeahead" data-items="8"  style="height:30px;" 
                data-source='["Aardvark","Beatlejuice","Capricorn","Deathmaul","Epic"]'/>
                <button id="b1" onClick="addFormField()" class="btn btn-info" type="button">+</button>
            </div>
            <br /><small>Press + to add another form field :)</small>
            </div>
            </div>
        </body>
</html>