<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$pgchequeServer = array(
"Name" =>"WIN-PGCHEQUE Server",
"Host" =>"192.168.200.210",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"dbCSV");

$cimsServer = array(
"Name" =>"CIMS Server",
"Host" =>"192.168.200.231",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"ExclusivesHO");

$server1 = array(
"Name" =>"192.168.200.200",
"Host" =>"192.168.200.200",
"User" =>"jdwade@puregold.local",
"Pass" =>"info@2013",
"Path" =>"/fromho/Others/POINTS%20FOR%20KIOSK/"
);

//My page refresher
$page = $_SERVER['PHP_SELF'];
$sec = "3600";

// connect to ftp server1
$conn1 = ftp_connect($server1['Host']);

// open a session to an external ftp site
$login1 = ftp_login ($conn1, $server1['User'], $server1['Pass']);

ftp_pasv($conn1, true);

//$generateCsvObj = new generateCsvObj();

switch($_POST['action']){

	case 'importPerksPoi':
	
			//$pgchequeServerCon = mssql_connect($pgchequeServer['Host'],$pgchequeServer['User'],$pgchequeServer['Pass']);
			//$pgchequeServerDb = mssql_select_db($pgchequeServer['Database']);	
			
			$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
			$cimsServerDb = mssql_select_db($cimsServer['Database']);	
			
			//database connection details
			//$connect = mssql_connect('localhost','root','');
			
			//if (!$connect) {
			//die('Could not connect to MySQL: ' . mysql_error());
			//}	
			
			//your database name
			//$cid = mssql_select_db('dbccdcms',$connect);
			
			$directory="C:/wamp/www/GenerateCSV/perks_data/";
			$arch_directory="C:/wamp/www/GenerateCSV/perks_archive/";
			
			
			// create a handler to the directory
			//$dirhandler = opendir($directory);
			// read all the files from directory
			$nofiles=0;
			$checkEmpty  = (count(glob($directory.'*')) === 0) ? 'Empty' : 'Not empty';

			if ($checkEmpty == "Empty"){
				//echo "$().toastmessage('showWarningToast','<b>Folder is empty!</b>');";
				echo "0";
				exit();
			}else{
				if ($dirhandler = opendir($directory)) {
                    $sql_delete_tblTnapPerksPoints = "
                    truncate table  tblTnapPerksPoints
                    ";
                    $sql_exec_delete_tblTnapPerksPoints = mssql_query($sql_delete_tblTnapPerksPoints);
					while ($file = readdir($dirhandler)) {
						$file_ext = explode('.',$file);
						$max_val = count($file_ext);
						$file_ext = $file_ext[($max_val-1)];
						$ermsg = "";
	
						if($file_ext == "csv" || $file_ext == "CSV"){
							
							$csv_file = $directory.$file;
							if (($handle = fopen($csv_file, "r")) !== FALSE) {
								//$perksObj->clearTBLCustPointsPerks();
                                /*
								$sql_delete_tblTnapPerksPoints = "
								delete from  tblTnapPerksPoints where LEN(cCardNumber) = 16
								";
								$sql_exec_delete_tblTnapPerksPoints = mssql_query($sql_delete_tblTnapPerksPoints);
                                */
								
								fgetcsv($handle);//Adding this line will skip the reading of th first line from the csv file and the reading process will begin from the second line onwards
								while (($data = fgetcsv($handle, 10000000, ",")) !== FALSE) {		
								
									$num = count($data);
									//echo "<p> $num fields in line $row: <br /></p>\n";
									$row++;
									for ($c=0; $c < 4; $c++) {
										//echo $data[$c] . "\n";
										$col1 = "'".trim(str_replace("'","''",$data[0]))."'";
										$col2 = "'".trim(str_replace("'","''",$data[1]))."'";
										$col3 = "'".trim(str_replace("'","''",$data[2]))."'";
										$col4 = $data[3];
										$col5 = "'".trim(str_replace("'","''",$data[4]))."'";
										$col6 = "'".trim(str_replace("'","''",date('Y-m-d h:i:s',strtotime($data[5]))))."'";	
										//$col7 = "'".trim(str_replace("'","''",date('m/d/Y',strtotime($data[6]))))."'";	
										//date("Y-m-d h:i:s", strtotime($myrow['dLastPurchaseDate']));
									}
									//$perksObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7);
									
									$sql_insertTblTnapPerksPoints = "
									INSERT INTO tblTnapPerksPoints(cCardNumber,cCustFullName,iPoints,dLastTranDate,dLastPurchaseDate,iLastPointsEarned,lastName) 
									VALUES({$col1},{$col2},{$col4},{$col6},NULL,NULL,{$col5})
									";
									$sql_exec_sql_insertTblTnapPerksPoints = mssql_query($sql_insertTblTnapPerksPoints);
								}	
								
								$sql_loadedRec = "
								select  count(*) as loaded
								from         tblTnapPerksPoints 
								";
                                //where LEN(cCardNumber) = 16
								
								if($sql_exec_sql_loadedRec = mssql_query($sql_loadedRec)){
									/*echo "
									$().toastmessage('showToast', {
									text     : '<b>Filename: </b> ".$file." data successfully imported to database! <br>"
									.'<font color="#00FF00"><b>Uploaded Rec.:</b></font> '.$loaded['loaded']."',
									sticky   : true,
									position : 'middle-center',
									type     : 'success',
									close    : function () {console.log('toast is closed ...');
										document.location.reload();}
									});
									";
									*/
									$sql_loadedRecCount = mssql_fetch_assoc($sql_exec_sql_loadedRec);
									
									echo "$('.divLogs-perksPoi').append('Message: Perks Points Data successfully imported to database!'+'<br/>').css('color','white');";
									echo "$('.divLogs-perksPoi').append('Message: Uploaded ".$sql_loadedRecCount['loaded']." record(s)' +'<br/>').css('color','white');";
								}
							}
							copy($directory.$file, $arch_directory.$file);
						}
						//echo "$().toastmessage('showSuccessToast','File data successfully imported to database!');";
					}
					
					//$perksObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9);
					
					//echo "alert('File data successfully imported to database!!')";
					
					//mssql_close($connect);
				}
				closedir($handle);
				
				$dir = 'C:/wamp/www/GenerateCSV/perks_data/';
				foreach(glob($dir.'*.csv*') as $v){
					unlink($v);
				}
				foreach(glob($dir.'*.CSV*') as $v){
					unlink($v);
				}
				/*if ($checkEmpty == "Not empty"){
				
				}*/
			}
		exit();
	break;
    
    case 'importTnapPoi':
    
            //$pgchequeServerCon = mssql_connect($pgchequeServer['Host'],$pgchequeServer['User'],$pgchequeServer['Pass']);
            //$pgchequeServerDb = mssql_select_db($pgchequeServer['Database']);    
            
            $cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
            $cimsServerDb = mssql_select_db($cimsServer['Database']);    
            
            //database connection details
            //$connect = mssql_connect('localhost','root','');
            
            //if (!$connect) {
            //die('Could not connect to MySQL: ' . mysql_error());
            //}    
            
            //your database name
            //$cid = mssql_select_db('dbccdcms',$connect);
            
            $directory="C:/wamp/www/GenerateCSV/perks_data/";
            $arch_directory="C:/wamp/www/GenerateCSV/perks_archive/";
            
            
            // create a handler to the directory
            //$dirhandler = opendir($directory);
            // read all the files from directory
            $nofiles=0;
            $checkEmpty  = (count(glob($directory.'*')) === 0) ? 'Empty' : 'Not empty';

            if ($checkEmpty == "Empty"){
                //echo "$().toastmessage('showWarningToast','<b>Folder is empty!</b>');";
                echo "0";
                exit();
            }else{
                if ($dirhandler = opendir($directory)) {
                    while ($file = readdir($dirhandler)) {
                        $file_ext = explode('.',$file);
                        $max_val = count($file_ext);
                        $file_ext = $file_ext[($max_val-1)];
                        $ermsg = "";
    
                        if($file_ext == "csv" || $file_ext == "CSV"){
                            
                            $csv_file = $directory.$file;
                            if (($handle = fopen($csv_file, "r")) !== FALSE) {
                                //$perksObj->clearTBLCustPointsPerks();
                                $sql_delete_tblTnapPerksPoints = "
                                delete from  tblTnapPerksPoints where LEN(cCardNumber) = 16
                                ";
                                $sql_exec_delete_tblTnapPerksPoints = mssql_query($sql_delete_tblTnapPerksPoints);
                                
                                fgetcsv($handle);//Adding this line will skip the reading of th first line from the csv file and the reading process will begin from the second line onwards
                                while (($data = fgetcsv($handle, 10000000, ",")) !== FALSE) {        
                                
                                    $num = count($data);
                                    //echo "<p> $num fields in line $row: <br /></p>\n";
                                    $row++;
                                    for ($c=0; $c < 4; $c++) {
                                        //echo $data[$c] . "\n";
                                        $col1 = "'".trim(str_replace("'","''",$data[0]))."'";
                                        $col2 = "'".trim(str_replace("'","''",$data[1]))."'";
                                        $col3 = "'".trim(str_replace("'","''",$data[2]))."'";
                                        $col4 = $data[3];
                                        $col5 = "'".trim(str_replace("'","''",$data[4]))."'";
                                        $col6 = "'".trim(str_replace("'","''",date('Y-m-d h:i:s',strtotime($data[5]))))."'";    
                                        //$col7 = "'".trim(str_replace("'","''",date('m/d/Y',strtotime($data[6]))))."'";    
                                        //date("Y-m-d h:i:s", strtotime($myrow['dLastPurchaseDate']));
                                    }
                                    //$perksObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7);
                                    
                                    $sql_insertTblTnapPerksPoints = "
                                    INSERT INTO tblTnapPerksPoints(cCardNumber,cCustFullName,iPoints,dLastTranDate,dLastPurchaseDate,iLastPointsEarned,lastName) 
                                    VALUES({$col1},{$col2},{$col4},{$col6},NULL,NULL,{$col5})
                                    ";
                                    $sql_exec_sql_insertTblTnapPerksPoints = mssql_query($sql_insertTblTnapPerksPoints);
                                }    
                                
                                $sql_loadedRec = "
                                select  count(*) as loaded
                                from         tblTnapPerksPoints
                                where LEN(cCardNumber) = 16
                                ";
                                
                                if($sql_exec_sql_loadedRec = mssql_query($sql_loadedRec)){
                                    /*echo "
                                    $().toastmessage('showToast', {
                                    text     : '<b>Filename: </b> ".$file." data successfully imported to database! <br>"
                                    .'<font color="#00FF00"><b>Uploaded Rec.:</b></font> '.$loaded['loaded']."',
                                    sticky   : true,
                                    position : 'middle-center',
                                    type     : 'success',
                                    close    : function () {console.log('toast is closed ...');
                                        document.location.reload();}
                                    });
                                    ";
                                    */
                                    $sql_loadedRecCount = mssql_fetch_assoc($sql_exec_sql_loadedRec);
                                    
                                    echo "$('.divLogs-perksPoi').append('Message: Perks Points Data successfully imported to database!'+'<br/>').css('color','white');";
                                    echo "$('.divLogs-perksPoi').append('Message: Uploaded ".$sql_loadedRecCount['loaded']." record(s)' +'<br/>').css('color','white');";
                                }
                            }
                            copy($directory.$file, $arch_directory.$file);
                        }
                        //echo "$().toastmessage('showSuccessToast','File data successfully imported to database!');";
                    }
                    
                    //$perksObj->inserttblTxtfileTemp($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9);
                    
                    //echo "alert('File data successfully imported to database!!')";
                    
                    //mssql_close($connect);
                }
                closedir($handle);
                
                $dir = 'C:/wamp/www/GenerateCSV/perks_data/';
                foreach(glob($dir.'*.csv*') as $v){
                    unlink($v);
                }
                foreach(glob($dir.'*.CSV*') as $v){
                    unlink($v);
                }
                /*if ($checkEmpty == "Not empty"){
                
                }*/
            }
        exit();
    break;
	
	case 'importTnapPoi':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".PTS";
		$fileExtComp = ".ZIP";
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate - 1", time()));
			
				$sqlCustPoi= "
				select cCardNumber,cCustFullName,iPoints,dLastTranDate,lastname from v_CustPoints_Website 
				";
				$arrSqlCustPoi = mssql_query($sqlCustPoi);

				$strlength = strlen($arrSqlCustPoi); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					$sql_delete_tblTnapPerksPoints = "
					delete from  tblTnapPerksPoints where LEN(cCardNumber) = 12
					";
					$sql_exec_delete_tblTnapPerksPoints = mssql_query($sql_delete_tblTnapPerksPoints);
					
								
					$sql_insertTblTnapPerksPoints = "
					INSERT INTO tblTnapPerksPoints(cCardNumber,cCustFullName,iPoints,dLastTranDate,dLastPurchaseDate,iLastPointsEarned,lastName)
					select cCardNumber,cCustFullName,iPoints,dLastTranDate,NULL,NULL,lastname from v_CustPoints_Website 
					";	
					if($sql_exec_sql_insertTblTnapPerksPoints = mssql_query($sql_insertTblTnapPerksPoints)){
						$sql_loadedRec = "
						select count(*) as loaded from tblTnapPerksPoints
						where LEN(cCardNumber) = 12
						";
						$sql_exec_sql_loadedRec = mssql_query($sql_loadedRec);
						$sql_loadedRecCount = mssql_fetch_assoc($sql_exec_sql_loadedRec);
						
						echo "$('.divLogs-tnapPoi').append('Message: TNAP Points Data successfully imported to database!'+'<br/>').css('color','white');";
						echo "$('.divLogs-tnapPoi').append('Message: Uploaded ".$sql_loadedRecCount['loaded']." record(s)' +'<br/>').css('color','white');";
					}
					else{
						echo "$('.divLogs-tnapPoi-error').append('Message: TNAP Points and Perks Points error inserting data'+'<br/>').css('color','red');";
					}
					
				}
				else{
					echo "$('.divLogs-tnapPoi-error').append('Message: TNAP Points and Perks Points Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
		}
		else{
			echo "$('.divLogs-custPoi-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
	case 'generateTnapAndPerksPoi':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
		$file_desti = "../../exported_file/"; // File destination
		//$file_desti_arc_server = "\\\\192.168.200.133\pg_src_data\\"; // ARC Server
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".txt";
		//$fileExtComp = ".ZIP";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("Ymd", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("Ymd", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = $serverCurrentDateMinOne.$fileExt;
			//$filenameComp = $serverCurrentDateMinOne.$fileExtComp;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate - 1", time()));
			
				$sqlCustPoi= "
				select cCardNumber,cCustFullName,iPoints,dLastTranDate,lastName from tblTnapPerksPoints 
				";
				$arrSqlCustPoi = mssql_query($sqlCustPoi);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrSqlCustPoi); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					while($myrow=mssql_fetch_array($arrSqlCustPoi)){
					
						$xcontentx .= trim($myrow['cCardNumber'])."|";
						$xcontentx .= trim($myrow['cCustFullName'])."|";
						$xcontentx .= trim($myrow['iPoints'])."|";
						$lastTranDate = date("Y-m-d h:i:s", strtotime($myrow['dLastTranDate']));
						$xcontentx .= trim($lastTranDate)."|";
						//$lastPurchaseDate = date("Y-m-d h:i:s", strtotime($myrow['dLastPurchaseDate']));
						//$xcontentx .= trim($lastPurchaseDate)."º";
						$xcontentx .= trim($myrow['lastName']);
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);					
					fclose($create);
					
					echo "$('.divLogs-tnapPoi').append('Message: TNAP Points and Perks Points Textfile Created!'+'<br/>').css('color','white');";
					
					/*
					// zip file
					$zip = new ZipArchive;
					$res = $zip->open($file_desti.$filenameComp, ZipArchive::CREATE);
					if ($res === TRUE) {
						
						if($zip->addFile($file_desti.$filename, $filename)){
							$zip->close();
							if(file_exists($file_desti.$filenameComp)){
								//ftp_put($conn1, $server1['Path'].$filenameComp, $file_desti.$filenameComp, FTP_ASCII);  // upload the file
								ftp_put($conn1, $server1['Path'].$filenameComp, $file_desti.$filenameComp, FTP_BINARY);  // upload the file
								echo "$('.divLogs-custPoi').append('Message: Customer  Points Created'+'<br/>').css('color','white');";
								
							}else{
								echo "$('.divLogs-custPoi-ftp').append('Message: Upload to FTP failed'+'<br/>').css('color','red');";
							}
						}else{
							echo "$('.divLogs-custPoi-ftp').append('Message: Error in compressing file to zip file'+'<br/>').css('color','red');";
						}
					} else {
						
						echo "$('.divLogs-custPoi-zip').append('Message: Error in compressing file to zip file'+'<br/>').css('color','red');";
					}
					*/
					
					// end zip file
					
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-tnapPerksPoi-error').append('Message: TNAP Points and Perks Points Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
		}
		else{
			echo "$('.divLogs-tnapPerksPoi-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
}
?>
		
<html>

	<head>
	
	<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	
		
		<script src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
        
        <link href="../../includes/showLoading/css/showLoading.css" rel="stylesheet" media="screen" /> 
        <script type="text/javascript" src="../../includes/showLoading/js/jquery.showLoading.js"></script>
		
		<script type="text/javascript">
		
			function generateCsv(){
				$.ajax({
					url: 'tnapPerksPoi.php',
					type: 'POST',
					data: 'action=importPerksPoi',
					beforeSend: function() {
						 $('#btnGenerateCsv').val('Running...');
					},
					success: function(data){
						if(data == 0){
							$().toastmessage('showToast', {
								text: 'Folder is empty!',
								sticky: false,
								position: 'middle-center',
								type: 'warning',
								closeText: '',
								close: function () 
								{
								console.log("toast is closed ...");
								jQuery('#activity_pane').hideLoading();
								}
							});
						}else{
							$('#btnGenerateCsv').val('Running...');
							$('#btnGenerateCsv').attr('disabled','disabled');
							$(".btn:hover").css({
								"text-decoration": "none",
								"background": "#3498db"
							});
							eval(data);
							<!-- importTnapPoi(); -->
                            generateTnapAndPerksPoi();
						}
					}		
				})	
			}
			
			function importTnapPoi(){
				$.ajax({
					url: 'tnapPerksPoi.php',
					type: 'POST',
					data: 'action=importTnapPoi',
					beforeSend: function() {
						$('#btnGenerateCsv').val('Running...');
					},
					success: function(data){
						$('#btnGenerateCsv').attr('disabled','disabled');
						$(".btn:hover").css({
							"text-decoration": "none",
							"background": "#3498db"
						});
						eval(data);
						generateTnapAndPerksPoi();
					}		
				})	
			}
			
			function generateTnapAndPerksPoi(){
				$.ajax({
					url: 'tnapPerksPoi.php',
					type: 'POST',
					data: 'action=generateTnapAndPerksPoi',
					beforeSend: function() {
						$('#btnGenerateCsv').val('Running...');
					},
					success: function(data){
						$('#btnGenerateCsv').attr('disabled','disabled');
						$(".btn:hover").css({
							"text-decoration": "none",
							"background": "#3498db"
						});
						eval(data);
						$('#btnGenerateCsv').val('Done!');
					}		
				})	
			}
		
		</script>
	
		<style type="text/css">
		<!--

		.btn {
		background: #3498db;
		background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
		background-image: -moz-linear-gradient(top, #3498db, #2980b9);
		background-image: -ms-linear-gradient(top, #3498db, #2980b9);
		background-image: -o-linear-gradient(top, #3498db, #2980b9);
		background-image: linear-gradient(to bottom, #3498db, #2980b9);
		-webkit-border-radius: 60;
		-moz-border-radius: 60;
		border-radius: 60px;
		font-family: Courier New;
		color: #ffffff;
		font-size: 38px;
		padding: 10px 20px 10px 20px;
		text-decoration: none;
		}

		.btn:hover {
		background: #3cb0fd;
		text-decoration: none;
		}
		
		.divLogs{
		height: 50%;
		width: 70%;
		overflow:auto;
		border: 1px solid #ddd;
		border-radius: 10px;
		background-color:black;
		margin-left: auto;
		margin-right: auto;
		}
		
		#dispLogs{
		color: white;
		font-size: 14px;
		}
		-->
		</style>   

    </head>
	
    	<body>
		
			<div id="activity_pane">
			
			<br />
            
				<form class="form-horizontal">
				
					<div class="dvContainer">
						<center>
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateTnapAndPerksPoi();" id="btnGenerateCsv" value="TNAP Points & Perks Points">
						</center>
						
						<br>
						
						<div class="divLogs">
							<div class="divLogs-cims">
							<?
							// check open
							if (!mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass'])) {
								echo "<font color='red'><b>Connection status:</b>" .$cimsServer ['Name'] . " connect failed.</font><br>";
							}
							else {
								echo "<font color='lightblue'><b>Connection status:</b> " . $cimsServer['Name'] . " connected.</font><br>";
							}
							?>
							</div>
							<div class="divLogs-ftpcon">
							<?
							// check open
							if ((!$conn1) || (!$login1)) {
								echo "<font color='red'><b>Connection status:</b> FTP server " . $server1['Name'] . " connect failed.</font><br>";
							}
							else {
								echo "<font color='lightblue'><b>Connection status:</b> FTP server " . $server1['Name'] . " connected.</font><br>";
							}
							?>
							</div>
							<div class="divLogs-perksPoi">
							</div>
							<div class="divLogs-perksPoi-error">
							</div>
							<div class="divLogs-tnapPoi">
							</div>
							<div class="divLogs-tnapPoi-error">
							</div>
							</div>
							<div class="divLogs-tnapPerksPoi">
							</div>
							<div class="divLogs-tnapPerksPoi-error">
							</div>
							
							<div class="divLogs-custPoi">
							</div>
							<div class="divLogs-custPoi-error">
							</div>
							<div class="divLogs-custPoi-zip">
							</div>
							<div class="divLogs-custPoi-ftp">
							</div>
							<div class="divLogs-ejCor">
							</div>
							<div class="divLogs-ejCor-error">
							</div>
							
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>