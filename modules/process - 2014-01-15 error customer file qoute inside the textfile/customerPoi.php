<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$stsServer = array(
"Name" =>"STS Server",
"Host" =>"192.168.200.226",
"User" =>"sa",
"Pass" =>"",
"Database" =>"STS");

$cashierListServer = array(
"Name" =>"Cashier List Server",
"Host" =>"192.168.200.103",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"CashierList");

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
"Path" =>"/fromho/Others/POINTS FOR KIOSK/"
);

// connect to ftp server1
$conn1 = ftp_connect($server1['Host']);

// open a session to an external ftp site
$login1 = ftp_login ($conn1, $server1['User'], $server1['Pass']);

ftp_pasv($conn1, true);

//$generateCsvObj = new generateCsvObj();

switch($_POST['action']){
	
	case 'generateCustomerPoi':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
		$file_desti = "../../exported_file/"; // File destination
		//$file_desti_arc_server = "\\\\192.168.200.133\pg_src_data\\"; // ARC Server
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".PTS";
		$fileExtComp = ".ZIP";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("Ymd", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("mdy", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "HO".$serverCurrentDateMinOne.$fileExt;
			$filenameComp = "HO".$serverCurrentDateMinOne.$fileExtComp;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate - 1", time()));
			
			$sqlExecCreateCustomerPoints = "
			Exec CreateCustomerPoints_mydel '$tranDate'
			";
		
			if(mssql_query($sqlExecCreateCustomerPoints)){
			
				$sqlCustPoi= "
				select cCardNumber,cCustFullName,iPoints,dLastTranDate,dLastPurchaseDate,iLastPointsEarned from TBLCustPoints 
				";
				$arrSqlCustPoi = mssql_query($sqlCustPoi);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrSqlCustPoi); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					while($myrow=mssql_fetch_array($arrSqlCustPoi)){
					
						$xcontentx .= trim($myrow['cCardNumber'])."°";
						$xcontentx .= trim($myrow['cCustFullName'])."°";
						$xcontentx .= trim($myrow['iPoints'])."°";
						$xcontentx .= trim($myrow['dLastTranDate'])."°";
						$xcontentx .= trim($myrow['dLastPurchaseDate'])."°";
						$xcontentx .= trim($myrow['iLastPointsEarned']);
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					
					// zip file
					$zip = new ZipArchive;
					$res = $zip->open($file_desti.$filenameComp, ZipArchive::CREATE);
					if ($res === TRUE) {
						
						if($zip->addFile($file_desti.$filename, $filename)){
							$zip->close();
							if(file_exists($file_desti.$filenameComp)){
								ftp_put($conn1, $server1['Path'].$filenameComp, $file_desti.$filenameComp, FTP_ASCII);  // upload the file
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
					
					// end zip file
					
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-custPoi-error').append('Message: Customer  Points Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
			}
		}
		else{
			echo "$('.divLogs-custPoi-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
}
?>
		
<html>

	<head>
		
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
					url: 'customerPoi.php',
					type: 'POST',
					data: 'action=generateCustomerPoi',
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
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateCsv();" id="btnGenerateCsv" value="Generate Zip">
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