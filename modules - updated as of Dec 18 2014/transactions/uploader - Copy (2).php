<?php require_once "../../includes/phpfileuploader/phpuploader/include_phpuploader.php" ?>
<?php session_start(); ?>

<?php
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("uploaderObj.php");

$uploaderObj = new uploaderObj();

switch($_POST['action']){
	case "import":
			
			//database connection details
			//$connect = mssql_connect('localhost','root','');
			
			//if (!$connect) {
			//die('Could not connect to MySQL: ' . mysql_error());
			//}	
			
			//your database name
			//$cid = mssql_select_db('dbccdcms',$connect);

			// path where your CSV file is located
			define(CSV_PATH,'savefiles\\');
			
			$csv_file = CSV_PATH . $_POST['fileName']; 
			
			if (($getfile = fopen($csv_file, "r")) !== FALSE) {
				$data = fgetcsv($getfile);
				$num = count($data);
				

					while (!@fgetcsv($data)) {
						
						for ($c=0; $c < $num; $c++) {
							$result = $data;
							$str = implode(",", $result);
							$slice = explode(",", $str);
						
							$col1 = $slice[0];
							$col2 = $slice[1];
							$col3 = $slice[2];
							$col4 = $slice[3];
							$col5 = $slice[4];
							$col6 = $slice[5];
							$col7 = $slice[6];
							$col8 = $slice[7];
							$col9 = $slice[8];
					
					 
						//$arPaymentObj->insertHeader($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8);
						// SQL Query to insert data into DataBase
						//$query = "INSERT INTO csvtbl(name,city) VALUES('".$col1."','".$col2."')";
						$uploaderObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9);
						//$s=mysql_query($query,$connect);
						
					}
				}
			}
			//echo "alert('File data successfully imported to database!!')";
			echo "$().toastmessage('showSuccessToast','File data successfully imported to database!');";
			//mssql_close($connect);
		exit();
	break;
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
		
    </head>
    <body>
        <div>
        
    <hr/>
        
        <?php
            $uploader=new PhpUploader();
            
            $uploader->MultipleFilesUpload=true;

            $uploader->InsertText="Select multiple files (Max 1000M)";
            
            $uploader->MaxSizeKB=1024000;
			
            $uploader->AllowedFileExtensions="*.csv";

            $uploader->SaveDirectory="savefiles";
            
            $uploader->FlashUploadMode="Partial";
            
            $uploader->Render();
            
        ?>
        
        </div>
            
        <script type='text/javascript'>
        function CuteWebUI_AjaxUploader_OnTaskComplete(task){
            var div=document.createElement("DIV");
            var link=document.createElement("A");
            link.setAttribute("href","savefiles/"+task.FileName);
            link.innerHTML="You have uploaded file : savefiles/"+task.FileName;
            link.target="_blank";
            div.appendChild(link);
            document.body.appendChild(div);
			
			$.ajax({
				url: 'uploader.php',
				type: 'POST',
				data: 'action=import&fileName='+task.FileName,
				success: function(data){
					eval(data);
				}	
			});
		}
        </script>
        
    </body>
</html>