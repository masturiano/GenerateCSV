<?php require_once "../../includes/phpfileuploader/phpuploader/include_phpuploader.php" ?>
<?php session_start(); ?>

<?php

ini_set('memory_limit','128M');
ini_set('max_execution_time','0');

include("../../includes/db.inc.php");
include("../../includes/common.php");


$dbqry = new dbHandler();

if(isset($_POST['testmatch']) AND $_POST['testmatch'] == "yes")
{
	
	exit();
}


?>

<html>
    <head>
        <title>Demo 1 - use SaveDirectory property</title>
        
        <script src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
        <link rel="stylesheet" type="text/css" href="../../includes/jquery/css/demo_page.css" />
    	<script type="text/javascript">
     
            function showSuccessToast() {
                $().toastmessage('showSuccessToast', "Success Dialog which is fading away ...");
            }
            function showStickySuccessToast() {
                $().toastmessage('showToast', {
                    text: 'Success Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'success',
                    closeText: '',
                    close: function () {
                        console.log("toast is closed ...");
                    }
                });
     
            }
            function showNoticeToast() {
                $().toastmessage('showNoticeToast', "Notice  Dialog which is fading away ...");
            }
            function showStickyNoticeToast() {
                $().toastmessage('showToast', {
                    text: 'Notice Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'notice',
                    closeText: '',
                    close: function () { console.log("toast is closed ..."); }
                });
            }
            function showWarningToast() {
                $().toastmessage('showWarningToast', "Warning Dialog which is fading away ...");
            }
            function showStickyWarningToast() {
                $().toastmessage('showToast', {
                    text: 'Warning Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'warning',
                    closeText: '',
                    close: function () {
                        console.log("toast is closed ...");
                    }
                });
            }
            function showErrorToast() {
                $().toastmessage('showErrorToast', "Error Dialog which is fading away ...");
            }
            function showStickyErrorToast() {
                $().toastmessage('showToast', {
                    text: 'Error Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'error',
                    closeText: '',
                    close: function () {
                        console.log("toast is closed ...");
                    }
                });
            }
			
			$('document').ready(function(){
				alert('adf'	);
				$( "#process_file").hide();
				$( "#process_file").dialog({
						width:550,
						height:250,
						modal: true,
						buttons: {
							Yes: function() {
								alert('yes');
								
							},
							No: function() {
								alert('no');
							}
						}
					});
			});
			
			
			
		</script>
    </head>
    <body>
        <div>
			<h2>Match BTF Files</h2>
        
			<input type="button" name="matchtest" value="TEST" id="matchtest" /> 
			<input type="button" name="matchproceed" value="UPDATE" id="matchproceed" /> 
		
        </div>
		
        <div id="process_file" title="Importing Files to tables">
			<p>Initialize Processing of Data Files to tables.</p>
		    <p>Proceed ?</p>
		</div>
    </body>
</html>