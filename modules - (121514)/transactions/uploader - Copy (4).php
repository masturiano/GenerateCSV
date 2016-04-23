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
			
			$row = 1;
			if (($handle = fopen($csv_file, "r")) !== FALSE) {
				$uploaderObj->cleartblTxtfileTemp();
				$uploaderObj->cleartblTxtfileTempDup();
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++;
					for ($c=0; $c < $num; $c++) {
						//echo $data[$c] . "\n";
						$col1 = $data[0];
						$col2 = $data[1];
						$col3 = $data[2];
						$col4 = $data[3];
						$col5 = $data[4];
						$col6 = $data[5];
						$col7 = $data[6];
						$col8 = $data[7];
						$col9 = $data[8];
					}
					if($uploaderObj->checkIfDuplicate($col1,$col2,$col3,$col4) == 0){
						$uploaderObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9);
						echo "\n";
					}
					if($uploaderObj->checkIfDuplicate($col1,$col2,$col3,$col4) != 0){
						$uploaderObj->inserttblTxtfileTempDup($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9);
						echo "\n";
					}
				}
				if($uploaderObj->checkIfDuplicate($col1,$col2,$col3,$col4) == 1){
					echo "$().toastmessage('showErrorToast','<b>Filename: </b> ".$_POST['fileName']."<br>Duplicate invoices no data imported to database!');";
				}
				if($uploaderObj->checkIfDuplicate($col1,$col2,$col3,$col4) == 0){
					$uploaderObj->insertHeader($_POST['fileName']);
					$uploaderObj->insertDetails();
					echo "$().toastmessage('showSuccessToast','<b>Filename: </b> ".$_POST['fileName']." data successfully imported to database!');";
					$dispDuplicate = $uploaderObj->displayDuplicate($col1,$col2,$col3,$col4);
					foreach($dispDuplicate as $val){ 
						echo "$().toastmessage('showErrorToast','<b>Duplicate!</b> <br>"
						.'<b>Invoice #:</b>  '.$val['invNum'].'<br>'
						.'<b>Store Code:</b>  '.$val['store'].'<br>'
						.'<b>Trans Date:</b>  '.date('m/d/Y',strtotime($val['transDate'])).'<br>'
						.'<b>Cust #:</b>  '.$val['custNum']."');";
					}
					//unlink(trim($csv_file));
					//.$val['invNum'].store,transDate,custNum,invNum
				}
				fclose($handle);
				//echo "$().toastmessage('showSuccessToast','File data successfully imported to database!');";
			}
			//$uploaderObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9);
			
			//echo "alert('File data successfully imported to database!!')";
			
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